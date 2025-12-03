<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Dashboard</title>
    </head>

    <body class="with-side-menu">
        <?php require_once('../MainHeader/header.php') ?>
        <div class="mobile-menu-left-overlay"></div>
        <?php require_once('../MainNav/nav.php') ?>

        <div class="page-content">
            <div class="container-fluid">
                <div class="box-typical box-typical-padding">
                    <h3>Bienvenido</h3>
                    <p>El sistema de reportes está siendo actualizado.</p>
                </div>
            </div>
        </div>

        <?php require_once('../MainJs/js.php') ?>
        <script type="text/javascript" src="../Home/home.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>
    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>