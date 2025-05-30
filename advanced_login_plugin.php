<?php
/**
 * Plugin Name: پلاگین ورودینو برای ورود و ثبت نام پیشرفته
 * Plugin URI: https://pouya-wp.ir
 * Description: پلاگین کاملی برای ورود، ثبت نام، احراز هویت پیامکی و Elementor widget
 * Version: 1.0.2
 * Author: پویا وردپرس
 * Text Domain: ورودینو
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('ADVANCED_LOGIN_URL', plugin_dir_url(__FILE__));
define('ADVANCED_LOGIN_PATH', plugin_dir_path(__FILE__));
define('ADVANCED_LOGIN_VERSION', '1.0.2');

// ثبت اسکریپت و استایل فقط با register (اصلاح شده)
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script(
        'advanced-login-widget-js',
        ADVANCED_LOGIN_URL . 'assets/advanced-login.js',
        [ 'jquery' ],
        ADVANCED_LOGIN_VERSION,
        true
    );
    wp_register_style(
        'advanced-login-widget-css',
        ADVANCED_LOGIN_URL . 'assets/advanced_login.css',
        [],
        ADVANCED_LOGIN_VERSION
    );
    wp_localize_script( 'advanced-login-widget-js', 'advanced_login_ajax', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'advanced_login_nonce' )
    ]);
});

// ثبت دسته‌بندی سفارشی المنتور
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category(
        'advanced-login',
        [
            'title' => 'ورودینو',
            'icon'  => 'fa fa-user-circle',
        ]
    );
});

// ثبت ویجت‌ها طبق استاندارد جدید
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    require_once __DIR__ . '/widgets/advanced-login-button-widget.php';
    require_once __DIR__ . '/widgets/advanced-login-form-widget.php';
    require_once __DIR__ . '/widgets/advanced-register-form-widget.php';
    require_once __DIR__ . '/widgets/advanced-user-dashboard-widget.php';

    $widgets_manager->register( new Advanced_Login_Button_Widget() );
    $widgets_manager->register( new Advanced_Login_Form_Widget() );
    $widgets_manager->register( new Advanced_Register_Form_Widget() );
    $widgets_manager->register( new Advanced_User_Dashboard_Widget() );
});

// سایر کدهای پلاگین (ورود، ثبت نام، احراز هویت پیامکی و ...)
// ... این بخش را می‌توانی به فایل‌های جدا منتقل کنی یا همینجا نگه داری ...

class AdvancedLoginPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_advanced_login', array($this, 'ajax_login'));
        add_action('wp_ajax_nopriv_advanced_login', array($this, 'ajax_login'));
        add_action('wp_ajax_advanced_register', array($this, 'ajax_register'));
        add_action('wp_ajax_nopriv_advanced_register', array($this, 'ajax_register'));
        add_action('wp_ajax_send_sms_code', array($this, 'send_sms_verification'));
        add_action('wp_ajax_nopriv_send_sms_code', array($this, 'send_sms_verification'));
        add_action('wp_ajax_verify_sms_code', array($this, 'verify_sms_code'));
        add_action('wp_ajax_nopriv_verify_sms_code', array($this, 'verify_sms_code'));
        add_action('wp_ajax_reset_password', array($this, 'ajax_reset_password'));
        add_action('wp_ajax_nopriv_reset_password', array($this, 'ajax_reset_password'));
        add_action('wp_ajax_update_memoji', array($this, 'update_user_memoji'));
        add_shortcode('advanced_login_button', array($this, 'login_button_shortcode'));
        add_shortcode('advanced_login_form', array($this, 'login_form_shortcode'));
        add_action('elementor/widgets/register', array($this, 'load_elementor_widgets'));
        
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init() {
        // ایجاد جداول دیتابیس
        $this->create_tables();
    }

    public function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'advanced_login_sms';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            phone varchar(20) NOT NULL,
            code varchar(6) NOT NULL,
            expires datetime DEFAULT CURRENT_TIMESTAMP,
            verified tinyint(1) DEFAULT 0,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function enqueue_scripts() {
        wp_register_script('advanced-login-widget-js', ADVANCED_LOGIN_URL . 'assets/advanced-login.js', array('jquery'), ADVANCED_LOGIN_VERSION, true);
        wp_register_style('advanced-login-widget-css', ADVANCED_LOGIN_URL . 'assets/advanced-login.css', array(), ADVANCED_LOGIN_VERSION);
        
        wp_localize_script('advanced-login-widget-js', 'advanced_login_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('advanced_login_nonce')
        ));
    }

    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'advanced-login') !== false) {
            wp_enqueue_script('advanced-login-admin', ADVANCED_LOGIN_URL . 'assets/admin.js', array('jquery'), ADVANCED_LOGIN_VERSION, true);
            wp_enqueue_style('advanced-login-admin-css', ADVANCED_LOGIN_URL . 'assets/admin.css', array(), ADVANCED_LOGIN_VERSION);
        }
    }

    public function admin_menu() {
        add_menu_page(
            'تنظیمات ورود پیشرفته',
            'ورود پیشرفته',
            'manage_options',
            'advanced-login-settings',
            array($this, 'admin_page'),
            'dashicons-admin-users',
            30
        );
    }

    public function admin_page() {
        if (isset($_POST['submit'])) {
            update_option('advanced_login_sms_api_key', sanitize_text_field($_POST['sms_api_key']));
            update_option('advanced_login_sms_sender', sanitize_text_field($_POST['sms_sender']));
            update_option('advanced_login_custom_login_page', sanitize_text_field($_POST['custom_login_page']));
            update_option('advanced_login_enable_sms', isset($_POST['enable_sms']) ? 1 : 0);
            update_option('advanced_login_enable_memoji', isset($_POST['enable_memoji']) ? 1 : 0);
            if (isset($_POST['main_color'])) {
                update_option('advanced_login_main_color', sanitize_hex_color($_POST['main_color']));
            }
            echo '<div class="notice notice-success"><p>تنظیمات با موفقیت ذخیره شد!</p></div>';
        }

        $sms_api_key = get_option('advanced_login_sms_api_key', '');
        $sms_sender = get_option('advanced_login_sms_sender', '');
        $custom_login_page = get_option('advanced_login_custom_login_page', '');
        $enable_sms = get_option('advanced_login_enable_sms', 0);
        $enable_memoji = get_option('advanced_login_enable_memoji', 1);
        $main_color = get_option('advanced_login_main_color', '#ff5e3a');
        ?>
        <div class="wrap advanced-login-admin">
            <h1>🚀 تنظیمات ورود و ثبت نام پیشرفته</h1>
            
            <div class="admin-container">
                <form method="post" action="">
                    <div class="settings-grid">
                        <div class="setting-card">
                            <h3>📱 تنظیمات پیامک</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="enable_sms">فعال‌سازی احراز هویت پیامکی</label></th>
                                    <td><input type="checkbox" id="enable_sms" name="enable_sms" <?php checked($enable_sms, 1); ?> /></td>
                                </tr>
                                <tr>
                                    <th><label for="sms_api_key">کلید API پیامک</label></th>
                                    <td><input type="text" id="sms_api_key" name="sms_api_key" value="<?php echo esc_attr($sms_api_key); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><label for="sms_sender">شماره ارسال کننده</label></th>
                                    <td><input type="text" id="sms_sender" name="sms_sender" value="<?php echo esc_attr($sms_sender); ?>" class="regular-text" /></td>
                                </tr>
                            </table>
                        </div>

                        <div class="setting-card">
                            <h3>🎨 تنظیمات طراحی</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="custom_login_page">صفحه ورود سفارشی (ID صفحه)</label></th>
                                    <td>
                                        <input type="number" id="custom_login_page" name="custom_login_page" value="<?php echo esc_attr($custom_login_page); ?>" class="regular-text" />
                                        <p class="description">اگر می‌خواهید از صفحه طراحی شده با Elementor استفاده کنید، ID صفحه را وارد کنید</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="main_color">رنگ اصلی</label></th>
                                    <td>
                                        <input type="color" id="main_color" name="main_color" value="<?php echo esc_attr($main_color); ?>" />
                                        <span class="description">رنگ اصلی دکمه‌ها و گرادینت</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="enable_memoji">فعال‌سازی انتخاب آواتار</label></th>
                                    <td><input type="checkbox" id="enable_memoji" name="enable_memoji" <?php checked($enable_memoji, 1); ?> /></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="setting-card">
                        <h3>📋 شورت کدها</h3>
                        <div class="shortcode-list">
                            <div class="shortcode-item">
                                <code>[advanced_login_button]</code>
                                <span>دکمه ورود/حساب کاربری برای هدر</span>
                            </div>
                            <div class="shortcode-item">
                                <code>[advanced_login_form]</code>
                                <span>فرم ورود و ثبت نام</span>
                            </div>
                        </div>
                    </div>

                    <?php submit_button('ذخیره تنظیمات', 'primary large'); ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function login_button_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'default',
            'text' => 'حساب کاربری'
        ), $atts);

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $memoji = get_user_meta($current_user->ID, 'user_memoji', true);
            $memoji_url = $memoji ? $memoji : ADVANCED_LOGIN_URL . 'assets/default-avatar.png';
            
            return '<div class="advanced-login-user-menu">
                        <button class="user-account-btn logged-in">
                            <img src="' . esc_url($memoji_url) . '" alt="آواتار" class="user-avatar">
                            <span>' . esc_html($current_user->display_name) . '</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="' . admin_url() . '">پیشخوان</a>
                            <a href="#" class="change-memoji">تغییر آواتار</a>
                            <a href="' . wp_logout_url() . '">خروج</a>
                        </div>
                    </div>';
        } else {
            return '<button class="advanced-login-btn" data-action="show-popup">
                        <i class="fas fa-user"></i>
                        <span>' . esc_html($atts['text']) . '</span>
                    </button>';
        }
    }

    public function login_form_shortcode($atts) {
        return $this->get_login_popup_html();
    }

    private function get_login_popup_html() {
        ob_start();
        ?>
        <div id="advanced-login-popup" class="advanced-login-popup">
            <div class="popup-overlay"></div>
            <div class="popup-container">
                <button class="popup-close">&times;</button>
                
                <div class="popup-header">
                    <h2 class="popup-title">خوش آمدید</h2>
                    <div class="form-tabs">
                        <button class="tab-btn active" data-tab="login">ورود</button>
                        <button class="tab-btn" data-tab="register">ثبت نام</button>
                    </div>
                </div>

                <div class="popup-content">
                    <!-- فرم ورود -->
                    <div id="login-form" class="form-container active">
                        <form class="advanced-form">
                            <div class="form-group">
                                <label>نام کاربری یا ایمیل</label>
                                <input type="text" name="username" required>
                                <i class="fas fa-user form-icon"></i>
                            </div>
                            <div class="form-group">
                                <label>رمز عبور</label>
                                <input type="password" name="password" required>
                                <i class="fas fa-lock form-icon"></i>
                                <button type="button" class="password-toggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-options">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remember">
                                    <span class="checkmark"></span>
                                    مرا به خاطر بسپار
                                </label>
                                <a href="#" class="forgot-password">فراموشی رمز عبور؟</a>
                            </div>
                            <button type="submit" class="submit-btn">
                                <span>ورود</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>

                    <!-- فرم ثبت نام -->
                    <div id="register-form" class="form-container">
                        <form class="advanced-form">
                            <div class="form-group">
                                <label>نام کاربری</label>
                                <input type="text" name="username" required>
                                <i class="fas fa-user form-icon"></i>
                            </div>
                            <div class="form-group">
                                <label>ایمیل</label>
                                <input type="email" name="email" required>
                                <i class="fas fa-envelope form-icon"></i>
                            </div>
                            <div class="form-group">
                                <label>شماره موبایل</label>
                                <input type="tel" name="phone" required>
                                <i class="fas fa-phone form-icon"></i>
                                <button type="button" class="verify-phone-btn">تأیید</button>
                            </div>
                            <div class="form-group verification-code" style="display: none;">
                                <label>کد تأیید</label>
                                <input type="text" name="verification_code" maxlength="6">
                                <i class="fas fa-shield-alt form-icon"></i>
                            </div>
                            <div class="form-group">
                                <label>رمز عبور</label>
                                <input type="password" name="password" required>
                                <i class="fas fa-lock form-icon"></i>
                                <div class="password-strength"></div>
                            </div>
                            <div class="form-group">
                                <label>تکرار رمز عبور</label>
                                <input type="password" name="confirm_password" required>
                                <i class="fas fa-lock form-icon"></i>
                            </div>
                            <button type="submit" class="submit-btn">
                                <span>ثبت نام</span>
                                <i class="fas fa-user-plus"></i>
                            </button>
                        </form>
                    </div>

                    <!-- فرم فراموشی رمز -->
                    <div id="forgot-form" class="form-container">
                        <form class="advanced-form">
                            <div class="form-group">
                                <label>ایمیل یا شماره موبایل</label>
                                <input type="text" name="email_or_phone" required>
                                <i class="fas fa-envelope form-icon"></i>
                            </div>
                            <button type="submit" class="submit-btn">
                                <span>ارسال لینک بازیابی</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button type="button" class="back-to-login">بازگشت به ورود</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- انتخاب آواتار -->
        <div id="memoji-selector" class="memoji-popup">
            <div class="popup-overlay"></div>
            <div class="memoji-container">
                <h3>انتخاب آواتار</h3>
                <?php
                $avatars_dir = ADVANCED_LOGIN_PATH . 'assets/avatars/';
                $avatars_url = ADVANCED_LOGIN_URL . 'assets/avatars/';
                $avatar_files = glob($avatars_dir . '*.webp');
                ?>
                <div class="memoji-grid">
                    <?php foreach($avatar_files as $file): $url = $avatars_url . basename($file); ?>
                        <div class="memoji-item" data-url="<?php echo esc_url($url); ?>">
                            <img src="<?php echo esc_url($url); ?>" alt="آواتار" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="memoji-close">بستن</button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function ajax_login() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        $remember = isset($_POST['remember']) ? true : false;

        $user = wp_authenticate($username, $password);

        if (is_wp_error($user)) {
            wp_send_json_error('نام کاربری یا رمز عبور اشتباه است.');
        }

        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, $remember);

        wp_send_json_success(array(
            'message' => 'ورود با موفقیت انجام شد.',
            'redirect' => home_url()
        ));
    }

    public function ajax_register() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $password = sanitize_text_field($_POST['password']);

        // بررسی وجود کاربر
        if (username_exists($username) || email_exists($email)) {
            wp_send_json_error('کاربری با این مشخصات قبلاً ثبت نام کرده است.');
        }

        // ایجاد کاربر جدید
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error('خطا در ایجاد حساب کاربری.');
        }

        // ذخیره شماره موبایل
        update_user_meta($user_id, 'user_phone', $phone);

        // ورود خودکار
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        wp_send_json_success(array(
            'message' => 'ثبت نام با موفقیت انجام شد.',
            'redirect' => home_url()
        ));
    }

    public function send_sms_verification() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        $phone = sanitize_text_field($_POST['phone']);
        $code = sprintf('%06d', mt_rand(100000, 999999));

        global $wpdb;
        $table_name = $wpdb->prefix . 'advanced_login_sms';

        // حذف کدهای قبلی
        $wpdb->delete($table_name, array('phone' => $phone));

        // ذخیره کد جدید
        $wpdb->insert($table_name, array(
            'phone' => $phone,
            'code' => $code,
            'expires' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
        ));

        // ارسال پیامک (اینجا باید API پیامک خود را فراخوانی کنید)
        $this->send_sms($phone, "کد تأیید شما: $code");

        wp_send_json_success('کد تأیید ارسال شد.');
    }

    public function verify_sms_code() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        $phone = sanitize_text_field($_POST['phone']);
        $code = sanitize_text_field($_POST['code']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'advanced_login_sms';

        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE phone = %s AND code = %s AND expires > NOW()",
            $phone, $code
        ));

        if ($result) {
            $wpdb->update($table_name, array('verified' => 1), array('id' => $result->id));
            wp_send_json_success('کد تأیید صحیح است.');
        } else {
            wp_send_json_error('کد تأیید نامعتبر یا منقضی شده است.');
        }
    }

    public function ajax_reset_password() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        $email_or_phone = sanitize_text_field($_POST['email_or_phone']);

        if (is_email($email_or_phone)) {
            $user = get_user_by('email', $email_or_phone);
        } else {
            // جستجو بر اساس شماره موبایل
            $users = get_users(array(
                'meta_key' => 'user_phone',
                'meta_value' => $email_or_phone
            ));
            $user = !empty($users) ? $users[0] : false;
        }

        if (!$user) {
            wp_send_json_error('کاربری با این مشخصات یافت نشد.');
        }

        // ارسال ایمیل بازیابی رمز عبور
        $reset_key = get_password_reset_key($user);
        wp_mail($user->user_email, 'بازیابی رمز عبور', 'لینک بازیابی رمز عبور شما: ' . network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login'));

        wp_send_json_success('لینک بازیابی رمز عبور ارسال شد.');
    }

    public function update_user_memoji() {
        check_ajax_referer('advanced_login_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('لطفاً وارد شوید.');
        }

        $memoji_url = esc_url_raw($_POST['memoji_url']);
        $user_id = get_current_user_id();

        update_user_meta($user_id, 'user_memoji', $memoji_url);

        wp_send_json_success('آواتار با موفقیت تغییر کرد.');
    }

    private function send_sms($phone, $message) {
        $api_key = get_option('advanced_login_sms_api_key');
        $sender = get_option('advanced_login_sms_sender');

        // اینجا کد ارسال پیامک با API مورد نظر قرار می‌گیرد
        // نمونه برای پنل پیامک ایرانی
        /*
        $url = "https://api.sms-panel.com/send";
        $data = array(
            'username' => 'your_username',
            'password' => 'your_password',
            'from' => $sender,
            'to' => $phone,
            'text' => $message
        );

        wp_remote_post($url, array('body' => $data));
        */
    }

    public function load_elementor_widgets() {
        require_once ADVANCED_LOGIN_PATH . 'includes/elementor-widgets.php';
    }
    
    public function activate() {
        $this->create_tables();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    } // پایان کلاس AdvancedLoginPlugin
    
    // راه اندازی پلاگین
    new AdvancedLoginPlugin();
    
    // اضافه کردن HTML پاپ اپ به فوتر
    add_action('wp_footer', function() {
        if (!is_admin()) {
            $plugin = new AdvancedLoginPlugin();
            echo $plugin->login_form_shortcode(array());
        }
    });
    ?>