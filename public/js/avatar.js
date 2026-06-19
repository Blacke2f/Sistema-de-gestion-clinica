/**
 * Generador de avatares local — reemplaza ui-avatars.com
 * Genera un canvas con las iniciales del nombre en el color especificado.
 * Uso: <img data-avatar="Juan Pérez" data-bg="#0d6efd" data-size="32">
 */
(function() {
    function generarAvatar(nombre, bg, size) {
        bg   = bg   || '#0d6efd';
        size = size || 32;

        // Obtener iniciales (máximo 2)
        const palabras  = (nombre || '?').trim().split(/\s+/);
        const iniciales = palabras.length >= 2
            ? (palabras[0][0] + palabras[1][0]).toUpperCase()
            : (palabras[0][0] || '?').toUpperCase();

        const canvas  = document.createElement('canvas');
        canvas.width  = size;
        canvas.height = size;
        const ctx     = canvas.getContext('2d');

        // Fondo
        ctx.fillStyle = bg;
        ctx.beginPath();
        ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
        ctx.fill();

        // Texto
        ctx.fillStyle   = '#ffffff';
        ctx.font        = `bold ${Math.floor(size * 0.38)}px Arial, sans-serif`;
        ctx.textAlign   = 'center';
        ctx.textBaseline= 'middle';
        ctx.fillText(iniciales, size / 2, size / 2 + 1);

        return canvas.toDataURL('image/png');
    }

    // Reemplazar todas las imágenes con data-avatar al cargar
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('img[data-avatar]').forEach(function(img) {
            const nombre = img.getAttribute('data-avatar') || '';
            const bg     = img.getAttribute('data-bg')     || '#0d6efd';
            const size   = parseInt(img.getAttribute('data-size') || '32') * 2; // retina
            img.src = generarAvatar(nombre, bg, size);
        });
    });

    // Exponer globalmente para uso dinámico
    window.generarAvatar = generarAvatar;
})();
