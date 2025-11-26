window.addEventListener("DOMContentLoaded", () => {
    let marcas = document.getElementById("brands");

    const scrollHandler = () => { 

        const rect = marcas.getBoundingClientRect();


        if (rect.top <= window.innerHeight * 0.8) { 

            marcas.classList.add("translate");

            document.removeEventListener("scroll", scrollHandler);
        }
    };


    document.addEventListener("scroll", scrollHandler);
});