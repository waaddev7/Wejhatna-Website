// ننتظر حتى يتم تحميل كل عناصر الصفحة قبل تشغيل أي كود
document.addEventListener('DOMContentLoaded', () => {
// 1. احصل على اسم الصفحة الحالية من الرابط
    // مثال: "http://localhost/trips.html" -> "trips.php"
    const currentPage = window.location.pathname.split("/").pop();

    // 2. احصل على كل الروابط الموجودة داخل القائمة
    const navLinks = document.querySelectorAll('.menu a');

    // 3. قم بالمرور على كل رابط
    navLinks.forEach(link => {
        // 4. قارن رابط كل زر باسم الصفحة الحالية
        if (link.getAttribute('href') === currentPage) {
            // 5. إذا تطابقا، أضف كلاس 'active'
            link.classList.add('active');
        }
    });
    // ==========================================================
    // القسم الأول: تعريف كل العناصر التي سنتعامل معها
    // ==========================================================
    const container = document.getElementById('container');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    // --- عناصر خاصة بصفحة تسجيل الدخول ---
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const signInForm = document.getElementById('signInForm');
    const signUpForm = document.getElementById('signUpForm');

    // --- عناصر النافذة المنبثقة للرسائل (Modal) ---
    const messageModal = document.getElementById('messageModal');
    const modalMessageText = document.getElementById('modalMessageText');
    const messageModalClose = document.querySelector('#messageModal .close-button');

    // --- عناصر نافذة "نسيت كلمة المرور" ---
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const forgotCloseButton = document.querySelector('#forgotPasswordModal .close-button');
    const resetRequestForm = document.getElementById('resetRequestForm');
    const resetModalMessage = document.getElementById('modalMessage');


    // ==========================================================
    // القسم الثاني: الوظائف والعمليات
    // ==========================================================

    // --- 1: وظيفة قائمة الموبايل ---
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }

    // --- 2: وظائف خاصة بصفحة تسجيل الدخول (إذا كانت موجودة) ---
    if (container) {

        // --- أ: الأنيميشن للوحة المتحركة (Overlay) ---
        if (signUpButton) signUpButton.addEventListener('click', () => container.classList.add("right-panel-active"));
        if (signInButton) signInButton.addEventListener('click', () => container.classList.remove("right-panel-active"));

        // --- ب: دالة عامة لإظهار الرسائل في النافذة ---
        function showMessage(modal, textElement, message, isSuccess) {
            if (modal && textElement) {
                textElement.textContent = message;
                textElement.style.color = isSuccess ? 'green' : 'red';
                modal.style.display = 'block';
            }
        }

        // --- ج: معالجة فورم تسجيل الدخول (Sign In) ---
        if (signInForm) {
            signInForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                if(submitBtn) submitBtn.disabled = true; // تعطيل الزر

                const formData = new FormData(this);

                fetch('signin_process.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showMessage(messageModal, modalMessageText, data.message + ' Redirecting...', true);
                            setTimeout(() => { window.location.href = data.redirectUrl; }, 2000);
                        } else {
                            showMessage(messageModal, modalMessageText, data.message, false);
                        }
                    })
                    .catch(error => { console.error('Error:', error); showMessage(messageModal, modalMessageText, 'An unexpected error occurred.', false); })
                    .finally(() => {
                        if(submitBtn) submitBtn.disabled = false; // إعادة تفعيل الزر
                    });
            });
        }

        // --- د: معالجة فورم إنشاء حساب (Sign Up) ---
        if (signUpForm) {
            signUpForm.addEventListener('submit', function (event) {
                event.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                if(submitBtn) submitBtn.disabled = true; // تعطيل الزر

                const formData = new FormData(this);

                fetch('khara.php', { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showMessage(messageModal, modalMessageText, data.message, true);
                            this.reset();
                            setTimeout(() => {
                                if(messageModal) messageModal.style.display = "none";
                                if(container) container.classList.remove("right-panel-active");
                            }, 2000);
                        } else {
                            showMessage(messageModal, modalMessageText, data.message, false);
                        }
                    })
                    .catch(error => { console.error('Error:', error); showMessage(messageModal, modalMessageText, 'An unexpected error occurred.', false); })
                    .finally(() => {
                        if(submitBtn) submitBtn.disabled = false; // إعادة تفعيل الزر
                    });
            });
        }

        // --- هـ: معالجة فورم "نسيت كلمة المرور" ---
        if (forgotPasswordLink) forgotPasswordLink.addEventListener('click', (e) => { e.preventDefault(); if(forgotPasswordModal) forgotPasswordModal.style.display = 'block'; });
        if (resetRequestForm) {
            resetRequestForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = document.getElementById('resetEmail').value;
                showMessage(forgotPasswordModal, resetModalMessage, 'Sending...', true); // إظهار رسالة الإرسال
                fetch('reset_request.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'email=' + encodeURIComponent(email) })
                    .then(response => response.json())
                    .then(data => showMessage(forgotPasswordModal, resetModalMessage, data.message, data.status === 'success'))
                    .catch(error => { console.error('Error:', error); showMessage(forgotPasswordModal, resetModalMessage, 'An error occurred.', false); });
            });
        }

        // --- و: إغلاق النوافذ المنبثقة ---
        if (messageModalClose) messageModalClose.onclick = () => messageModal.style.display = 'none';
        if (forgotCloseButton) forgotCloseButton.onclick = () => forgotPasswordModal.style.display = 'none';
        window.addEventListener('click', (e) => {
            if (e.target == forgotPasswordModal) forgotPasswordModal.style.display = 'none';
            if (e.target == messageModal) messageModal.style.display = 'none';
        });
    }
});
