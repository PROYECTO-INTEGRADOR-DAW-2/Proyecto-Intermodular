import añadirProductoAPI from './api.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const categoria   = document.getElementById("categoria").value;
            const nombre      = document.getElementById("nombre").value;
            const precio      = document.getElementById("precio").value;
            const talla       = document.getElementById("talla").value;
            const color       = document.getElementById("color").value;
            const stock       = document.getElementById("stock").value;
            const ajuste      = document.getElementById("ajuste").value;
            const sexo        = document.getElementById("sexo").value;
            const descripcion = document.getElementById("descripcion").value;
            const altura      = document.getElementById("altura").value;
            const deporte     = document.getElementById("deporte").value;
            const ofertaSeleccionada = document.querySelector('input[name="oferta"]:checked')?.value;

            let data = {categoria, nombre, precio,talla,color,stock,ajuste,sexo,descripcion,altura,deporte,ofertaSeleccionada};

            try {
                const result = await añadirProductoAPI(data);

                if (result.status === 'success' || result.message === 'Product added') {
                    alert('Producto añadido correctamente!');
                    form.reset();
                } else {
                    console.error('Error from server:', result);
                    alert('Hubo un error al añadir el producto. Revisa la consola para más detalles.');
                }

            } catch (error) {
                console.error('Error sending data:', error);
                alert('Error de conexión al intentar añadir el producto.');
            }
        });
    }
});