//API.js

export default async function añadirProducto(producto) {

    let response = await fetch(`../backend/routes/router.php?action=addProduct`, {
        method: "POST",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(producto)
    });

    if (!response.ok) {
        throw new Error(`Erro: ${response.statusText}`)
    }

    return response.json();

}
