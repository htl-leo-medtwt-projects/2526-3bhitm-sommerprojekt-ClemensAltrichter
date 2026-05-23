/* ═══════════════════════════════════════════
   nav.js — Aufklappbare Top Navigation
═══════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

  const nav    = document.querySelector('.top-nav');
  const header = document.querySelector('.nav-header');

  if (!nav || !header) return;

  // Auf/Zu beim Klick auf Header
  header.addEventListener('click', () => {
    nav.classList.toggle('open');
  });

  // Schließen bei Klick außerhalb
  document.addEventListener('click', (e) => {
    if (!nav.contains(e.target)) {
      nav.classList.remove('open');
    }
  });

  // Schließen beim Navigieren
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      nav.classList.remove('open');
    });
  });

});