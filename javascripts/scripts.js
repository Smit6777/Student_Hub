(function(){
    function showToast(message, type = 'success'){
        let toast = document.getElementById('studenthub-toast');
        if(!toast){
            toast = document.createElement('div');
            toast.id = 'studenthub-toast';
            toast.className = 'toast';
            document.body.appendChild(toast);
        }

        toast.className = `toast ${type}`;
        toast.textContent = message;
        toast.classList.add('show');

        clearTimeout(showToast.timeoutId);
        showToast.timeoutId = setTimeout(()=> toast.classList.remove('show'), 2800);
    }

    function safeStorageGet(key){
        try { return localStorage.getItem(key); } catch (e) { return null; }
    }

    function safeStorageSet(key, value){
        try { localStorage.setItem(key, value); } catch (e) {}
    }

    function initThemeToggle(){
        const btn = document.getElementById('theme-toggle');
        if(!btn) return;

        function updateButton(){
            const isDark = document.documentElement.classList.contains('dark-theme');
            btn.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
        }

        btn.addEventListener('click', function(){
            const isDark = document.documentElement.classList.toggle('dark-theme');
            safeStorageSet('theme', isDark ? 'dark' : 'light');
            updateButton();
        });

        const savedTheme = safeStorageGet('theme');
        if(savedTheme === 'dark'){
            document.documentElement.classList.add('dark-theme');
        }
        updateButton();
    }

    function initHamburger(){
        const hamb = document.querySelector('.hamburger');
        const navLinks = document.getElementById('nav-links');
        if(!hamb || !navLinks) return;

        hamb.addEventListener('click', function(){
            navLinks.classList.toggle('active');
        });

        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => navLinks.classList.remove('active'));
        });
    }

    function initFAQ(){
        const questions = document.querySelectorAll('.faq-question');
        if (!questions.length) return;

        questions.forEach(q => {
            q.addEventListener('click', function(){
                const item = q.closest('.faq-item');
                if(!item) return;
                item.classList.toggle('active');
                const answer = item.querySelector('.faq-answer');
                if(answer){
                    answer.style.maxHeight = item.classList.contains('active') ? answer.scrollHeight + 'px' : null;
                }
            });
        });
    }

    function initFormDrafts(){
        document.querySelectorAll('[data-draft-key]').forEach(form => {
            const key = form.dataset.draftKey;
            const saved = safeStorageGet(key);
            if(saved){
                try {
                    const values = JSON.parse(saved);
                    Object.entries(values).forEach(([fieldName, value]) => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if(field && field.type !== 'checkbox' && field.type !== 'radio'){
                            field.value = value;
                        }
                    });
                } catch (e) {}
            }

            form.addEventListener('input', function(){
                const data = {};
                const fields = form.querySelectorAll('input, textarea, select');
                fields.forEach(field => {
                    if(field.name){
                        data[field.name] = field.value;
                    }
                });
                safeStorageSet(key, JSON.stringify(data));
            });
        });
    }

    function initContactForm(){
        const form = document.getElementById('contact-form');
        if(!form) return;

        form.addEventListener('submit', function(event){
            event.preventDefault();
            const name = form.querySelector('[name="name"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim();
            const message = form.querySelector('[name="message"]').value.trim();

            if(!name || !email || !message){
                showToast('Please fill in all required contact fields.', 'error');
                return;
            }

            const existing = JSON.parse(safeStorageGet('studenthub_contact_messages') || '[]');
            existing.push({ name, email, message, time: new Date().toISOString() });
            safeStorageSet('studenthub_contact_messages', JSON.stringify(existing));
            form.reset();
            localStorage.removeItem('contact-form-draft');
            showToast('Message sent successfully. We will contact you soon!');
        });
    }

    function initFeedbackForm(){
        const form = document.getElementById('feedback-form');
        if(!form) return;

        form.addEventListener('submit', function(event){
            event.preventDefault();
            const name = form.querySelector('#fbName').value.trim();
            const email = form.querySelector('#fbEmail').value.trim();
            const subject = form.querySelector('#fbSubject').value.trim();
            const message = form.querySelector('#fbMessage').value.trim();

            if(!name || !email || !subject || !message){
                showToast('Please complete the feedback form before submitting.', 'error');
                return;
            }

            const existing = JSON.parse(safeStorageGet('studenthub_feedback') || '[]');
            existing.push({
                name,
                email,
                subject,
                message,
                createdAt: new Date().toISOString()
            });
            safeStorageSet('studenthub_feedback', JSON.stringify(existing));
            form.reset();
            localStorage.removeItem('feedback-form-draft');
            showToast('Thank you! Your feedback has been submitted.');
        });
    }

    function initRegisterForm(){
        const form = document.getElementById('register-form');
        if(!form) return;

        form.addEventListener('submit', function(event){
            event.preventDefault();
            const password = form.querySelector('#password').value;
            const confirmPassword = form.querySelector('#confirmPassword').value;
            const email = form.querySelector('#email').value.trim();

            if(password !== confirmPassword){
                showToast('Passwords do not match. Please try again.', 'error');
                return;
            }

            if(password.length < 6){
                showToast('Password must be at least 6 characters long.', 'error');
                return;
            }

            const existingUser = JSON.parse(safeStorageGet('studenthub_user') || 'null');
            if(existingUser && existingUser.email === email){
                showToast('This email is already registered.', 'error');
                return;
            }

            const user = {
                firstName: form.querySelector('#firstName').value.trim(),
                lastName: form.querySelector('#lastName').value.trim(),
                email,
                phone: form.querySelector('#phone').value.trim(),
                department: form.querySelector('#department').value,
                semester: form.querySelector('#semester').value,
                enrollmentNo: form.querySelector('#enrollmentNo').value.trim(),
                password
            };

            safeStorageSet('studenthub_user', JSON.stringify(user));
            form.reset();
            localStorage.removeItem('register-form-draft');
            showToast('Registration successful. Please log in now.');
            setTimeout(() => window.location.href = 'login.html', 1200);
        });
    }

    function initLoginForm(){
        const form = document.getElementById('login-form');
        if(!form) return;

        form.addEventListener('submit', function(event){
            event.preventDefault();
            const emailInput = form.querySelector('#username');
            const passwordInput = form.querySelector('#password');
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const user = JSON.parse(safeStorageGet('studenthub_user') || 'null');

            if(!user || user.email !== email || user.password !== password){
                showToast('Invalid email or password. Please check your details.', 'error');
                return;
            }

            safeStorageSet('studenthub_logged_in', 'true');
            safeStorageSet('studenthub_current_user', JSON.stringify(user));
            form.reset();
            localStorage.removeItem('login-form-draft');
            showToast('Login successful. Redirecting to dashboard...');
            setTimeout(() => window.location.href = 'dashboard.html', 1200);
        });
    }

    function initAll(){
        initThemeToggle();
        initHamburger();
        initFAQ();
        initFormDrafts();
        initContactForm();
        initFeedbackForm();
        initRegisterForm();
        initLoginForm();
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

/* ================= SLIDER, BANNER & MODAL IMPLEMENTATION ================= */
(function(){
    function safeStorageGet(key){
        try { return localStorage.getItem(key); } catch (e) { return null; }
    }

    function safeStorageSet(key, value){
        try { localStorage.setItem(key, value); } catch (e) {}
    }

    function initSlider(){
        const slider = document.querySelector('.slider');
        if(!slider) return;
        const track = slider.querySelector('.slider-track');
        const slides = Array.from(slider.querySelectorAll('.slide'));
        if(!track || slides.length === 0) return;

        const prev = slider.querySelector('.slider-prev');
        const next = slider.querySelector('.slider-next');
        let current = 0;
        let timer = null;

        function goTo(i){
            current = (i + slides.length) % slides.length;
            const offset = -current * 100;
            track.style.transform = `translateX(${offset}%)`;
            slides.forEach((s, idx) => s.classList.toggle('active', idx === current));
        }

        function nextSlide(){ goTo(current + 1); }
        function prevSlide(){ goTo(current - 1); }

        function resetTimer(){
            clearInterval(timer);
            timer = setInterval(nextSlide, 5000);
        }

        if(next) next.addEventListener('click', ()=>{ nextSlide(); resetTimer(); });
        if(prev) prev.addEventListener('click', ()=>{ prevSlide(); resetTimer(); });

        slider.addEventListener('mouseenter', ()=> clearInterval(timer));
        slider.addEventListener('mouseleave', ()=> resetTimer());

        track.style.transition = 'transform .5s ease';
        goTo(0);
        timer = setInterval(nextSlide, 5000);
    }

    function initBanner(){
        try{
            if(safeStorageGet('bannerDismissed') === 'true') return;
        } catch (e) {}

        const banner = document.createElement('div');
        banner.className = 'site-banner';
        banner.innerHTML = `<div class="site-banner-inner"><span class="banner-text">🔔 Campus Notice: Registration closes Friday at 5pm — check dashboard for details.</span><button class="banner-close" aria-label="Dismiss">✕</button></div>`;
        document.body.insertBefore(banner, document.body.firstChild);

        const btn = banner.querySelector('.banner-close');
        btn.addEventListener('click', ()=>{
            banner.style.display = 'none';
            try { localStorage.setItem('bannerDismissed', 'true'); } catch (e) {}
        });
    }

    function initModal(){
        if(document.getElementById('site-modal')) return;
        const modal = document.createElement('div');
        modal.id = 'site-modal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-backdrop" tabindex="-1"></div>
            <div class="modal-dialog" role="dialog" aria-modal="true">
                <button class="modal-close" aria-label="Close">✕</button>
                <div class="modal-body"><h3 id="modal-title">Notice</h3><p id="modal-content">This is a modal.</p><div class="modal-actions"><button class="modal-ok">OK</button></div></div>
            </div>`;
        document.body.appendChild(modal);

        modal.querySelector('.modal-close').addEventListener('click', ()=> modal.classList.remove('open'));
        modal.querySelector('.modal-backdrop').addEventListener('click', ()=> modal.classList.remove('open'));
        modal.querySelector('.modal-ok').addEventListener('click', ()=> modal.classList.remove('open'));

        document.addEventListener('click', function(e){
            const trg = e.target.closest('[data-modal-title]');
            if(!trg) return;
            const title = trg.getAttribute('data-modal-title') || 'Notice';
            const content = trg.getAttribute('data-modal-content') || '';
            const m = document.getElementById('site-modal');
            if(m){
                m.querySelector('#modal-title').textContent = title;
                m.querySelector('#modal-content').textContent = content;
                m.classList.add('open');
            }
        });
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){ initSlider(); initBanner(); initModal(); });
    } else {
        initSlider(); initBanner(); initModal();
    }
})();
