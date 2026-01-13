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

export async function addDBComment(comment) {

    let response = await fetch(`../backend/routes/router.php?action=addComment`, {
        method: "POST",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(comment)
    });

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}

export async function editDBComment(comment) {

    let response = await fetch(`../backend/routes/router.php?action=editComment`, {
        method: "PATCH",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(comment)
    });

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}

export async function deleteDBComment(comment) {

    let response = await fetch(`../backend/routes/router.php?action=deleteComment`, {
        method: "DELETE",
        headers: {
            "Content-type": "application/json"
        },
        body: JSON.stringify(comment)
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

export async function getDBComments(idProduct) {
    let response = await fetch(`../backend/routes/router.php?action=getComments&idProduct=${idProduct}`);

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();
}

export async function getDBAverageRating(idProduct) {
    let response = await fetch(`../backend/routes/router.php?action=getAverageRating&idProduct=${idProduct}`)

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();

}

export async function getDBSessionUser() {
    let response = await fetch(`../backend/routes/router.php?action=getSessionUser`);

    if (!response.ok) {
        throw new Error(`Error: ${response.statusText}`)
    }

    return response.json();
}
