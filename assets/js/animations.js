/**
 * ProGreenClean Animations
 * Scroll-triggered reveal animations
 */
(function() {
    'use strict';
    
    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
        // Make all elements visible immediately
        document.querySelectorAll('.pgc-animate, .pgc-animate-scale').forEach(el => {
            el.classList.add('pgc-visible');
        });
        return;
    }
    
    // Intersection Observer for scroll animations
    const initScrollAnimations = () => {
        const animatedElements = document.querySelectorAll('.pgc-animate, .pgc-animate-scale');
        
        if (!animatedElements.length) return;
        
        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('pgc-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        animatedElements.forEach(el => observer.observe(el));
    };
    
    // FAQ Accordion
    const initFaqAccordion = () => {
        const faqContainers = document.querySelectorAll('.pgc-faq-accordion');
        
        faqContainers.forEach(container => {
            const items = container.querySelectorAll('.pgc-faq-item');
            const toggleBtn = container.querySelector('.pgc-faq__toggle');
            const previewCount = parseInt(container.dataset.preview) || 4;
            
            items.forEach(item => {
                const question = item.querySelector('.pgc-faq-item__question');
                const answer = item.querySelector('.pgc-faq-item__answer');
                
                if (question && answer) {
                    question.addEventListener('click', () => {
                        const isOpen = item.classList.contains('pgc-faq-item--open');
                        
                        // Close all other items
                        items.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.remove('pgc-faq-item--open');
                                otherItem.querySelector('.pgc-faq-item__question').setAttribute('aria-expanded', 'false');
                            }
                        });
                        
                        // Toggle current item
                        item.classList.toggle('pgc-faq-item--open');
                        question.setAttribute('aria-expanded', !isOpen);
                    });
                }
            });
            
            // Toggle button for "View All"
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const hiddenItems = container.querySelectorAll('.pgc-faq-item--hidden');
                    const isExpanded = toggleBtn.classList.contains('pgc-faq__toggle--expanded');
                    
                    if (isExpanded) {
                        items.forEach((item, index) => {
                            if (index >= previewCount) {
                                item.classList.add('pgc-faq-item--hidden');
                            }
                        });
                        toggleBtn.textContent = 'View All FAQs';
                        toggleBtn.classList.remove('pgc-faq__toggle--expanded');
                    } else {
                        hiddenItems.forEach(item => item.classList.remove('pgc-faq-item--hidden'));
                        toggleBtn.textContent = 'Show Less';
                        toggleBtn.classList.add('pgc-faq__toggle--expanded');
                    }
                });
            }
        });
    };
    
    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initScrollAnimations();
            initFaqAccordion();
        });
    } else {
        initScrollAnimations();
        initFaqAccordion();
    }
})();
