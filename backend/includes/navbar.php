<?php
/**
 * Shared Navbar Component
 * 
 * Variables expected:
 * $base_path - The relative path string to reach the project root (e.g., "../../", "./")
 */

// If $base_path is not defined, default to current directory
if (!isset($base_path)) {
    $base_path = "./";
}

// Determine current page for active links or icon paths
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav id="nav-section" class="navbar navbar-expand-lg fixed-top" style="background-color: #1F1F1F;">
    <div class="container-fluid">

        <!-- Mobile Toggle & Logo -->
        <div class="d-flex align-items-center justify-content-between w-100 d-lg-none">
            <a href="<?php echo $base_path; ?>frontend/home.html">
                <img src="<?php echo $base_path; ?>frontend/img/logo.png" alt="Logotipo de J&A Sports" class="logoPagina img-fluid"
                    style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation"
                style="border: 1px solid #D72631;">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
        </div>


        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="row w-100 align-items-center g-0">

                <!-- Desktop Logo -->
                <div class="col-lg-1 d-none d-lg-block text-center">
                    <a href="<?php echo $base_path; ?>frontend/home.html">
                        <img src="<?php echo $base_path; ?>frontend/img/logo.png" alt="Logotipo de J&A Sports" class="logoPagina img-fluid">
                    </a>
                </div>


                <!-- Navigation Links -->
                <div class="col-lg-4 col-md-12 mt-3 mt-lg-0">
                    <ul id="main-nav"
                        class="d-flex flex-column flex-lg-row justify-content-around list-unstyled m-0 gx-0 gap-2 gap-lg-0">
                        <li><a href="#">Hombre</a></li>
                        <li><a href="#">Mujer</a></li>
                        <li><a href="#">Niños</a></li>
                        <li><a href="<?php echo $base_path; ?>frontend/productos.html">Productos</a></li>
                    </ul>
                </div>

                <!-- Search Bar -->
                <div class="col-lg-5 col-md-12 my-3 my-lg-0 px-lg-3">
                    <input type="text" placeholder="Buscar" aria-label="Buscar productos" class="w-100">
                </div>

                <!-- Icons -->
                <div class="col-lg-2 col-md-12 d-flex justify-content-center justify-content-lg-end">
                    <ul id="icon-nav" class="d-flex list-unstyled m-0 gap-3">
                        <li>
                            <a href="<?php echo $base_path; ?>backend/auth/login.php" aria-label="Iniciar sesión">
                                <img src="<?php echo $base_path; ?>frontend/img/user.png" alt="Icono de usuario">
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $base_path; ?>frontend/carrito.html" aria-label="Ver carrito">
                                <img src="<?php echo $base_path; ?>frontend/img/carrito.png" alt="Icono del carrito">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
