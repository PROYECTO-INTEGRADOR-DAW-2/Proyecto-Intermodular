//API.js

export async function addDBProduct(producto) {

    let response = await fetch(`../backend/routes/router.php?action=addProduct`, {
        method: "POST",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(producto)
    });

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}

export async function getDBProducts() {

    let response = await fetch(`../backend/routes/router.php?action=getProducts`);

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}

export async function getDBProduct(idProduct) {

    let response = await fetch(`../backend/routes/router.php?action=getProduct&idProduct=${idProduct}`);

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}
