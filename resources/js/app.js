import Sortable from 'sortablejs';
window.Sortable = Sortable;

// ===========================
// THEME CONTROLLER (localStorage only)
// ===========================

function initTheme() {
    // Default: light jika belum pernah di-set
    const isDark = localStorage.getItem('kai_theme') === 'dark';

    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    updateThemeIcons(isDark);
}

function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('kai_theme', isDark ? 'dark' : 'light');
    updateThemeIcons(isDark);
}

function updateThemeIcons(isDark) {
    const moonIcons = document.querySelectorAll('.theme-icon-moon');
    const sunIcons = document.querySelectorAll('.theme-icon-sun');
    
    moonIcons.forEach(el => {
        if (isDark) el.classList.add('hidden');
        else el.classList.remove('hidden');
    });
    
    sunIcons.forEach(el => {
        if (isDark) el.classList.remove('hidden');
        else el.classList.add('hidden');
    });
}

// Expose globally to window
window.toggleTheme = toggleTheme;
window.initTheme = initTheme;
window.updateThemeIcons = updateThemeIcons;

// Initialize when script loads and on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTheme);
} else {
    initTheme();
}

// Check every 30 seconds to automatically switch at 17:00 and 07:00 WIB
setInterval(initTheme, 30000);

// Disable pinch-to-zoom gestures (2+ fingers) on mobile browsers
document.addEventListener('gesturestart', function (e) {
    e.preventDefault();
});
document.addEventListener('gesturechange', function (e) {
    e.preventDefault();
});
document.addEventListener('gestureend', function (e) {
    e.preventDefault();
});
document.addEventListener('touchstart', function (e) {
    if (e.touches.length > 1) {
        e.preventDefault();
    }
}, { passive: false });
