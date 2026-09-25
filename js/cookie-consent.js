// cookie-consent.js — SANTORO.
// Este sitio usa ÚNICAMENTE la cookie de sesión de PHP para el carrito
// (estrictamente necesaria). No hay analytics, ads ni tracking de terceros.
// Por eso este banner es informativo, no un selector de categorías de consentimiento:
// no hay categorías opcionales que activar/desactivar.

document.addEventListener('DOMContentLoaded', () => {
  const banner = document.getElementById('cookieBanner');
  const dismissBtn = document.getElementById('cookieBannerDismiss');
  if (!banner || !dismissBtn) return;

  const YA_VISTO = 'santoro_cookie_notice_seen';

  try {
    if (!localStorage.getItem(YA_VISTO)) {
      banner.hidden = false;
    }
  } catch (e) {
    // Si localStorage no está disponible, mostramos el aviso de todos modos.
    banner.hidden = false;
  }

  dismissBtn.addEventListener('click', () => {
    banner.hidden = true;
    try {
      localStorage.setItem(YA_VISTO, '1');
    } catch (e) {
      // Sin almacenamiento disponible, el aviso volverá a aparecer la próxima visita.
    }
  });
});
