import * as API from './api.js';

document.addEventListener('DOMContentLoaded', async () => {
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const categoria = document.getElementById("categoria").value;
            const nombre = document.getElementById("nombre").value;
            const precio = document.getElementById("precio").value;
            const talla = document.getElementById("talla").value;
            const color = document.getElementById("color").value;
            const stock = document.getElementById("stock").value;
            const ajuste = document.getElementById("ajuste").value;
            const sexo = document.getElementById("sexo").value;
            const descripcion = document.getElementById("descripcion").value;
            const altura = document.getElementById("altura").value;
            const deporte = document.getElementById("deporte").value;
            const oferta = document.querySelector('input[name="oferta"]:checked')?.value;

            let product = { categoria, nombre, precio, talla, color, stock, ajuste, sexo, descripcion, altura, deporte, oferta };

            try {
                const result = await API.addDBProduct(product);

                if (result) {
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



    await getProducts();

});

async function getProducts() {
    try {
        const products = await API.getDBProducts();
        const productsList = document.getElementById("products")

        if (products) {
            products.forEach(product => {
                let div = document.createElement("div");

                div.innerHTML = `
                    <h3>${product.Nombre}</h3>
                    <p>${product.Precio}</p>
                    <a href="producto.html?productId=${product.id}">Ver más</a>
                `
                productsList.append(div)
            })
        } else {
            console.error('Error from server:', products);
            alert('Hubo un error al mostrar los productos. Revisa la consola para más detalles.');
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar mostrar los productos');
    }
}
