/* Theme toggle JS: handles button state and persistence */
(function(){
    function initThemeToggle(){
        const btn = document.getElementById('theme-toggle');
        if(!btn) return;

        function updateButton(){
            if(document.documentElement.classList.contains('dark-theme')){
                btn.textContent = '☀️ Light Mode';
            } else {
                btn.textContent = '🌙 Dark Mode';
            }
        }

        btn.addEventListener('click', function(){
            const isDark = document.documentElement.classList.toggle('dark-theme');
            try{ localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch(e){}
            updateButton();
        });

        updateButton();
    }

    function initHamburger(){
        const hamb = document.querySelector('.hamburger');
        const navLinks = document.getElementById('nav-links');
        if(!hamb || !navLinks) return;
        hamb.addEventListener('click', function(){
            navLinks.classList.toggle('active');
        });
    }

    function initFAQ(){
        const questions = document.querySelectorAll('.faq-question');
        if(!questions) return;
        questions.forEach(q => {
            q.addEventListener('click', function(){
                const item = q.closest('.faq-item');
                if(!item) return;
                item.classList.toggle('active');
                const answer = item.querySelector('.faq-answer');
                if(answer){
                    if(item.classList.contains('active')){
                        answer.style.maxHeight = answer.scrollHeight + 'px';
                    } else {
                        answer.style.maxHeight = null;
                    }
                }
            });
        });
    }

    function initAll(){
        initThemeToggle();
        initHamburger();
        initFAQ();
        initSlider();
        initBanner();
        initModal();
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

/* ================= SLIDER, BANNER & MODAL IMPLEMENTATION ================= */
(function(){
    function initSlider(){
        const slider = document.querySelector('.slider');
        if(!slider) return;
        const track = slider.querySelector('.slider-track');
        const slides = Array.from(slider.querySelectorAll('.slide'));
        const prev = slider.querySelector('.slider-prev');
        const next = slider.querySelector('.slider-next');
        let current = 0;
        let timer = null;

        function goTo(i){
            current = (i + slides.length) % slides.length;
            const offset = -current * 100;
            track.style.transform = `translateX(${offset}%)`;
            slides.forEach((s,idx) => s.classList.toggle('active', idx===current));
        }

        function nextSlide(){ goTo(current+1); }
        function prevSlide(){ goTo(current-1); }

        if(next) next.addEventListener('click', ()=>{ nextSlide(); resetTimer(); });
        if(prev) prev.addEventListener('click', ()=>{ prevSlide(); resetTimer(); });

        slider.addEventListener('mouseenter', ()=> clearInterval(timer));
        slider.addEventListener('mouseleave', ()=> timer = setInterval(nextSlide, 5000));

        function resetTimer(){ clearInterval(timer); timer = setInterval(nextSlide, 5000); }

        // init
        track.style.transition = 'transform .5s ease';
        goTo(0);
        timer = setInterval(nextSlide, 5000);
    }

    function initBanner(){
        try{
            if(localStorage.getItem('bannerDismissed') === 'true') return;
        }catch(e){}
        const banner = document.createElement('div');
        banner.className = 'site-banner';
        banner.innerHTML = `<div class="site-banner-inner"><span class="banner-text">🔔 Campus Notice: Registration closes Friday at 5pm — check dashboard for details.</span><button class="banner-close" aria-label="Dismiss">✕</button></div>`;
        document.body.insertBefore(banner, document.body.firstChild);
        const btn = banner.querySelector('.banner-close');
        btn.addEventListener('click', ()=>{
            banner.style.display = 'none';
            try{ localStorage.setItem('bannerDismissed','true'); }catch(e){}
        });
    }

    function initModal(){
        // create reusable modal markup
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

        // global open triggers: elements with data-modal-target attribute
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

    // initialize when DOM ready (also safe if called twice)
    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', function(){ initSlider(); initBanner(); initModal(); });
    } else {
        initSlider(); initBanner(); initModal();
    }

})();