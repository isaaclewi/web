// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    });
});

// Theme switcher functionality
const themeSwitcher = {
    init() {
        this.currentTheme = 'default';
        this.themes = {
            'default': {
                primary: '#3B82F6',
                secondary: '#8B5CF6'
            },
            'dark': {
                primary: '#8B5CF6',
                secondary: '#3B82F6'
            },
            'ocean': {
                primary: '#06B6D4',
                secondary: '#3B82F6'
            }
        };
        
        this.setupEventListeners();
    },
    
    setupEventListeners() {
        // You can add theme switcher buttons in your UI and bind them here
        // Example:
        // document.querySelector('.theme-default').addEventListener('click', () => this.setTheme('default'));
        // document.querySelector('.theme-dark').addEventListener('click', () => this.setTheme('dark'));
        // document.querySelector('.theme-ocean').addEventListener('click', () => this.setTheme('ocean'));
    },
    
    setTheme(themeName) {
        if (!this.themes[themeName]) return;
        
        this.currentTheme = themeName;
        const theme = this.themes[themeName];
        
        // Update CSS variables
        document.documentElement.style.setProperty('--primary-color', theme.primary);
        document.documentElement.style.setProperty('--secondary-color', theme.secondary);
        
        // Store preference
        localStorage.setItem('synthriTheme', themeName);
    },
    
    loadSavedTheme() {
        const savedTheme = localStorage.getItem('synthriTheme');
        if (savedTheme && this.themes[savedTheme]) {
            this.setTheme(savedTheme);
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    themeSwitcher.init();
    themeSwitcher.loadSavedTheme();
    
    // Initialize animations
    const animateOnScroll = () => {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                }
            });
        }, { threshold: 0.1 });
        
        elements.forEach(el => observer.observe(el));
    };
    
    animateOnScroll();
});