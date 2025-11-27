class App {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initMobileMenu();
            this.initFAQ();
            this.initSmoothScroll();
        });
    }

    // Mobile Menu Management
    initMobileMenu() {
        this.mobileMenuButton = document.getElementById('mobileMenuButton');
        this.mobileMenuClose = document.getElementById('mobileMenuClose');
        this.mobileMenu = document.getElementById('mobileMenu');
        this.mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        this.mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

        if (this.mobileMenuButton) {
            this.mobileMenuButton.addEventListener('click', () => this.openMobileMenu());
        }
        if (this.mobileMenuClose) {
            this.mobileMenuClose.addEventListener('click', () => this.closeMobileMenu());
        }
        if (this.mobileMenuOverlay) {
            this.mobileMenuOverlay.addEventListener('click', () => this.closeMobileMenu());
        }

        this.mobileNavLinks.forEach(link => {
            link.addEventListener('click', () => this.closeMobileMenu());
        });
    }

    openMobileMenu() {
        this.mobileMenu.classList.add('open');
        this.mobileMenuOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    closeMobileMenu() {
        this.mobileMenu.classList.remove('open');
        this.mobileMenuOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    // FAQ Management
    initFAQ() {
        this.faqQuestions = document.querySelectorAll('.faq-question');

        if (this.faqQuestions.length > 0) {
            this.faqQuestions.forEach(question => {
                question.addEventListener('click', () => this.toggleFAQItem(question));
            });
        }
    }

    toggleFAQItem(clickedQuestion) {
        const faqItem = clickedQuestion.closest('.faq-item');
        const answer = faqItem.querySelector('.faq-answer');
        const icon = clickedQuestion.querySelector('i');

        // Close all other FAQ items
        this.closeAllFAQItemsExcept(faqItem);

        // Toggle current item
        if (faqItem.classList.contains('active')) {
            this.closeFAQItem(faqItem, answer, icon);
        } else {
            this.openFAQItem(faqItem, answer, icon);
        }
    }

    closeAllFAQItemsExcept(exceptItem) {
        document.querySelectorAll('.faq-item').forEach(item => {
            if (item !== exceptItem) {
                const answer = item.querySelector('.faq-answer');
                const icon = item.querySelector('.faq-question i');
                this.closeFAQItem(item, answer, icon);
            }
        });
    }

    openFAQItem(item, answer, icon) {
        item.classList.add('active');
        if (answer) answer.style.display = 'block';
        if (icon) icon.style.transform = 'rotate(180deg)';
    }

    closeFAQItem(item, answer, icon) {
        item.classList.remove('active');
        if (answer) answer.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    }

    // Smooth Scroll Management
    initSmoothScroll() {
        this.scrollAnchors = document.querySelectorAll('a[href^="#"]');

        this.scrollAnchors.forEach(anchor => {
            anchor.addEventListener('click', (e) => this.handleSmoothScroll(e, anchor));
        });
    }

    handleSmoothScroll(e, anchor) {
        e.preventDefault();
        const targetId = anchor.getAttribute('href');
        const target = document.querySelector(targetId);

        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Update URL without page jump
            history.pushState(null, null, targetId);
        }
    }

    // Utility method to close mobile menu (can be called from other components)
    closeAllMenus() {
        this.closeMobileMenu();
    }
}

// Initialize the app
new App();
