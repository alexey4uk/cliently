class App {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initTheme();
            this.initEventListeners();
        });
    }

    // Theme Management
    initTheme() {
        this.applyInitialTheme();
        this.watchSystemThemeChanges();
    }

    applyInitialTheme() {
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            this.setTheme('dark');
        } else {
            this.setTheme('light');
        }
    }

    setTheme(theme) {
        const isDark = theme === 'dark';

        // Apply to document
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', theme);

        // Update theme icons
        this.toggleThemeIcons(isDark);

        // Dispatch custom event for other components
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    toggleThemeIcons(isDark) {
        const elements = {
            light: [
                'theme-light-icon',
                'theme-light-icon-desktop'
            ],
            dark: [
                'theme-dark-icon',
                'theme-dark-icon-desktop'
            ]
        };

        // Show/hide light icons
        elements.light.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.toggle('hidden', isDark);
            }
        });

        // Show/hide dark icons
        elements.dark.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.toggle('hidden', !isDark);
            }
        });
    }

    toggleTheme() {
        const isDark = document.documentElement.classList.contains('dark');
        this.setTheme(isDark ? 'light' : 'dark');
    }

    watchSystemThemeChanges() {
        // Only watch if user hasn't explicitly set a preference
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                this.setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Menu Management
    initEventListeners() {
        this.initThemeToggleListeners();
        this.initMenuListeners();
        this.initClickOutsideListeners();
    }

    initThemeToggleListeners() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeToggleDesktop = document.getElementById('theme-toggle-desktop');

        [themeToggle, themeToggleDesktop].forEach(element => {
            if (element) {
                element.addEventListener('click', () => this.toggleTheme());
            }
        });
    }

    initMenuListeners() {
        // Mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Mobile user menu
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');

        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', () => {
                userMenu.classList.toggle('hidden');
            });
        }

        // Desktop user menu
        const userMenuButtonDesktop = document.getElementById('user-menu-button-desktop');
        const userMenuDesktop = document.getElementById('user-menu-desktop');

        if (userMenuButtonDesktop && userMenuDesktop) {
            userMenuButtonDesktop.addEventListener('click', () => {
                userMenuDesktop.classList.toggle('hidden');
            });
        }
    }

    initClickOutsideListeners() {
        document.addEventListener('click', (event) => {
            this.handleClickOutside(event);
        });
    }

    handleClickOutside(event) {
        // Mobile user menu
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        if (userMenuButton && userMenu &&
            !userMenuButton.contains(event.target) &&
            !userMenu.contains(event.target)) {
            userMenu.classList.add('hidden');
        }

        // Desktop user menu
        const userMenuButtonDesktop = document.getElementById('user-menu-button-desktop');
        const userMenuDesktop = document.getElementById('user-menu-desktop');
        if (userMenuButtonDesktop && userMenuDesktop &&
            !userMenuButtonDesktop.contains(event.target) &&
            !userMenuDesktop.contains(event.target)) {
            userMenuDesktop.classList.add('hidden');
        }

        // Mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu &&
            !mobileMenuButton.contains(event.target) &&
            !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
        }
    }
}

// Initialize the app
new App();
