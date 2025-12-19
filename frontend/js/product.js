import * as API from './api.js';

document.addEventListener('DOMContentLoaded', async (event) => {
    event.preventDefault();

    const params = new URLSearchParams(window.location.search);

    if(params.has("productId")) {
        const idProduct = params.get("productId");

        let product = await getProduct(idProduct);

        if(product) {
            //Acceder y renderizar comentarios
        }
    }

    

})


async function getProduct(idProduct) {
    try {
        const product = await API.getDBProduct(idProduct);
        console.log(product)
        const productContainer = document.getElementById("product-main-data-container")

        if (product) {

            let div = document.createElement("div");

            div.innerHTML = `
                <h3>${product.Nombre}<h3>
                <p>${product.Precio}<p>
            `
            productContainer.append(div)

            return true;

        } else {
            console.error('Error from server:', product);
            alert('Hubo un error al añadir el producto. Revisa la consola para más detalles.');
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar mostrar el producto.');
        return false;
    }
}
