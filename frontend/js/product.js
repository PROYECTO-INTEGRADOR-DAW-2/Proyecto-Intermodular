import * as API from './api.js';
let SESSION_USER = null;
let formMode = "add";
const params = new URLSearchParams(window.location.search);

document.addEventListener('DOMContentLoaded', async (event) => {

    SESSION_USER = await getSessionUser(); // aquí s
    if (params.has("productId")) {
        const idProduct = params.get("productId");

        let product = await getProduct(idProduct);

        if (product) {
            addResetHandler();

            let commentsResult = await getComments(idProduct);

            const addCommentForm = document.getElementById("add-comment-form");

            addCommentForm.addEventListener("submit", async (event) => {
                event.preventDefault();

                const valoracion = document.getElementById("valoracion").value;
                const comentari = document.getElementById("comment").value;
                const id_producte = idProduct;
                const id_usuari = SESSION_USER?.id;
                const now = new Date();
                const data = now.toISOString().replace(/\.\d{3}Z$/, '+00:00');
                if (id_usuari) {
                    
                    if(formMode === "add") {
                        let payload = { id_producte, id_usuari, comentari, valoracion, data };
                        let addCommentResult = await addComment(payload)
                    } else if (formMode === "edit") {
                        let id = document.getElementById("id-comment").value; 
                        let payload = {id, id_producte, id_usuari, comentari, valoracion, data };
                        let editCommentResult = await editComment(payload)
                    }
                    
                } else {
                    alert("Debes de iniciar sesion para comentar")
                }


            })
        }
    } else {
        alert("No se puede acceder a los datos del producto")
    }



})

