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
        const productContainer = document.getElementById("product-data")

        if (product) {

            let div = document.createElement("div");

            div.innerHTML = `
                <h3>${product.Nombre}<h3>
                <p>${product.Precio}€</p>
                <p>Categoria: ${product.Categoria}</p>
                <p>Deporte: ${product.Deporte}</p>
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

async function getComments(idProduct) {
    try {
        const comments = await API.getDBComments(idProduct);
        //const users = await API.getDBUsersCommented(idProduct)
        console.log(comments)
        //console.log(users)

        const commentsContainer = document.getElementById("comments")


        if (comments) {
            comments.forEach(comment => {
                let div = document.createElement("div");
                div.classList.add("comment","col-6","d-flex","flex-column");        

                div.innerHTML = `
                    <h3>${comment.comentari}<h3>
                    <p>${comment.valoracion}<p>
                `
                commentsContainer.append(div)

                return true;

            })
        } else {
            console.error('Error from server:', comments);
            alert('Hubo un error al mostrar los comentarios');
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar mostrar los comentarios');
        return false;
    }
}
