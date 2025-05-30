jQuery(document).ready(function($) {
    'use strict';

    // متغیرهای سراسری
    let currentTab = 'login';
    let smsTimer = null;
    let smsCountdown = 0;
    let memojiList = [];

    // کلاس اصلی پلاگین
    class AdvancedLogin {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadMemojis();
        }

        // رویدادها
        bindEvents() {
            // نمایش پاپ اپ
            $(document).on('click', '[data-action="show-popup"]', this.showPopup);
            
            // بستن پاپ اپ
            $(document).on('click', '.popup-close, .popup-overlay', this.hidePopup);
            
            // تغییر تب
            $(document).on('click', '.tab-btn', this.switchTab.bind(this));
            
            // ارسال فرم‌ها
            $(document).on('submit', '#login-form form', this.handleLogin.bind(this));
            $(document).on('submit', '#register-form form', this.handleRegister.bind(this));
            $(document).on('submit', '#forgot-form form', this.handleForgotPassword.bind(this));
            
            // نمایش/مخفی کردن رمز عبور
            $(document).on('click', '.password-toggle', this.togglePassword);
            
            // بررسی قدرت رمز عبور
            $(document).on('input', 'input[name="password"]', this.checkPasswordStrength);
            
            // ارسال کد تأیید پیامک
            $(document).on('click', '.verify-phone-btn', this.sendSmsCode.bind(this));
            
            // نمایش فرم فراموشی رمز
            $(document).on('click', '.forgot-password', this.showForgotForm);
            
            // بازگشت به فرم ورود
            $(document).on('click', '.back-to-login', this.backToLogin);
            
            // انتخاب Memoji
            $(document).on('click', '.change-memoji', this.showMemojiSelector.bind(this));
            $(document).on('click', '.memoji-item', this.selectMemoji.bind(this));
            $(document).on('click', '.memoji-close', this.hideMemojiSelector);
            
            // ESC برای بستن پاپ اپ‌ها
            $(document).on('keydown', this.handleEscKey);

            // باز شدن پاپ‌آپ انتخاب آواتار با دکمه change-avatar-btn
            $(document).on('click', '.change-avatar-btn', function(e) {
                e.preventDefault();
                $('#memoji-selector').addClass('active');
                $('.memoji-popup .popup-overlay').addClass('blurred');
                setTimeout(() => $('.memoji-container').addClass('show'), 10);
            });

            // بستن پاپ‌آپ انتخاب آواتار
            $(document).on('click', '.memoji-close, .memoji-popup .popup-overlay', function() {
                $('.memoji-container').removeClass('show');
                setTimeout(() => {
                    $('#memoji-selector').removeClass('active');
                    $('.memoji-popup .popup-overlay').removeClass('blurred');
                }, 300);
            });
        }

        // نمایش پاپ اپ
        showPopup(e) {
            e.preventDefault();
            $('#advanced-login-popup').addClass('active');
            $('body').addClass('popup-open');
            $('.popup-overlay').addClass('blurred');
            setTimeout(() => $('#advanced-login-popup .popup-container').addClass('show'), 10);
        }

        // مخفی کردن پاپ اپ
        hidePopup(e) {
            if (e.target === e.currentTarget) {
                $('#advanced-login-popup .popup-container').removeClass('show');
                setTimeout(() => {
                    $('#advanced-login-popup').removeClass('active');
                    $('body').removeClass('popup-open');
                    $('.popup-overlay').removeClass('blurred');
                }, 300);
            }
        }

        // تغییر تب
        switchTab(e) {
            e.preventDefault();
            const tab = $(e.target).data('tab');
            
            $('.tab-btn').removeClass('active');
            $(e.target).addClass('active');
            
            $('.form-container').removeClass('active');
            $(`#${tab}-form`).addClass('active');
            
            currentTab = tab;
        }

        // مدیریت ورود
        async handleLogin(e) {
            e.preventDefault();
            
            const form = $(e.target);
            const submitBtn = form.find('.submit-btn');
            const formData = new FormData(form[0]);
            
            // غیرفعال کردن دکمه
            submitBtn.addClass('loading').prop('disabled', true);
            
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'advanced_login',
                    nonce: advanced_login_ajax.nonce,
                    username: formData.get('username'),
                    password: formData.get('password'),
                    remember: formData.get('remember') ? 1 : 0
                });

                if (response.success) {
                    this.showNotification('ورود با موفقیت انجام شد!', 'success');
                    setTimeout(() => {
                        window.location.href = response.data.redirect || window.location.reload();
                    }, 1500);
                } else {
                    this.showNotification(response.data || 'خطا در ورود', 'error');
                }
            } catch (error) {
                this.showNotification('خطا در برقراری ارتباط', 'error');
                console.error('Login error:', error);
            } finally {
                submitBtn.removeClass('loading').prop('disabled', false);
            }
        }

        // مدیریت ثبت نام
        async handleRegister(e) {
            e.preventDefault();
            
            const form = $(e.target);
            const submitBtn = form.find('.submit-btn');
            const formData = new FormData(form[0]);
            
            // بررسی تطابق رمز عبور
            if (formData.get('password') !== formData.get('confirm_password')) {
                this.showNotification('رمزهای عبور یکسان نیستند', 'error');
                return;
            }

            // بررسی تأیید پیامک
            if (!this.isSmsVerified(formData.get('phone'))) {
                this.showNotification('لطفاً ابتدا شماره موبایل را تأیید کنید', 'warning');
                return;
            }
            
            submitBtn.addClass('loading').prop('disabled', true);
            
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'advanced_register',
                    nonce: advanced_login_ajax.nonce,
                    username: formData.get('username'),
                    email: formData.get('email'),
                    phone: formData.get('phone'),
                    password: formData.get('password')
                });

                if (response.success) {
                    this.showNotification('ثبت نام با موفقیت انجام شد!', 'success');
                    setTimeout(() => {
                        window.location.href = response.data.redirect || window.location.reload();
                    }, 1500);
                } else {
                    this.showNotification(response.data || 'خطا در ثبت نام', 'error');
                }
            } catch (error) {
                this.showNotification('خطا در برقراری ارتباط', 'error');
                console.error('Register error:', error);
            } finally {
                submitBtn.removeClass('loading').prop('disabled', false);
            }
        }

        // مدیریت فراموشی رمز عبور
        async handleForgotPassword(e) {
            e.preventDefault();
            
            const form = $(e.target);
            const submitBtn = form.find('.submit-btn');
            const formData = new FormData(form[0]);
            
            submitBtn.addClass('loading').prop('disabled', true);
            
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'advanced_reset_password',
                    nonce: advanced_login_ajax.nonce,
                    email_or_phone: formData.get('email_or_phone')
                });

                if (response.success) {
                    this.showNotification('لینک بازیابی ارسال شد', 'success');
                    this.backToLogin();
                } else {
                    this.showNotification(response.data || 'خطا در ارسال', 'error');
                }
            } catch (error) {
                this.showNotification('خطا در برقراری ارتباط', 'error');
            } finally {
                submitBtn.removeClass('loading').prop('disabled', false);
            }
        }

        // ارسال کد تأیید پیامک
        async sendSmsCode(e) {
            e.preventDefault();
            
            const btn = $(e.target);
            const phoneInput = btn.siblings('input[name="phone"]');
            const phone = phoneInput.val().trim();
            
            if (!phone) {
                this.showNotification('شماره موبایل را وارد کنید', 'warning');
                return;
            }

            if (!this.validatePhone(phone)) {
                this.showNotification('شماره موبایل نامعتبر است', 'error');
                return;
            }
            
            btn.prop('disabled', true).text('در حال ارسال...');
            
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'send_sms_code',
                    nonce: advanced_login_ajax.nonce,
                    phone: phone
                });

                if (response.success) {
                    this.showNotification('کد تأیید ارسال شد', 'success');
                    $('.verification-code').slideDown();
                    this.startSmsTimer(btn);
                } else {
                    this.showNotification(response.data || 'خطا در ارسال پیامک', 'error');
                    btn.prop('disabled', false).text('تأیید');
                }
            } catch (error) {
                this.showNotification('خطا در برقراری ارتباط', 'error');
                btn.prop('disabled', false).text('تأیید');
            }
        }

        // شروع تایمر پیامک
        startSmsTimer(btn) {
            smsCountdown = 120; // 2 دقیقه
            
            const updateTimer = () => {
                const minutes = Math.floor(smsCountdown / 60);
                const seconds = smsCountdown % 60;
                btn.text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
                
                if (smsCountdown <= 0) {
                    clearInterval(smsTimer);
                    btn.prop('disabled', false).text('ارسال مجدد');
                } else {
                    smsCountdown--;
                }
            };
            
            smsTimer = setInterval(updateTimer, 1000);
            updateTimer();
        }

        // بررسی تأیید پیامک
        async verifySmsCode(phone, code) {
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'verify_sms_code',
                    nonce: advanced_login_ajax.nonce,
                    phone: phone,
                    code: code
                });

                return response.success;
            } catch (error) {
                console.error('SMS verification error:', error);
                return false;
            }
        }

        // بررسی وضعیت تأیید پیامک
        isSmsVerified(phone) {
            // اینجا باید چک کنید که آیا پیامک تأیید شده یا نه
            // برای سادگی فرض می‌کنیم که اگر فیلد کد تأیید پر باشد، تأیید شده
            const verificationCode = $('input[name="verification_code"]').val();
            return verificationCode && verificationCode.length === 6;
        }

        // نمایش/مخفی کردن رمز عبور
        togglePassword(e) {
            e.preventDefault();
            
            const btn = $(e.target);
            const input = btn.closest('.form-group').find('input');
            const icon = btn.find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        }

        // بررسی قدرت رمز عبور
        checkPasswordStrength(e) {
            const password = $(e.target).val();
            const strengthMeter = $(e.target).siblings('.password-strength');
            
            if (!strengthMeter.length) return;
            
            let strength = 0;
            
            // طول رمز
            if (password.length >= 8) strength++;
            
            // حروف بزرگ و// حروف بزرگ و کوچک
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            
            // اعداد
            if (/\d/.test(password)) strength++;
            
            // کاراکترهای خاص
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
            
            // تنظیم نوار قدرت
            strengthMeter.removeClass('weak medium strong very-strong');
            
            switch (strength) {
                case 0:
                case 1:
                    strengthMeter.addClass('weak').text('ضعیف');
                    break;
                case 2:
                    strengthMeter.addClass('medium').text('متوسط');
                    break;
                case 3:
                    strengthMeter.addClass('strong').text('قوی');
                    break;
                case 4:
                    strengthMeter.addClass('very-strong').text('بسیار قوی');
                    break;
            }
        }

        // نمایش فرم فراموشی رمز
        showForgotForm(e) {
            e.preventDefault();
            
            $('.form-container').removeClass('active');
            $('#forgot-form').addClass('active');
        }

        // بازگشت به فرم ورود
        backToLogin(e) {
            if (e) e.preventDefault();
            
            $('.form-container').removeClass('active');
            $('#login-form').addClass('active');
            
            $('.tab-btn').removeClass('active');
            $('.tab-btn[data-tab="login"]').addClass('active');
        }

        // نمایش انتخابگر Memoji
        showMemojiSelector(e) {
            e.preventDefault();
            
            $('#memoji-selector').addClass('active');
            $('body').addClass('memoji-open');
        }

        // مخفی کردن انتخابگر Memoji
        hideMemojiSelector(e) {
            if (e && e.target !== e.currentTarget) return;
            
            $('#memoji-selector').removeClass('active');
            $('body').removeClass('memoji-open');
        }

        // انتخاب Memoji
        async selectMemoji(e) {
            e.preventDefault();
            
            const memojiUrl = $(e.target).data('url');
            
            try {
                const response = await $.post(advanced_login_ajax.ajax_url, {
                    action: 'update_memoji',
                    nonce: advanced_login_ajax.nonce,
                    memoji_url: memojiUrl
                });

                if (response.success) {
                    this.showNotification('آواتار تغییر یافت!', 'success');
                    $('.user-avatar').attr('src', memojiUrl);
                    this.hideMemojiSelector();
                } else {
                    this.showNotification(response.data || 'خطا در تغییر آواتار', 'error');
                }
            } catch (error) {
                this.showNotification('خطا در برقراری ارتباط', 'error');
            }
        }

        // بارگذاری Memoji ها
        async loadMemojis() {
            try {
                const response = await fetch(advanced_login_ajax.memoji_api);
                const data = await response.json();
                
                memojiList = data.filter(item => item.name.endsWith('.png'));
                this.renderMemojis();
            } catch (error) {
                console.error('Error loading memojis:', error);
            }
        }

        // رندر کردن Memoji ها
        renderMemojis() {
            const container = $('.memoji-grid');
            container.empty();
            
            memojiList.forEach(memoji => {
                const memojiElement = $(`
                    <div class="memoji-item" data-url="${memoji.download_url}">
                        <img src="${memoji.download_url}" alt="${memoji.name}" loading="lazy">
                    </div>
                `);
                
                container.append(memojiElement);
            });
        }

        // مدیریت کلید ESC
        handleEscKey(e) {
            if (e.keyCode === 27) { // ESC key
                if ($('#advanced-login-popup').hasClass('active')) {
                    $('#advanced-login-popup').removeClass('active');
                    $('body').removeClass('popup-open');
                }
                
                if ($('#memoji-selector').hasClass('active')) {
                    $('#memoji-selector').removeClass('active');
                    $('body').removeClass('memoji-open');
                }
            }
        }

        // نمایش اعلان
        showNotification(message, type = 'info') {
            // حذف اعلان‌های قبلی
            $('.advanced-notification').remove();
            
            const notification = $(`
                <div class="advanced-notification ${type}">
                    <div class="notification-content">
                        <i class="fas ${this.getNotificationIcon(type)}"></i>
                        <span>${message}</span>
                        <button class="notification-close">&times;</button>
                    </div>
                </div>
            `);
            
            $('body').append(notification);
            
            // انیمیشن ورود
            setTimeout(() => {
                notification.addClass('show');
            }, 100);
            
            // حذف خودکار بعد از 5 ثانیه
            setTimeout(() => {
                this.hideNotification(notification);
            }, 5000);
            
            // رویداد بستن دستی
            notification.find('.notification-close').on('click', () => {
                this.hideNotification(notification);
            });
        }

        // مخفی کردن اعلان
        hideNotification(notification) {
            notification.removeClass('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }

        // آیکون اعلان
        getNotificationIcon(type) {
            switch (type) {
                case 'success':
                    return 'fa-check-circle';
                case 'error':
                    return 'fa-exclamation-circle';
                case 'warning':
                    return 'fa-exclamation-triangle';
                default:
                    return 'fa-info-circle';
            }
        }

        // اعتبارسنجی شماره موبایل
        validatePhone(phone) {
            // الگوی شماره موبایل ایرانی
            const iranMobilePattern = /^(\+98|0)?9\d{9}$/;
            return iranMobilePattern.test(phone);
        }

        // اعتبارسنجی ایمیل
        validateEmail(email) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(email);
        }

        // تمیز کردن فرم
        clearForm(formSelector) {
            $(formSelector).find('input[type="text"], input[type="email"], input[type="password"], input[type="tel"]').val('');
            $(formSelector).find('input[type="checkbox"]').prop('checked', false);
            $(formSelector).find('.password-strength').text('').removeClass('weak medium strong very-strong');
        }

        // اضافه کردن افکت بارگذاری به دکمه
        addButtonLoading(button) {
            const originalText = button.text();
            button.data('original-text', originalText)
                  .addClass('loading')
                  .prop('disabled', true)
                  .html('<i class="fas fa-spinner fa-spin"></i> در حال پردازش...');
        }

        // حذف افکت بارگذاری از دکمه
        removeButtonLoading(button) {
            const originalText = button.data('original-text');
            button.removeClass('loading')
                  .prop('disabled', false)
                  .text(originalText);
        }
    }

    // راه‌اندازی پلاگین
    const advancedLogin = new AdvancedLogin();

    // رویدادهای اضافی
    
    // منوی کاربری
    $(document).on('click', '.user-account-btn', function(e) {
        if ($(this).hasClass('logged-in')) {
            $(this).next('.user-dropdown').toggleClass('show');
        } else {
            showPopup();
        }
    });

    // بستن منوی کاربری با کلیک خارج از آن
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.advanced-login-user-menu').length) {
            $('.user-dropdown').removeClass('show');
        }
    });

    // انیمیشن فرم‌ها
    $('.advanced-form input').on('focus', function() {
        $(this).parent().addClass('focused');
    });

    $('.advanced-form input').on('blur', function() {
        if (!$(this).val()) {
            $(this).parent().removeClass('focused');
        }
    });

    // بررسی خودکار کد تأیید پیامک
    $(document).on('input', 'input[name="verification_code"]', function() {
        const code = $(this).val();
        const phone = $('input[name="phone"]').val();
        
        if (code.length === 6) {
            advancedLogin.verifySmsCode(phone, code).then(isValid => {
                if (isValid) {
                    $(this).addClass('verified');
                    advancedLogin.showNotification('کد تأیید صحیح است', 'success');
                } else {
                    $(this).addClass('invalid');
                    advancedLogin.showNotification('کد تأیید نامعتبر است', 'error');
                }
            });
        } else {
            $(this).removeClass('verified invalid');
        }
    });

    // پیش‌نمایش آواتار
    $(document).on('mouseenter', '.memoji-item', function() {
        $(this).addClass('hover');
    });

    $(document).on('mouseleave', '.memoji-item', function() {
        $(this).removeClass('hover');
    });

    // بررسی اتصال اینترنت
    window.addEventListener('online', function() {
        advancedLogin.showNotification('اتصال اینترنت برقرار شد', 'success');
    });

    window.addEventListener('offline', function() {
        advancedLogin.showNotification('اتصال اینترنت قطع شد', 'warning');
    });

    // ذخیره وضعیت فرم در localStorage (در صورت نیاز)
    function saveFormState() {
        const formData = {
            currentTab: currentTab,
            timestamp: Date.now()
        };
        
        try {
            localStorage.setItem('advanced_login_state', JSON.stringify(formData));
        } catch (e) {
            console.warn('Unable to save form state:', e);
        }
    }

    // بازیابی وضعیت فرم
    function restoreFormState() {
        try {
            const savedState = localStorage.getItem('advanced_login_state');
            if (savedState) {
                const formData = JSON.parse(savedState);
                
                // بررسی تاریخ انقضا (30 دقیقه)
                if (Date.now() - formData.timestamp < 30 * 60 * 1000) {
                    currentTab = formData.currentTab;
                    
                    $('.tab-btn').removeClass('active');
                    $(`.tab-btn[data-tab="${currentTab}"]`).addClass('active');
                    
                    $('.form-container').removeClass('active');
                    $(`#${currentTab}-form`).addClass('active');
                }
            }
        } catch (e) {
            console.warn('Unable to restore form state:', e);
        }
    }

    // اجرای بازیابی وضعیت در شروع
    restoreFormState();

    // ذخیره وضعیت هنگام تغییر تب
    $(document).on('click', '.tab-btn', saveFormState);

    console.log('🚀 Advanced Login Plugin loaded successfully!');

    // نمایش Toast
    function showToast(message, type = 'success') {
        $('.advanced-notification').remove();
        const toast = $('<div class="advanced-notification ' + type + '">' + message + '</div>');
        $('body').append(toast);
        setTimeout(() => toast.addClass('show'), 100);
        setTimeout(() => toast.removeClass('show'), 3500);
        setTimeout(() => toast.remove(), 4000);
    }

    // انتخاب آواتار
    $(document).on('click', '.memoji-item', function() {
        $('.memoji-item').removeClass('selected');
        $(this).addClass('selected');
        let url = $(this).data('url');
        $.post(advanced_login_ajax.ajax_url, {
            action: 'update_memoji',
            nonce: advanced_login_ajax.nonce,
            memoji_url: url
        }).done(function(response) {
            if (response.success) {
                $('.user-avatar').attr('src', url);
                showToast('آواتار با موفقیت تغییر کرد!','success');
            } else {
                showToast(response.data || 'خطا در تغییر آواتار','error');
            }
        });
    });

    // تغییر حالت تاریک/روشن
    $(document).on('click', '.toggle-darkmode', function() {
        $('body').toggleClass('dark-mode');
    });
});