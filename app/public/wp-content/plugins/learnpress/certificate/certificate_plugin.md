Below is a clean way to build a **WordPress certificate plugin** that **listens to LearnPress quiz completion**, checks for **≥ 80%**, then **issues + displays (popup) a certificate automatically**.

---

## 1) Trigger point in LearnPress (the “event” you hook)

LearnPress exposes an action you can hook when a quiz is completed:

* `do_action( 'learn-press/quiz-completed', $quiz_id, $course_id, $user_id, $result );` ([LearnPress][1])

That’s the reliable place to:

1. compute score %
2. decide pass/fail
3. create an “issued certificate” record
4. set a “show popup” flag for the next page render

---

## 2) Data model (don’t store this in postmeta only)

Use a small custom table for issued certs (fast lookups, clean uniqueness rules, easy admin listing):

**`wp_lp_auto_certs`**

* `id` (PK)
* `code` (unique public verifier, e.g. UUID or short hash)
* `user_id`
* `course_id`
* `quiz_id`
* `percent`
* `issued_at`
* `revoked_at` (nullable)
* `pdf_path` (nullable) or regenerate on demand

Uniqueness rule:

* **one cert per (user_id, course_id)** unless you explicitly want multiple attempts.

---

## 3) “Popup automatically” UX (pragmatic approach)

You can’t “popup” *at the exact moment of PHP hook execution* because that’s server-side, but you can:

1. On `learn-press/quiz-completed`, store a short-lived flag:

   * `set_transient( "lp_cert_popup_{$user_id}", $cert_code, 5 * MINUTE_IN_SECONDS );`

2. On the next frontend render (quiz results page), enqueue a tiny JS that:

   * calls an authenticated AJAX endpoint `lp_cert_should_popup`
   * if it returns a cert code, open a modal with **View / Download** CTA.

This is stable even with caching/CDNs (because the AJAX call is user-specific).

---

## 4) Minimal plugin skeleton (works as a base)

### `learnpress-auto-certificates.php`

```php
<?php
/**
 * Plugin Name: LearnPress Auto Certificates
 * Description: Issue certificates automatically when a LearnPress quiz score >= threshold.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) exit;

final class LP_Auto_Certificates {
    const DB_VERSION = '1.0';
    const TABLE = 'lp_auto_certs';
    const OPT_THRESHOLD = 'lpac_threshold'; // percent integer

    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'activate']);

        // LearnPress hook: quiz completed
        add_action('learn-press/quiz-completed', [$this, 'on_quiz_completed'], 10, 4); // :contentReference[oaicite:1]{index=1}

        // Frontend popup helper
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend']);

        // AJAX: check popup + fetch cert
        add_action('wp_ajax_lpac_should_popup', [$this, 'ajax_should_popup']);
        add_action('wp_ajax_lpac_get_cert', [$this, 'ajax_get_cert']);

        // Optional: public verification endpoint via shortcode
        add_shortcode('lpac_verify', [$this, 'shortcode_verify']);
    }

    public function activate() {
        global $wpdb;

        $table = $wpdb->prefix . self::TABLE;
        $charset = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            code VARCHAR(64) NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            course_id BIGINT UNSIGNED NOT NULL,
            quiz_id BIGINT UNSIGNED NOT NULL,
            percent DECIMAL(5,2) NOT NULL,
            issued_at DATETIME NOT NULL,
            revoked_at DATETIME NULL,
            pdf_path TEXT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY code (code),
            UNIQUE KEY user_course (user_id, course_id)
        ) {$charset};";

        dbDelta($sql);

        add_option('lpac_db_version', self::DB_VERSION);
        add_option(self::OPT_THRESHOLD, 80);
    }

    public function on_quiz_completed($quiz_id, $course_id, $user_id, $result) {
        $threshold = (int) get_option(self::OPT_THRESHOLD, 80);

        $percent = $this->extract_percent($result);

        if ($percent < $threshold) return;

        // Issue once per course per user
        $cert = $this->maybe_issue_certificate($user_id, $course_id, $quiz_id, $percent);
        if (!$cert) return;

        // Tell frontend to popup (5 min window)
        set_transient("lpac_cert_popup_{$user_id}", $cert['code'], 5 * MINUTE_IN_SECONDS);
    }

    private function extract_percent($result): float {
        // LearnPress commonly provides mark_percent (sometimes 0..1). Be defensive.
        if (is_array($result)) {
            if (isset($result['mark_percent'])) {
                $p = (float) $result['mark_percent'];
                return ($p <= 1.0) ? ($p * 100.0) : $p;
            }
            if (isset($result['percent'])) {
                $p = (float) $result['percent'];
                return ($p <= 1.0) ? ($p * 100.0) : $p;
            }
        }
        return 0.0;
    }

    private function maybe_issue_certificate($user_id, $course_id, $quiz_id, $percent) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $existing = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE user_id=%d AND course_id=%d AND revoked_at IS NULL", $user_id, $course_id),
            ARRAY_A
        );
        if ($existing) return $existing;

        $code = wp_generate_password(24, false, false) . '-' . $user_id . '-' . $course_id;

        $ok = $wpdb->insert($table, [
            'code'      => $code,
            'user_id'   => (int) $user_id,
            'course_id' => (int) $course_id,
            'quiz_id'   => (int) $quiz_id,
            'percent'   => (float) $percent,
            'issued_at' => current_time('mysql'),
        ], ['%s','%d','%d','%d','%f','%s']);

        return $ok ? [
            'code' => $code,
            'user_id' => $user_id,
            'course_id' => $course_id,
            'quiz_id' => $quiz_id,
            'percent' => $percent,
        ] : null;
    }

    public function enqueue_frontend() {
        if (!is_user_logged_in()) return;

        wp_register_script(
            'lpac-frontend',
            plugins_url('assets/lpac-frontend.js', __FILE__),
            ['jquery'],
            '0.1.0',
            true
        );

        wp_localize_script('lpac-frontend', 'LPAC', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('lpac_nonce'),
        ]);

        wp_enqueue_script('lpac-frontend');
    }

    public function ajax_should_popup() {
        check_ajax_referer('lpac_nonce', 'nonce');

        $user_id = get_current_user_id();
        $code = get_transient("lpac_cert_popup_{$user_id}");
        if (!$code) wp_send_json_success(['show' => false]);

        // consume it so it doesn't popup forever
        delete_transient("lpac_cert_popup_{$user_id}");

        wp_send_json_success(['show' => true, 'code' => $code]);
    }

    public function ajax_get_cert() {
        check_ajax_referer('lpac_nonce', 'nonce');

        $user_id = get_current_user_id();
        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';

        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE code=%s AND user_id=%d AND revoked_at IS NULL", $code, $user_id),
            ARRAY_A
        );

        if (!$row) wp_send_json_error(['message' => 'Certificate not found.'], 404);

        // TODO: generate PDF or return a secure download URL
        $verify_url = add_query_arg(['lpac' => 'verify', 'code' => $row['code']], home_url('/'));
        wp_send_json_success([
            'code' => $row['code'],
            'percent' => $row['percent'],
            'verifyUrl' => $verify_url,
        ]);
    }

    public function shortcode_verify($atts) {
        $code = isset($_GET['code']) ? sanitize_text_field($_GET['code']) : '';
        if (!$code) return '<p>Missing certificate code.</p>';

        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE code=%s", $code), ARRAY_A);
        if (!$row || !empty($row['revoked_at'])) return '<p>Certificate is invalid or revoked.</p>';

        return sprintf(
            '<div><strong>Valid certificate</strong><br>Course ID: %d<br>User ID: %d<br>Score: %s%%<br>Issued: %s</div>',
            (int) $row['course_id'],
            (int) $row['user_id'],
            esc_html($row['percent']),
            esc_html($row['issued_at'])
        );
    }
}
new LP_Auto_Certificates();
```

