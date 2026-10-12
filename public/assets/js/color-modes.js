(() => {
  'use strict'

  // Enforce Light / White theme across the entire application
  localStorage.setItem('theme', 'light');
  document.documentElement.setAttribute('data-bs-theme', 'light');

  // Prevent any OS dark mode media query from switching theme
  window.addEventListener('DOMContentLoaded', () => {
    localStorage.setItem('theme', 'light');
    document.documentElement.setAttribute('data-bs-theme', 'light');
  });
})();