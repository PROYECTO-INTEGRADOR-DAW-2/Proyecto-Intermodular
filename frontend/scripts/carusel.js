let desplazarIzquierda = document.getElementById("despl-izq");
let desplazarDerecha = document.getElementById("despl-der");
let products = document.getElementById('products');

const cantidadScroll = 1220;

desplazarDerecha.addEventListener('click', () => {
    products.scrollBy({
        left: cantidadScroll,
        behavior: "smooth"
    })
})

desplazarIzquierda.addEventListener('click', () => {
    products.scrollBy({
        left: -cantidadScroll,
        behavior: "smooth"
    })
})
