// animations.js — SANTORO.
// 1) Reveal on scroll (estilo Apple: fade + translateY, IntersectionObserver)
// 2) Orden de productos por precio, del lado del cliente, sin recargar la página
// 3) Cursor personalizado sutil

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- 1. REVEAL ON SCROLL ---------- */
  const targets = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window && targets.length > 0) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );
    targets.forEach((el) => observer.observe(el));
  } else {
    targets.forEach((el) => el.classList.add('is-visible'));
  }

  /* ---------- 2. ORDENAR POR PRECIO (sin recargar) ---------- */
  const selectOrden = document.getElementById('ordenar');
  if (selectOrden) {
    selectOrden.addEventListener('change', () => {
      const modo = selectOrden.value;

      document.querySelectorAll('[data-ordenable="true"]').forEach((grid) => {
        const tarjetas = Array.from(grid.querySelectorAll('.tarjeta-producto'));

        if (modo === 'default') {
          tarjetas.forEach((t) => t.style.order = '');
          return;
        }

        tarjetas
          .map((t) => ({
            el: t,
            precio: parseFloat(t.querySelector('.precio')?.dataset.precio || 0),
          }))
          .sort((a, b) => (modo === 'asc' ? a.precio - b.precio : b.precio - a.precio))
          .forEach((item, i) => {
            item.el.style.order = i;
          });

        grid.style.display = 'grid';
      });
    });
  }

  /* ---------- 3. CURSOR PERSONALIZADO (sutil) ---------- */
  const cursorDot = document.getElementById('cursorDot');
  if (cursorDot && window.matchMedia('(hover: hover)').matches) {
    let mx = 0, my = 0, cx = 0, cy = 0;
    window.addEventListener('mousemove', (e) => { mx = e.clientX; my = e.clientY; });

    function raf() {
      cx += (mx - cx) * 0.2;
      cy += (my - cy) * 0.2;
      cursorDot.style.transform = `translate(${cx}px, ${cy}px) translate(-50%, -50%)`;
      requestAnimationFrame(raf);
    }
    raf();

    document.querySelectorAll('.tarjeta-producto, .hero-cta, a').forEach((el) => {
      el.addEventListener('mouseenter', () => cursorDot.classList.add('is-active'));
      el.addEventListener('mouseleave', () => cursorDot.classList.remove('is-active'));
    });
  } else if (cursorDot) {
    cursorDot.style.display = 'none';
  }
});
