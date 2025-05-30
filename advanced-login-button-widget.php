<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// ویجت دکمه ورود
class Advanced_Login_Button_Widget extends Widget_Base {
    public function get_name() {
        return 'advanced-login-button';
    }
    public function get_title() {
        return 'دکمه حساب کاربری';
    }
    public function get_icon() {
        return 'eicon-button';
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
                'label' => 'محتوا',
                'tab' => Controls_Manager::TAB_CONTENT,
            )
        );
        $this->add_control(
            'button_text',
            array(
                'label' => 'متن دکمه',
                'type' => Controls_Manager::TEXT,
                'default' => 'حساب کاربری',
                'placeholder' => 'متن دکمه را وارد کنید',
            )
        );
        $this->add_control(
            'show_icon',
            array(
                'label' => 'نمایش آیکون',
                'type' => Controls_Manager::SWITCHER,
                'label_on' => 'بله',
                'label_off' => 'خیر',
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );
        $this->add_control(
            'button_icon',
            array(
                'label' => 'آیکون',
                'type' => Controls_Manager::ICONS,
                'default' => array(
                    'value' => 'fas fa-user',
                    'library' => 'solid',
                ),
                'condition' => array(
                    'show_icon' => 'yes',
                ),
            )
        );
        $this->add_control(
            'icon_position',
            array(
                'label' => 'موقعیت آیکون',
                'type' => Controls_Manager::SELECT,
                'default' => 'before',
                'options' => array(
                    'before' => 'قبل از متن',
                    'after' => 'بعد از متن',
                ),
                'condition' => array(
                    'show_icon' => 'yes',
                ),
            )
        );
        $this->end_controls_section();
        // بخش استایل دکمه
        $this->start_controls_section(
            'button_style_section',
            array(
                'label' => 'استایل دکمه',
                'tab' => Controls_Manager::TAB_STYLE,
            )
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name' => 'button_typography',
                'label' => 'تایپوگرافی',
                'selector' => '{{WRAPPER}} .advanced-login-btn',
            )
        );
        $this->start_controls_tabs('button_style_tabs');
        // حالت عادی
        $this->start_controls_tab(
            'button_normal_tab',
            array(
                'label' => 'عادی',
            )
        );
        $this->add_control(
            'button_color',
            array(
                'label' => 'رنگ متن',
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'button_background',
                'label' => 'پس‌زمینه',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .advanced-login-btn',
            )
        );
        $this->end_controls_tab();
        // حالت هاور
        $this->start_controls_tab(
            'button_hover_tab',
            array(
                'label' => 'هاور',
            )
        );
        $this->add_control(
            'button_hover_color',
            array(
                'label' => 'رنگ متن',
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn:hover' => 'color: {{VALUE}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name' => 'button_hover_background',
                'label' => 'پس‌زمینه',
                'types' => array('classic', 'gradient'),
                'selector' => '{{WRAPPER}} .advanced-login-btn:hover',
            )
        );
        $this->add_control(
            'button_hover_animation',
            array(
                'label' => 'انیمیشن هاور',
                'type' => Controls_Manager::HOVER_ANIMATION,
            )
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'button_border',
                'label' => 'حاشیه',
                'selector' => '{{WRAPPER}} .advanced-login-btn',
                'separator' => 'before',
            )
        );
        $this->add_control(
            'button_border_radius',
            array(
                'label' => 'گردی حاشیه',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'default' => array(
                    'top' => 25,
                    'right' => 25,
                    'bottom' => 25,
                    'left' => 25,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'button_box_shadow',
                'label' => 'سایه',
                'selector' => '{{WRAPPER}} .advanced-login-btn',
            )
        );
        $this->add_responsive_control(
            'button_padding',
            array(
                'label' => 'فاصله داخلی',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => array(
                    'top' => 12,
                    'right' => 24,
                    'bottom' => 12,
                    'left' => 24,
                    'unit' => 'px',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'separator' => 'before',
            )
        );
        $this->add_responsive_control(
            'button_margin',
            array(
                'label' => 'فاصله خارجی',
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
        // بخش استایل آیکون
        $this->start_controls_section(
            'icon_style_section',
            array(
                'label' => 'استایل آیکون',
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_icon' => 'yes',
                ),
            )
        );
        $this->add_control(
            'icon_size',
            array(
                'label' => 'اندازه آیکون',
                'type' => Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ),
                ),
                'default' => array(
                    'unit' => 'px',
                    'size' => 16,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .advanced-login-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );
        $this->add_control(
            'icon_spacing',
            array(
                'label' => 'فاصله آیکون',
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
                    'size' => 8,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .advanced-login-btn.icon-before i, {{WRAPPER}} .advanced-login-btn.icon-before svg' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .advanced-login-btn.icon-after i, {{WRAPPER}} .advanced-login-btn.icon-after svg' => 'margin-right: {{SIZE}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        $button_classes = array('advanced-login-btn');
        if (!empty($settings['button_hover_animation'])) {
            $button_classes[] = 'elementor-animation-' . $settings['button_hover_animation'];
        }
        if ($settings['show_icon'] === 'yes' && !empty($settings['button_icon']['value'])) {
            $button_classes[] = 'icon-' . $settings['icon_position'];
        }
        echo '<div class="advanced-login-button-widget">';
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $memoji = get_user_meta($current_user->ID, 'user_memoji', true);
            $memoji_url = $memoji ? esc_url($memoji) : ADVANCED_LOGIN_URL . 'assets/default-avatar.png';
            echo '<div class="advanced-login-user-menu">';
            echo '<button class="' . esc_attr(implode(' ', $button_classes)) . ' logged-in">';
            echo '<img src="' . esc_url($memoji_url) . '" alt="آواتار" class="user-avatar">';
            echo '<span>' . esc_html($current_user->display_name) . '</span>';
            echo '</button>';
            echo '<div class="user-dropdown">';
            echo '<a href="' . esc_url(admin_url()) . '">پیشخوان</a>';
            echo '<a href="#" class="change-memoji">تغییر آواتار</a>';
            echo '<a href="' . esc_url(wp_logout_url()) . '">خروج</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<button class="' . esc_attr(implode(' ', $button_classes)) . '" data-action="show-popup">';
            if ($settings['show_icon'] === 'yes' && !empty($settings['button_icon']['value']) && $settings['icon_position'] === 'before') {
                \Elementor\Icons_Manager::render_icon($settings['button_icon'], array('aria-hidden' => 'true'));
            }
            echo '<span>' . esc_html($settings['button_text']) . '</span>';
            if ($settings['show_icon'] === 'yes' && !empty($settings['button_icon']['value']) && $settings['icon_position'] === 'after') {
                \Elementor\Icons_Manager::render_icon($settings['button_icon'], array('aria-hidden' => 'true'));
            }
            echo '</button>';
        }
        echo '</div>';
    }
} 