document.addEventListener('DOMContentLoaded', () => {

    // ==========================================================
    // القسم الأول: تحديث قائمة التنقل (Navigation) ديناميكياً
    // ==========================================================
    function updateUserNav() {
        fetch('get_session_status.php')
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok'); }
                return response.json();
            })
            .then(data => {
                const navLinksContainer = document.getElementById('user-nav-links');
                const mobileNavLinksContainer = document.getElementById('user-nav-links-mobile');
                let htmlContent = '', mobileHtmlContent = '';

                if (data.loggedin) {
                    const adminLink = data.isAdmin ? `<a href="admin_dashboard.php" class="admin-link">Admin Page</a>` : '';
                    htmlContent = `${adminLink}<span class="welcome-user">Welcome, ${data.userName}</span><a href="logout.php">Logout</a>`;
                    mobileHtmlContent = `${adminLink}<span class="welcome-user">Welcome, ${data.userName}</span><a href="logout.php">Logout</a>`;
                } else {
                    htmlContent = `<a href="sign.html">SignIn</a>`;
                    mobileHtmlContent = `<a href="sign.html">SignIn</a>`;
                }
                if (navLinksContainer) navLinksContainer.innerHTML = htmlContent;
                if (mobileNavLinksContainer) mobileNavLinksContainer.innerHTML = mobileHtmlContent;
            })
            .catch(error => {
                console.error('Error fetching session status:', error);
                const navLinksContainer = document.getElementById('user-nav-links');
                if (navLinksContainer) navLinksContainer.innerHTML = `<a href="sign.html">SignIn</a>`;
            });
    }
    // updateUserNav(); // You can enable this if you create the get_session_status.php file

    // ==========================================================
    // القسم الثاني: وظيفة قائمة الموبايل
    // ==========================================================
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });
    }

    // ==========================================================
    // القسم الثالث: فلترة الرحلات (في صفحة trips.php)
    // ==========================================================
    const searchInput = document.getElementById('filter-search');
    const regionFilter = document.getElementById('filter-region');
    const activityFilter = document.getElementById('filter-activity');
    const difficultyFilter = document.getElementById('filter-difficulty');
    const tripsGrid = document.getElementById('trips-grid');
    const noResultsMessage = document.getElementById('no-results');

    // This condition ensures the code only runs on the trips page
    if (tripsGrid && searchInput) {

        const tripCards = tripsGrid.getElementsByClassName('trip-card');

        function filterAndDisplayTrips() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRegion = regionFilter.value;
            const selectedActivity = activityFilter.value;
            const selectedDifficulty = difficultyFilter.value;
            let visibleCount = 0;

            for (const card of tripCards) {
                const name = (card.dataset.name || '').toLowerCase();
                const location = (card.dataset.location || '').toLowerCase();
                const region = card.dataset.region || '';
                const activity = card.dataset.activity || '';
                const difficulty = card.dataset.difficulty || '';

                const matchesSearch = name.includes(searchTerm) || location.includes(searchTerm);
                const matchesRegion = selectedRegion === 'all' || region === selectedRegion;
                const matchesActivity = selectedActivity === 'all' || activity === selectedActivity;
                const matchesDifficulty = selectedDifficulty === 'all' || difficulty === selectedDifficulty;

                if (matchesSearch && matchesRegion && matchesActivity && matchesDifficulty) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            }

            if (noResultsMessage) {
                noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        searchInput.addEventListener('keyup', filterAndDisplayTrips);
        regionFilter.addEventListener('change', filterAndDisplayTrips);
        activityFilter.addEventListener('change', filterAndDisplayTrips);
        difficultyFilter.addEventListener('change', filterAndDisplayTrips);

        const urlParams = new URLSearchParams(window.location.search);
        const searchTermFromUrl = urlParams.get('search');
        if (searchTermFromUrl) {
            searchInput.value = searchTermFromUrl;
            filterAndDisplayTrips();
        }
    }

    // ==========================================================
    // القسم الرابع: معالجة نموذج التواصل (في contact.php)
    // ==========================================================
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.textContent = 'Sending...';
            submitButton.disabled = true;
            const formData = new FormData(this);
            fetch('contact.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') { contactForm.reset(); }
                })
                .finally(() => {
                    submitButton.textContent = 'Send Message';
                    submitButton.disabled = false;
                });
        });
    }

    // ==========================================================
    // القسم الخامس: معالجة نموذج إضافة تقييم (في homee.php)
    // ==========================================================
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(reviewForm);

            fetch('submit_review.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        reviewForm.reset();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Submission Error:', error);
                    alert('An unexpected error occurred. Please try again.');
                });
        });
    }

    // ==========================================================
    // القسم السادس: معالجة نماذج تسجيل الدخول وإنشاء الحساب (في sign.html)
    // ==========================================================
    const container = document.getElementById('container');
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const signInForm = document.getElementById('signInForm');
    const signUpForm = document.getElementById('signUpForm');
    const messageModal = document.getElementById('messageModal');
    const modalMessageText = document.getElementById('modalMessageText');
    const messageModalClose = document.querySelector('#messageModal .close-button');

    // This condition ensures the code only runs on the sign-in/up page
    if (container && signUpButton && signInButton && signInForm && signUpForm) {

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
            signInForm.reset();
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
            signUpForm.reset();
        });

        function showMessage(message, isSuccess) {
            if (messageModal && modalMessageText) {
                modalMessageText.textContent = message;
                modalMessageText.style.color = isSuccess ? 'green' : 'red';
                messageModal.style.display = 'block';
            }
        }

        if (messageModalClose) {
            messageModalClose.onclick = () => { messageModal.style.display = 'none'; }
        }
        window.onclick = (event) => {
            if (event.target == messageModal) { messageModal.style.display = 'none'; }
        }

        signInForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('signin_process.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showMessage(data.message, true);
                        setTimeout(() => { window.location.href = data.redirectUrl; }, 1500);
                    } else {
                        showMessage(data.message, false);
                    }
                })
                .catch(error => showMessage('An unexpected error occurred.', false));
        });

        signUpForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('signin_process.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showMessage(data.message, true);
                        this.reset();
                        setTimeout(() => {
                            if (messageModal) messageModal.style.display = 'none';
                            container.classList.remove("right-panel-active");
                        }, 2000);
                    } else {
                        showMessage(data.message, false);
                    }
                })
                .catch(error => showMessage('An unexpected error occurred.', false));
        });
    }
});