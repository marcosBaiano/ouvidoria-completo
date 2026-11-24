 // Smooth scroll para links internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Animações ao scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'slideInUp 0.6s ease forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.beneficio-card, .principio-card, .funciona-step').forEach(el => {
            observer.observe(el);
        });

        // Estilo para animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Highlight do link ativo na navbar
        window.addEventListener('scroll', () => {
            const scrollPosition = window.scrollY;
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    const targetPosition = target.offsetTop;
                    const targetHeight = target.offsetHeight;
                    if (scrollPosition >= targetPosition - 200 && 
                        scrollPosition < targetPosition + targetHeight - 200) {
                        document.querySelectorAll('.navbar-link').forEach(l => l.style.color = 'var(--gray-700)');
                        link.style.color = 'var(--primary)';
                        link.style.fontWeight = '600';
                    }
                }
            });
        });