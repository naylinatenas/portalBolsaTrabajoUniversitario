document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnModoOscuro');
  const icono = document.getElementById('iconoModo');
  const body = document.body;

  if (localStorage.getItem('modoOscuro') === 'true') {
    body.classList.add('modo-oscuro');
    icono.classList.replace('bi-moon-fill', 'bi-sun-fill');
  }

  btn.addEventListener('click', () => {
    body.classList.toggle('modo-oscuro');
    const activo = body.classList.contains('modo-oscuro');
    icono.classList.replace(activo ? 'bi-moon-fill' : 'bi-sun-fill', activo ? 'bi-sun-fill' : 'bi-moon-fill');
    localStorage.setItem('modoOscuro', activo);
  });
});
