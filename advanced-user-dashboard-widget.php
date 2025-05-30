<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Utils;

// ویجت داشبورد کاربری
class Advanced_User_Dashboard_Widget extends Widget_Base {
    public function get_name() {
        return 'advanced-user-dashboard';
    }
    public function get_title() {
        return 'داشبورد کاربری';
    }
    public function get_icon() {
        return 'eicon-person';
    }
    public function get_categories() {
        return array('advanced-login');
    }
    public function get_script_depends() {
        return [ 'advanced-login-widget-js' ];
    }
    public function get_style_depends() {
        return [ 'advanced-login-widget-css' ];
    }
    protected function _register_controls() {
        // بخش محتوا
        $this->start_controls_section(
            'content_section',
            array(
                'label' => 'تنظیمات داشبورد',
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );
        $this->add_control(
            'login_required_message',
            array(
                'label' => 'پیام نیاز به ورود',
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'برای مشاهده داشبورد، لطفا ابتدا وارد شوید یا ثبت نام کنید.',
            )
        );
        $this->add_control(
            'show_avatar_selector',
            array(
                'label' => 'نمایش انتخابگر آواتار',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'memoji_source',
            array(
                'label' => 'منبع Memoji ها',
                'type' => Controls_Manager::SELECT,
                'default' => 'plugin',
                'options' => array(
                    'plugin' => 'لیست پیشفرض پلاگین (از API)',
                    'custom' => 'لیست سفارشی',
                ),
                'condition' => array(
                    'show_avatar_selector' => 'yes',
                ),
            )
        );
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'memoji_name', array(
                'label' => 'نام Memoji',
                'type' => Controls_Manager::TEXT,
                'default' => 'آواتار من',
            )
        );
        $repeater->add_control(
            'memoji_url', array(
                'label' => 'URL تصویر Memoji',
                'type' => Controls_Manager::MEDIA,
                'default' => array(
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ),
            )
        );
        $this->add_control(
            'custom_memoji_list',
            array(
                'label' => 'لیست Memoji های سفارشی',
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => array(
                    array('memoji_name' => 'لبخند', 'memoji_url' => array('url' => '')),
                    array('memoji_name' => 'قلب', 'memoji_url' => array('url' => '')),
                ),
                'title_field' => '{{{ memoji_name }}}',
                'condition' => array(
                    'show_avatar_selector' => 'yes',
                    'memoji_source' => 'custom',
                ),
            )
        );
        $this->add_control(
            'show_profile_edit',
            array(
                'label' => 'نمایش ویرایش پروفایل',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'show_orders',
            array(
                'label' => 'نمایش بخش "سفارشات من" (نیازمند یکپارچه‌سازی)',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'orders_shortcode',
            array(
                'label' => 'شورت‌کد نمایش سفارشات',
                'type' => Controls_Manager::TEXT,
                'placeholder' => '[my_orders_shortcode]',
                'condition' => array(
                    'show_orders' => 'yes',
                ),
                'description' => 'اگر از افزونه فروشگاهی استفاده می‌کنید، شورت‌کد مربوط به نمایش سفارشات کاربر را اینجا وارد کنید (مثلا برای ووکامرس: [woocommerce_my_account view=orders]).',
            )
        );
        $this->add_control(
            'show_security_tab',
            array(
                'label' => 'نمایش تب "امنیت" (تغییر رمز عبور)',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->end_controls_section();
        // بخش استایل
        $this->start_controls_section(
            'dashboard_style_section',
            array(
                'label' => 'استایل داشبورد',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'dashboard_background',
                'label' => 'پس‌زمینه کلی داشبورد',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .advanced-user-dashboard',
            )
        );
        $this->add_responsive_control(
            'dashboard_padding',
            array(
                'label' => 'فاصله داخلی داشبورد',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-user-dashboard' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_control(
            'hr_dashboard_header',
            array(
                'type' => Controls_Manager::DIVIDER,
            )
        );
        $this->add_control(
            'dashboard_header_heading',
            array(
                'label' => 'استایل هدر داشبورد',
                'type' => Controls_Manager::HEADING,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'dashboard_username_typography',
                'label' => 'تایپوگرافی نام کاربر',
                'selector' => '{{WRAPPER}} .dashboard-header .user-info h3',
            )
        );
        $this->add_control(
            'dashboard_username_color',
            array(
                'label' => 'رنگ نام کاربر',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .dashboard-header .user-info h3' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'dashboard_email_typography',
                'label' => 'تایپوگرافی ایمیل کاربر',
                'selector' => '{{WRAPPER}} .dashboard-header .user-info p',
            )
        );
        $this->add_control(
            'dashboard_email_color',
            array(
                'label' => 'رنگ ایمیل کاربر',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .dashboard-header .user-info p' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            'avatar_size',
            array(
                'label' => 'اندازه آواتار',
                'type' => Controls_Manager::SLIDER,
                'default' => array(
                    'size' => 64,
                    'unit' => 'px',
                ),
                'range' => array(
                    'px' => array(
                        'min' => 32,
                        'max' => 256,
                        'step' => 1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .user-avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();

        if (!is_user_logged_in()) {
            if (!empty($settings['login_required_message'])) {
                 // You might want to wrap this message in some Elementor classes for styling
                echo '<div class="elementor-alert elementor-alert-info" role="alert">';
                echo esc_html($settings['login_required_message']);
                echo '</div>';
            }
            // Optionally, display a login button/link here
            // echo do_shortcode('[advanced_login_button]'); 
            return;
        }

        $current_user = wp_get_current_user();
        $user_meta_memoji = get_user_meta($current_user->ID, 'user_memoji', true);
        $default_avatar = ADVANCED_LOGIN_URL . 'assets/default-avatar.png'; // Ensure this constant is available
        $memoji_url = $user_meta_memoji ? esc_url($user_meta_memoji) : $default_avatar;
        ?>
        <div class="advanced-user-dashboard elementor-widget-container">
            <div class="dashboard-header">
                <div class="user-avatar-section">
                    <img src="<?php echo esc_url($memoji_url); ?>" alt="آواتار" class="user-avatar current-user-avatar">
                    <?php if ($settings['show_avatar_selector'] === 'yes') : ?>
                        <button class="change-avatar-btn elementor-button elementor-button-link">تغییر آواتار</button>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <h3 class="elementor-heading-title">سلام <?php echo esc_html($current_user->display_name); ?></h3>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>
            
            <div class="dashboard-content">
                <div class="dashboard-tabs">
                    <?php 
                    $active_tab_set = false; 
                    if ($settings['show_profile_edit'] === 'yes') : ?>
                        <button class="tab-btn elementor-button <?php if(!$active_tab_set){ echo 'active'; $active_tab_set = true; } ?>" data-tab="profile-edit">پروفایل من</button>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_orders'] === 'yes') : ?>
                        <button class="tab-btn elementor-button <?php if(!$active_tab_set){ echo 'active'; $active_tab_set = true; } ?>" data-tab="orders">سفارشات</button>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_security_tab'] === 'yes') : ?>
                        <button class="tab-btn elementor-button <?php if(!$active_tab_set){ echo 'active'; $active_tab_set = true; } ?>" data-tab="security">امنیت</button>
                    <?php endif; ?>
                </div>
                
                <div class="dashboard-panels">
                    <?php 
                    $active_tab_set_panel = false; 
                    if ($settings['show_profile_edit'] === 'yes') : ?>
                        <div class="tab-panel <?php if(!$active_tab_set_panel){ echo 'active'; $active_tab_set_panel = true; } ?>" id="profile-edit-panel">
                            <form class="profile-edit-form advanced-form elementor-form" method="post">
                                <div class="form-group elementor-form-fields-wrapper">
                                     <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">نام و نام خانوادگی</label>
                                        <input type="text" name="display_name" class="elementor-field elementor-field-textual" value="<?php echo esc_attr($current_user->display_name); ?>">
                                    </div>
                                </div>
                                <div class="form-group elementor-form-fields-wrapper">
                                    <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">ایمیل</label>
                                        <input type="email" name="email" class="elementor-field elementor-field-textual" value="<?php echo esc_attr($current_user->user_email); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group elementor-form-fields-wrapper">
                                    <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">شماره موبایل</label>
                                        <input type="tel" name="phone" class="elementor-field elementor-field-textual" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'user_phone', true)); ?>">
                                    </div>
                                </div>
                                <?php wp_nonce_field('update_user_profile_nonce', 'user_profile_nonce'); ?>
                                <input type="hidden" name="action" value="update_user_profile_advanced">
                                <button type="submit" class="update-profile-btn elementor-button">بروزرسانی پروفایل</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ($settings['show_orders'] === 'yes') : ?>
                        <div class="tab-panel <?php if(!$active_tab_set_panel){ echo 'active'; $active_tab_set_panel = true; } ?>" id="orders-panel">
                            <?php 
                            if (!empty($settings['orders_shortcode'])) {
                                echo do_shortcode(sanitize_text_field($settings['orders_shortcode']));
                            } else {
                                if (class_exists('WooCommerce')) {
                                    echo do_shortcode('[woocommerce_my_account view=orders]');
                                } else {
                                    echo '<p>بخش سفارشات در حال حاضر در دسترس نیست. لطفا شورت‌کد مربوطه را در تنظیمات ویجت وارد کنید.</p>';
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_security_tab'] === 'yes') : ?>
                        <div class="tab-panel <?php if(!$active_tab_set_panel){ echo 'active'; $active_tab_set_panel = true; } ?>" id="security-panel">
                            <form class="password-change-form advanced-form elementor-form" method="post">
                                <div class="form-group elementor-form-fields-wrapper">
                                    <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">رمز عبور فعلی</label>
                                        <input type="password" name="current_password" class="elementor-field elementor-field-textual" required>
                                    </div>
                                </div>
                                <div class="form-group elementor-form-fields-wrapper">
                                    <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">رمز عبور جدید</label>
                                        <input type="password" name="new_password" class="elementor-field elementor-field-textual" required>
                                    </div>
                                </div>
                                <div class="form-group elementor-form-fields-wrapper">
                                    <div class="elementor-field-group elementor-column elementor-col-100">
                                        <label class="elementor-field-label">تأیید رمز عبور جدید</label>
                                        <input type="password" name="confirm_password" class="elementor-field elementor-field-textual" required>
                                    </div>
                                </div>
                                <?php wp_nonce_field('change_user_password_nonce', 'user_password_nonce'); ?>
                                <input type="hidden" name="action" value="change_user_password_advanced">
                                <button type="submit" class="change-password-btn elementor-button">تغییر رمز عبور</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($settings['show_avatar_selector'] === 'yes') : ?>
                <div class="memoji-selector-popup" style="display: none;"> <?php // Changed class for consistency ?>
                    <div class="popup-overlay memoji-overlay"></div>
                    <div class="popup-container memoji-container">
                        <button class="popup-close memoji-close-btn">&times;</button>
                        <h3 class="popup-title">انتخاب آواتار</h3>
                        <div class="memoji-grid">
                            <?php
                            $memoji_list_final = array();
                            if ($settings['memoji_source'] === 'custom' && !empty($settings['custom_memoji_list'])) {
                                foreach ($settings['custom_memoji_list'] as $item) {
                                    if (!empty($item['memoji_name']) && !empty($item['memoji_url']['url'])) {
                                        $memoji_list_final[esc_html($item['memoji_name'])] = esc_url($item['memoji_url']['url']);
                                    }
                                }
                            } else {
                                $memoji_list_final = array();
                            }
                            
                            if (!empty($memoji_list_final)) {
                                foreach ($memoji_list_final as $name => $url) :
                                ?>
                                    <div class="memoji-item" data-url="<?php echo esc_url($url); ?>">
                                        <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
                                    </div>
                                <?php endforeach; 
                            } else if ($settings['memoji_source'] === 'plugin') {
                                 echo '<p class="loading-memojis">در حال بارگذاری آواتارها... (توسط جاوااسکریپت انجام می‌شود)</p>';
                            } else {
                                 echo '<p>لیست آواتار سفارشی خالی است.</p>';
                            }
                            ?>
                        </div>
                        <button class="memoji-select-confirm-btn elementor-button" style="margin-top:15px;">تایید انتخاب</button>
                    </div>
                </div>
            <?php endif; ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Basic Tab Functionality for this widget instance
                    $('.advanced-user-dashboard[data-id="<?php echo esc_js($this->get_id()); ?>"] .dashboard-tabs .tab-btn').on('click', function(e) {
                        e.preventDefault();
                        var tabId = $(this).data('tab');
                        var $dashboard = $(this).closest('.advanced-user-dashboard');

                        $dashboard.find('.dashboard-tabs .tab-btn').removeClass('active');
                        $(this).addClass('active');

                        $dashboard.find('.dashboard-panels .tab-panel').removeClass('active').hide(); // Hide all
                        $dashboard.find('.dashboard-panels #' + tabId + '-panel').addClass('active').show(); // Show selected
                    });
                     // Ensure the first visible tab content is shown
                    $('.advanced-user-dashboard[data-id="<?php echo esc_js($this->get_id()); ?>"] .dashboard-tabs .tab-btn.active').trigger('click');


                    // Memoji Selector specific to this widget instance
                    var $dashboardWidget = $('.advanced-user-dashboard[data-id="<?php echo esc_js($this->get_id()); ?>"]');
                    
                    $dashboardWidget.find('.change-avatar-btn').on('click', function() {
                        $dashboardWidget.find('.memoji-selector-popup').fadeIn();
                    });

                    $dashboardWidget.find('.memoji-close-btn, .memoji-overlay').on('click', function() {
                        $dashboardWidget.find('.memoji-selector-popup').fadeOut();
                    });

                    var selectedMemojiUrl = '';
                    $dashboardWidget.find('.memoji-grid .memoji-item').on('click', function() {
                        selectedMemojiUrl = $(this).data('url');
                        $dashboardWidget.find('.memoji-grid .memoji-item').removeClass('selected');
                        $(this).addClass('selected');
                    });

                    $dashboardWidget.find('.memoji-select-confirm-btn').on('click', function() {
                        if (selectedMemojiUrl) {
                             $.post(advanced_login_ajax.ajax_url, {
                                action: 'update_memoji',
                                nonce: advanced_login_ajax.nonce,
                                memoji_url: selectedMemojiUrl,
                                user_id: <?php echo get_current_user_id(); ?>
                            }).done(function(response) {
                                if (response.success) {
                                    $dashboardWidget.find('.current-user-avatar').attr('src', selectedMemojiUrl);
                                } else {
                                    alert(response.data || 'خطا در ذخیره آواتار.');
                                }
                            }).fail(function() {
                                alert('خطا در ارتباط با سرور.');
                            });
                            $dashboardWidget.find('.memoji-selector-popup').fadeOut();
                        } else {
                            alert('لطفا یک آواتار انتخاب کنید.');
                        }
                    });
                    if ('<?php echo esc_js($settings['memoji_source']); ?>' === 'plugin' && typeof advancedLogin !== 'undefined' && typeof advancedLogin.renderMemojis === 'function') {
                        // advancedLogin.renderMemojis($dashboardWidget.find('.memoji-grid'));
                    }

                });
            </script>
        </div>
        <?php
    }
} 