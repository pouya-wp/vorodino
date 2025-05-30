<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Repeater;

// ویجت فرم ورود
class Advanced_Login_Form_Widget extends Widget_Base {
    public function get_name() {
        return 'advanced-login-form';
    }
    public function get_title() {
        return 'فرم ورود';
    }
    public function get_icon() {
        return 'eicon-form-horizontal';
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
                'default' => 'ورود به حساب کاربری',
            )
        );
        $this->add_control(
            'show_remember',
            array(
                'label' => 'نمایش "مرا به خاطر بسپار"',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'show_forgot_password',
            array(
                'label' => 'نمایش "فراموشی رمز عبور"',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'redirect_after_login',
            array(
                'label' => 'صفحه هدایت بعد از ورود',
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
            'form_style_section',
            array(
                'label' => 'استایل فرم',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'form_background',
                'label' => 'پس‌زمینه فرم',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .advanced-login-form-container',
            )
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'form_border',
                'label' => 'حاشیه فرم',
                'selector' => '{{WRAPPER}} .advanced-login-form-container',
            )
        );
        $this->add_control(
            'form_border_radius',
            array(
                'label' => 'گردی حاشیه',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'default' => array(
                    'top' => 10,
                    'right' => 10,
                    'bottom' => 10,
                    'left' => 10,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-form-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'form_box_shadow',
                'label' => 'سایه فرم',
                'selector' => '{{WRAPPER}} .advanced-login-form-container',
            )
        );
        $this->add_responsive_control(
            'form_padding',
            array(
                'label' => 'فاصله داخلی',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => array(
                    'top' => 30,
                    'right' => 30,
                    'bottom' => 30,
                    'left' => 30,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
        // بخش استایل فیلدها
        $this->start_controls_section(
            'fields_style_section',
            array(
                'label' => 'استایل فیلدها',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'field_typography',
                'label' => 'تایپوگرافی فیلد',
                'selector' => '{{WRAPPER}} .form-group input, {{WRAPPER}} .form-group label',
            )
        );
        $this->add_control(
            'label_color',
            array(
                'label' => 'رنگ لیبل',
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => array(
                    '{{WRAPPER}} .form-group label' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_control(
            'field_text_color',
            array(
                'label' => 'رنگ متن فیلد',
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => array(
                    '{{WRAPPER}} .form-group input' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'field_background',
                'label' => 'پس‌زمینه فیلد',
                'types' => array('classic'),
                'selector' => '{{WRAPPER}} .form-group input',
                'default' => array(
                    'background' => 'classic',
                    'color' => '#f9f9f9',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'field_border',
                'label' => 'حاشیه فیلد',
                'selector' => '{{WRAPPER}} .form-group input',
            )
        );
        $this->add_control(
            'field_border_radius',
            array(
                'label' => 'گردی فیلد',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'default' => array(
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .form-group input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_responsive_control(
            'field_padding',
            array(
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em'),
                'default' => array(
                    'top' => 12,
                    'right' => 15,
                    'bottom' => 12,
                    'left' => 15,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .form-group input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_responsive_control(
            'field_margin_bottom',
            array(
                'label' => 'فاصله پایین فیلدها',
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ),
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 20,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .form-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
        // بخش استایل دکمه ورود
        $this->start_controls_section(
            'submit_button_style_section',
            array(
                'label' => 'استایل دکمه ارسال',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'submit_button_typography',
                'label' => 'تایپوگرافی',
                'selector' => '{{WRAPPER}} .submit-btn',
            )
        );
        $this->start_controls_tabs('submit_button_style_tabs');
        $this->start_controls_tab(
            'submit_button_normal_tab',
            array(
                'label' => 'عادی',
            )
        );
        $this->add_control(
            'submit_button_color',
            array(
                'label' => 'رنگ متن',
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'submit_button_background',
                'label' => 'پس‌زمینه',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .submit-btn',
                'default' => array(
                    'background' => 'classic',
                    'color' => '#667eea',
                ),
            )
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'submit_button_hover_tab',
            array(
                'label' => 'هاور',
            )
        );
        $this->add_control(
            'submit_button_hover_color',
            array(
                'label' => 'رنگ متن هاور',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn:hover' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'submit_button_hover_background',
                'label' => 'پس‌زمینه هاور',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .submit-btn:hover',
            )
        );
        $this->add_control(
            'submit_button_hover_border_color',
            array(
                'label' => 'رنگ حاشیه هاور',
                'type' => Controls_Manager::COLOR,
                'condition' => array(
                    'submit_button_border_border!' => '',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'submit_button_border',
                'label' => 'حاشیه',
                'selector' => '{{WRAPPER}} .submit-btn',
                'separator' => 'before',
            )
        );
        $this->add_control(
            'submit_button_border_radius',
            array(
                'label' => 'گردی حاشیه',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'default' => array(
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_responsive_control(
            'submit_button_padding',
            array(
                'label' => 'فاصله داخلی',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => array(
                    'top' => 15,
                    'right' => 30,
                    'bottom' => 15,
                    'left' => 30,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_responsive_control(
            'submit_button_margin',
            array(
                'label' => 'فاصله خارجی دکمه ارسال',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .submit-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="advanced-login-form-widget">
            <div class="advanced-login-form-container elementor-form">
                <?php if (!empty($settings['form_title'])) : ?>
                    <h3 class="form-title elementor-heading-title"><?php echo esc_html($settings['form_title']); ?></h3>
                <?php endif; ?>
                
                <form class="advanced-form login-form elementor-form" method="post"> 
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                        <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">نام کاربری یا ایمیل</label>
                            <input size="1" type="text" name="username" class="elementor-field elementor-field-textual elementor-size-md" required>
                        </div>
                    </div>
                    
                    <div class="form-group elementor-form-fields-wrapper elementor-labels-above">
                         <div class="elementor-field-group elementor-column elementor-col-100">
                            <label class="elementor-field-label">رمز عبور</label>
                            <input size="1" type="password" name="password" class="elementor-field elementor-field-textual elementor-size-md" required>
                            <button type="button" class="password-toggle" style="position: absolute; right: 30px; top: 70%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer;"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    
                    <div class="form-options elementor-field-group">
                        <?php if ($settings['show_remember'] === 'yes') : ?>
                            <div class="elementor-field-subgroup">
                                <span class="elementor-field-option">
                                    <input type="checkbox" name="remember" id="remember-me-<?php echo esc_attr($this->get_id()); ?>">
                                    <label for="remember-me-<?php echo esc_attr($this->get_id()); ?>">مرا به خاطر بسپار</label>
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($settings['show_forgot_password'] === 'yes') : ?>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password-link">فراموشی رمز عبور؟</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group elementor-field-group elementor-column elementor-col-100 elementor-button-wrapper">
                        <button type="submit" class="submit-btn elementor-button elementor-size-md">
                            <span class="elementor-button-content-wrapper">
                                <span class="elementor-button-text">ورود</span>
                            </span>
                        </button>
                    </div>
                     <?php 
                        if (!empty($settings['redirect_after_login']['url'])) {
                            // echo '<input type="hidden" name="redirect_to" value="' . esc_url($settings['redirect_after_login']['url']) . '">';
                        }
                    ?>
                </form>
            </div>
        </div>
        <?php
    }
} 