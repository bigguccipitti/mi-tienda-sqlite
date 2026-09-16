// animations.js
// Revela elementos con fade + desplazamiento suave al entrar en pantalla,
// usando el mismo lenguaje de movimiento que Apple usa en sus páginas de producto:
// fade + translateY con easing "easeOutExpo", activado por scroll real (no scroll-jacking).

document.addEventListener('DOMContentLoaded', () => {
  const targets = document.querySelectorAll('.reveal, .reveal-stagger');

  if (!('IntersectionObserver' in window) || targets.length === 0) {
    targets.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.15,
      rootMargin: '0px 0px -40px 0px',
    }
  );

  targets.forEach((el) => observer.observe(el));
});
