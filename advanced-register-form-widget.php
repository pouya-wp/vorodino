<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Utils;

// ویجت فرم ثبت نام
class Advanced_Register_Form_Widget extends Widget_Base {
    public function get_name() {
        return 'advanced-register-form';
    }
    public function get_title() {
        return 'فرم ثبت نام';
    }
    public function get_icon() {
        return 'eicon-form-vertical';
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
                'label' => 'تنظیمات فرم',
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );
        $this->add_control(
            'form_title',
            array(
                'label' => 'عنوان فرم',
                'type' => Controls_Manager::TEXT,
                'default' => 'ثبت نام در سایت',
            )
        );
        $this->add_control(
            'show_name_field',
            array(
                'label' => 'نمایش فیلد نام و نام خانوادگی',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'show_username_field',
            array(
                'label' => 'نمایش فیلد نام کاربری',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'show_phone_field',
            array(
                'label' => 'نمایش فیلد موبایل',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'phone_verification',
            array(
                'label' => 'نیاز به تأیید موبایل (با دکمه)',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => array(
                    'show_phone_field' => 'yes',
                ),
            )
        );
        $this->add_control(
            'terms_conditions',
            array(
                'label' => 'نیاز به پذیرش قوانین و مقررات',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'terms_link',
            array(
                'label' => 'لینک قوانین و مقررات',
                'type' => Controls_Manager::URL,
                'placeholder' => home_url('/terms-and-conditions'),
                'default' => array(
                    'url' => home_url('/terms-and-conditions'),
                ),
                'condition' => array(
                    'terms_conditions' => 'yes',
                ),
            )
        );
        $this->add_control(
            'redirect_after_register',
            array(
                'label' => 'صفحه هدایت بعد از ثبت نام',
                'type' => Controls_Manager::URL,
                'placeholder' => home_url(),
                'show_external' => true,
                'default' => array(
                    'url' => '',
                ),
            )
        );
        $this->end_controls_section();
        // بخش استایل فرم
        $this->start_controls_section(
            'register_form_style_section',
            array(
                'label' => 'استایل فرم ثبت نام',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'register_form_background',
                'label' => 'پس‌زمینه فرم',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .advanced-register-form-container',
            )
        );
        $this->add_responsive_control(
            'register_form_padding',
            array(
                'label' => 'فاصله داخلی فرم',
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => array(
                    '{{WRAPPER}} .advanced-register-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'register_fields_style_section',
            array(
                'label' => 'استایل فیلدهای ثبت نام',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'register_field_typography',
                'label' => 'تایپوگرافی فیلد',
                'selector' => '{{WRAPPER}} .register-form .form-group input, {{WRAPPER}} .register-form .form-group label',
            )
        );
        $this->add_control(
            'register_label_color',
            array(
                'label' => 'رنگ لیبل',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .register-form .form-group label' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            'register_field_text_color',
            array(
                'label' => 'رنگ متن فیلد',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .register-form .form-group input' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'register_field_border',
                'label' => 'حاشیه فیلد',
                'selector' => '{{WRAPPER}} .register-form .form-group input',
            )
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'register_submit_button_style_section',
            array(
                'label' => 'استایل دکمه ثبت نام',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'register_submit_button_typography',
                'label' => 'تایپوگرافی',
                'selector' => '{{WRAPPER}} .register-form .submit-btn',
            )
        );
        $this->add_control(
            'register_submit_button_color',
            array(
                'label' => 'رنگ متن دکمه',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .register-form .submit-btn' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'register_submit_button_background',
                'label' => 'پس‌زمینه دکمه',
                'selector' => '{{WRAPPER}} .register-form .submit-btn',
            )
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="advanced-register-form-widget">
            <div class="advanced-register-form-container elementor-form">
                <?php if (!empty($settings['form_title'])) : ?>
                    <h3 class="form-title elementor-heading-title"><?php echo esc_html($settings['form_title']); ?></h3>
                <?php endif; ?>
                
                <form class="advanced-form register-form elementor-form" method="post">
                    <?php if ($settings['show_name_field'] === 'yes') : ?>
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">نام و نام خانوادگی</label>
                            <input size="1" type="text" name="display_name" class="elementor-field elementor-field-textual elementor-size-md" required>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_username_field'] === 'yes') : ?>
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">نام کاربری</label>
                            <input size="1" type="text" name="username" class="elementor-field elementor-field-textual elementor-size-md" required>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">ایمیل</label>
                            <input size="1" type="email" name="email" class="elementor-field elementor-field-textual elementor-size-md" required>
                        </div>
                    </div>
                    
                    <?php if ($settings['show_phone_field'] === 'yes') : ?>
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">شماره موبایل</label>
                            <input size="1" type="tel" name="phone" class="elementor-field elementor-field-textual elementor-size-md" required>
                            <?php if ($settings['phone_verification'] === 'yes') : ?>
                                <button type="button" class="verify-phone-btn elementor-button elementor-button-link" style="margin-top: 5px;">تأیید شماره</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">رمز عبور</label>
                            <input size="1" type="password" name="password" class="elementor-field elementor-field-textual elementor-size-md" required>
                             <button type="button" class="password-toggle" style="position: absolute; right: 30px; top: 70%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer;"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">تأیید رمز عبور</label>
                            <input size="1" type="password" name="password_confirm" class="elementor-field elementor-field-textual elementor-size-md" required>
                        </div>
                    </div>
                    
                    <?php if ($settings['terms_conditions'] === 'yes') : ?>
                    <div class="form-options elementor-field-group">
                         <div class="elementor-field-subgroup">
                            <span class="elementor-field-option">
                                <input type="checkbox" name="terms" id="terms-<?php echo esc_attr($this->get_id()); ?>" required>
                                <label for="terms-<?php echo esc_attr($this->get_id()); ?>">
                                    با <a href="<?php echo esc_url($settings['terms_link']['url']); ?>" target="_blank">قوانین و مقررات</a> موافقم.
                                </label>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                     <div class="form-group elementor-field-group elementor-column elementor-col-100 elementor-button-wrapper">
                        <button type="submit" class="submit-btn elementor-button elementor-size-md">
                             <span class="elementor-button-content-wrapper">
                                <span class="elementor-button-text">ثبت نام</span>
                            </span>
                        </button>
                    </div>
                    <?php
                        if (!empty($settings['redirect_after_register']['url'])) {
                            // echo '<input type="hidden" name="redirect_to" value="' . esc_url($settings['redirect_after_register']['url']) . '">';
                        }
                    ?>
                </form>
            </div>
        </div>
        <?php
    }
} 