### `assets/lpac-frontend.js`

```js
jQuery(function ($) {
  $.post(LPAC.ajaxUrl, { action: "lpac_should_popup", nonce: LPAC.nonce }, function (res) {
    if (!res || !res.success || !res.data.show) return;

    const code = res.data.code;

    // Fetch certificate details
    $.post(LPAC.ajaxUrl, { action: "lpac_get_cert", nonce: LPAC.nonce, code }, function (r2) {
      if (!r2 || !r2.success) return;

      const d = r2.data;

      // Minimal modal (replace with your UI framework)
      const html = `
        <div id="lpac-modal" style="position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99999;display:flex;align-items:center;justify-content:center;">
          <div style="background:#fff;max-width:520px;width:92%;border-radius:16px;padding:18px;">
            <h3 style="margin:0 0 8px 0;">Certificate unlocked</h3>
            <p style="margin:0 0 14px 0;">Score: ${d.percent}%</p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
              <a href="${d.verifyUrl}" style="padding:10px 12px;border:1px solid #ddd;border-radius:10px;text-decoration:none;">View / Verify</a>
              <button id="lpac-close" style="padding:10px 12px;border:1px solid #ddd;border-radius:10px;background:#f7f7f7;">Close</button>
            </div>
          </div>
        </div>
      `;
      $("body").append(html);
      $("#lpac-close").on("click", function () { $("#lpac-modal").remove(); });
      $("#lpac-modal").on("click", function (e) { if (e.target.id === "lpac-modal") $("#lpac-modal").remove(); });
    });
  });
});
```

---

## 5) Next upgrades (the “Apple-grade” version)

If you want this to feel premium and bulletproof:

* **Template system**: certificate templates with variable placeholders (`{name}`, `{course}`, `{date}`, `{score}`, `{code}`)
* **PDF generation**: server-side render (Dompdf/mPDF) + cache file per cert
* **QR code** on certificate linking to `/verify?code=...`
* **Admin screens**:

  * list issued certs, revoke, reissue
  * set threshold globally + optionally override per course/quiz
* **Course logic**:

  * “any quiz in course” vs “final quiz only”
  * only issue if the user is enrolled/finished course (if that matters)
* **Security**:

  * verify endpoints rate-limited
  * avoid exposing user_id; use only `code`
  * strong sanitization everywhere (LearnPress has had REST-related security issues in the past; keep your surface area tight)

Also: LearnPress has paid certificate add-ons—useful as a benchmark, but your custom approach gives you full control over rules and UX.

---

## Source URLs (requested, not inline)

```text
https://docs.thimpress.com/learnpress-developer-documentation/hooks-and-filters/learnpress-complete-hooks-and-filters-reference/
https://plugintests.com/plugins/wporg/learnpress/4.2.7.7/structure/functions
```

[1]: https://learnpresslms.com/docs/learnpress-developer-documentation/hooks-and-filters-actions-filters/ "LearnPress Developer Documentation - Hooks and Filters (Actions & Filters)"
