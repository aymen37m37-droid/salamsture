// SALAM REAL ESTATE - MAIN JS

document.addEventListener('DOMContentLoaded', function () {

    // ===== HERO SLIDER =====
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let sliderInterval;

    function goToSlide(n) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        currentSlide = (n + slides.length) % slides.length;
        if (slides[currentSlide]) slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    function nextSlide() { goToSlide(currentSlide + 1); }
    function prevSlide() { goToSlide(currentSlide - 1); }

    function startSlider() {
        if (slides.length < 2) return;
        sliderInterval = setInterval(nextSlide, 5000);
    }

    function resetSlider() {
        clearInterval(sliderInterval);
        startSlider();
    }

    if (slides.length > 0) {
        goToSlide(0);
        startSlider();

        document.querySelectorAll('.slider-dot').forEach((dot, i) => {
            dot.addEventListener('click', () => { goToSlide(i); resetSlider(); });
        });

        const prevBtn = document.querySelector('.slider-arrow.prev');
        const nextBtn = document.querySelector('.slider-arrow.next');
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetSlider(); });
        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetSlider(); });

        let touchStartX = 0;
        const slider = document.querySelector('.hero-slider');
        if (slider) {
            slider.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; });
            slider.addEventListener('touchend', e => {
                const diff = touchStartX - e.changedTouches[0].screenX;
                if (Math.abs(diff) > 50) { diff > 0 ? nextSlide() : prevSlide(); resetSlider(); }
            });
        }
    }

    // ===== MOBILE MENU =====
    const menuBtn = document.getElementById('mobileMenuBtn');
    const nav = document.getElementById('mainNav');
    if (menuBtn && nav) {
        menuBtn.addEventListener('click', () => {
            nav.classList.toggle('open');
            const icon = menuBtn.querySelector('i');
            if (icon) { icon.classList.toggle('fa-bars'); icon.classList.toggle('fa-times'); }
        });
        document.addEventListener('click', (e) => {
            if (!menuBtn.contains(e.target) && !nav.contains(e.target)) {
                nav.classList.remove('open');
            }
        });
    }

    // ===== SCROLL TO TOP =====
    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('visible', window.scrollY > 300);
        });
        scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // ===== COUNTER ANIMATION =====
    const counters = document.querySelectorAll('.stat-number[data-target]');
    if (counters.length > 0 && window.IntersectionObserver) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-target'));
                    const suffix = el.getAttribute('data-suffix') || '';
                    let start = 0;
                    const duration = 1500;
                    const step = target / (duration / 16);
                    const timer = setInterval(() => {
                        start += step;
                        if (start >= target) { start = target; clearInterval(timer); }
                        el.textContent = Math.round(start).toLocaleString() + suffix;
                    }, 16);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.3 });
        counters.forEach(c => observer.observe(c));
    }

    // ===== PROJECT FILTER =====
    const filterTabs = document.querySelectorAll('.filter-tab');
    const projectCards = document.querySelectorAll('.project-card[data-status]');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-filter');
            projectCards.forEach(card => {
                card.style.display = (filter === 'all' || card.getAttribute('data-status') === filter) ? '' : 'none';
            });
        });
    });

    // ===== IMAGE PREVIEW =====
    document.querySelectorAll('.img-upload-input').forEach(input => {
        input.addEventListener('change', function () {
            const preview = document.getElementById(this.dataset.preview);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ===== SCROLL ANIMATIONS =====
    if (window.IntersectionObserver) {
        const animateEls = document.querySelectorAll('.service-card, .project-card, .team-card, .whyus-card, .vm-card, .stat-item');
        const anim = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.style.opacity = '1';
                    e.target.style.transform = 'translateY(0)';
                    anim.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        animateEls.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            anim.observe(el);
        });
    }

    // ===== CONFIRM DELETE =====
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.')) {
                e.preventDefault();
            }
        });
    });

    // ===== CONTACT FORM =====
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function () {
            const btn = this.querySelector('[type=submit]');
            if (btn) { btn.disabled = true; btn.textContent = 'جاري الإرسال...'; }
        });
    }

    // ===== ADMIN: File upload label =====
    document.querySelectorAll('.upload-area').forEach(area => {
        const input = area.querySelector('input[type=file]');
        if (input) {
            area.addEventListener('click', () => input.click());
            input.addEventListener('change', function () {
                if (this.files.length > 0) {
                    area.querySelector('span') && (area.querySelector('span').textContent = this.files.length > 1 ? `تم اختيار ${this.files.length} صور` : this.files[0].name);
                }
            });
        }
    });
});