//Obtenemos la informacion del producto para aplicarla en la plantilla(Producto.html)
async function getProduct(idProduct) {
    try {
        const product = await API.getDBProduct(idProduct);
        console.log(product)
        const productContainer = document.getElementById("product-data")

        if (product) {

            let div = document.createElement("div");

            div.innerHTML = `
                <h3>${product.Nombre}</h3>
                <h4>${product.Precio}€</h4>
                <h4>Categoria: ${product.Categoria}</h4>
                <h4>Deporte: ${product.Deporte}</h4>
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

//Funcion para obtener los comentarios del producto
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
                div.classList.add("comment", "col-6", "d-flex", "flex-column", "p-3");
                div.id = `comment-${comment.id}`;

                div.innerHTML = `
                    
                    <h6>Usuario ${comment.id_usuari}</h6>
                    <p>${comment.valoracion} estrellas</p>
                    <p>${comment.comentari}</p>
                    ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="edit"><span>edit</span></button>' : ""}
                    ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="delete"><span>delete</span></button>' : ""}
                `

                commentsContainer.append(div)

                if(div.querySelector(".edit") !== null) {
                    let button = div.querySelector(".edit");
                    button.addEventListener("click",(event) => {
                        event.preventDefault();
                        formMode = "edit"
                        fillFormData(comment);
                    })
                }

                if(div.querySelector(".delete") !== null) {
                    let button = div.querySelector(".delete");
                    button.addEventListener("click",(event) => {
                        event.preventDefault();
                        let confirmacion = confirm("Deseas eliminar el comentario?");

                        if (confirmacion) deleteComment(comment);

                    })
                }

            })
            return true;
        } else {
            console.error('Error from server:', comments);
            alert('Hubo un error al mostrar los comentarios');
            return false;
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar mostrar los comentarios');
        return false;
    }
}

//Funcion para añadir comentario
async function addComment(comment) {
    try {
        const addComment = await API.addDBComment(comment);
        const commentsContainer = document.getElementById("comments")

        if (addComment.errormsg === undefined) {

            let div = document.createElement("div");

            div.classList.add("comment", "col-6", "d-flex", "flex-column", "p-3");
            console.log(addComment.body.id)
            div.id = addComment.body.id;

            div.innerHTML = `
                <h6>Usuario ${comment.id_usuari}</h6>
                <p>${comment.valoracion} estrellas</p>
                <p>${comment.comentari}</p>
                ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="edit"><span>edit</span></button>' : ""}
                ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="delete"><span>delete</span></button>' : ""}
            `

            commentsContainer.append(div)

            if(div.querySelector(".edit") !== null) {
                let button = div.querySelector(".edit");
                button.addEventListener("click",(event) => {
                    event.preventDefault();
                    formMode = "edit"
                    fillFormData(comment);
                })
            }

            if(div.querySelector(".delete") !== null) {
                let button = div.querySelector(".delete");
                button.addEventListener("click",(event) => {
                    event.preventDefault();
                    let confirmacion = confirm("Deseas eliminar el comentario?");

                    if (confirmacion) deleteComment(comment);

                })
            }

            return true;

        } else {
            console.error('Error from server:', addComment.errormsg);
            alert('Hubo un error al añadir el comentario. Revisa la consola para más detalles.');
            return false;
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar añadir el comentario.');
        return false;
    }
}

//Funcion para editar comentario
async function editComment(comment) {
    try {
        const editComment = await API.editDBComment(comment);
        const commentsContainer = document.getElementById("comments")
        console.log(editComment)

        if (editComment) {

            let div = document.createElement("div");

            div.classList.add("comment", "col-6", "d-flex", "flex-column", "p-3");

            div.id = `comment-${comment.id}`;

            div.innerHTML = `
                <h6>Usuario ${comment.id_usuari}</h6>
                <p>${comment.valoracion} estrellas</p>
                <p>${comment.comentari}</p>
                ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="edit"><span>edit</span></button>' : ""}
                ${SESSION_USER.admin == true ||SESSION_USER?.id && comment.id_usuari === SESSION_USER.id ? '<button class="delete"><span>delete</span></button>' : ""}
            `

            let oldComment = commentsContainer.querySelector(`#comment-${comment.id}`);

            oldComment.replaceWith(div);

            if(div.querySelector(".edit") !== null) {
                let button = div.querySelector(".edit");
                button.addEventListener("click",(event) => {
                    event.preventDefault();
                    formMode = "edit"
                    fillFormData(comment);
                })
            }

            if(div.querySelector(".delete") !== null) {
                let button = div.querySelector(".delete");
                button.addEventListener("click",(event) => {
                    event.preventDefault();
                    let confirmacion = confirm("Deseas eliminar el comentario?");

                    if (confirmacion) deleteComment(comment);

                })
            }

            return true;

        } else {
            console.error('Error from server:', editComment);
            alert('Hubo un error al editar el comentario. Revisa la consola para más detalles.');
            return false;
        }
    } catch (error) {
        console.error('Error sending data:', error);
        alert('Error de conexión al intentar editar el comentario.');
        return false;
    }
}

//Funcion para completar el formulario para edicion de comentario
function fillFormData(comment) {
    let headerCommentSection = document.getElementById("comment-header-section");
    headerCommentSection.textContent = "Editar opinion";

    if (params.has("productId")) {
        let idComment = document.getElementById("id-comment");
        let valoracion = document.getElementById("valoracion");
        let comentari = document.getElementById("comment");

        idComment.value = comment.id;
        valoracion.value = comment.valoracion;
        comentari.value = comment.comentari;
    }
}

//Funcion para resetear formulario
function addResetHandler() {
    let resetBtn = document.getElementById("add-comment-form").querySelector('input[type="reset"]');
    resetBtn.addEventListener("click",() => {
        formMode = "add";
        let headerCommentSection = document.getElementById("comment-header-section");
        headerCommentSection.textContent = "Añade una opinion";
    })
}

//Funcion para obtener el usuario autenticado
async function getSessionUser() {

    if (sessionStorage.getItem("user") !== null) {
        return JSON.parse(sessionStorage.getItem("user"))
    } else {
        try {
            let userData = await API.getDBSessionUser();

            if (userData.errormsg === undefined) {
                sessionStorage.setItem("user", JSON.stringify({ id: userData.id, nom_usuari: userData.nom_usuari }))

                return {
                    id: userData.id,
                    nom_usuari: userData.nom_usuari
                }

            } else {
                return false
            }
        } catch (error) {
            return false;
        }
    }



}
