<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Gestion de usuarios</title>
    </head>

    <body class="with-side-menu">

        <?php require_once('../MainHeader/header.php') ?>

        <div class="mobile-menu-left-overlay"></div>

        <?php require_once('../MainNav/nav.php') ?>

        <!-- contenido -->
        <div class="page-content">
            <div class="container-fluid">
                <header class="section-header">
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <h3>Gestion de usuarios</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li class="active">Gestion de usuarios</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical box-typical-padding">
                <button type="button" id="btnnuevoregistro" class="btn btn-inline btn-primary">Nuevo registro</button>
                    <table id="user_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr role="row">
                                <th style="width: 25%;">Nombres</th>
                                <th style="width: 25%;">Apellidos</th>
                                <th style="width: 31%;">Correo</th>
                                <th style="width: 5%;">Rol</th>
                                <th style="width: 2%;">Editar</th>
                                <th style="width: 2%;">Eliminar</th>

                            </tr>
                        </thead>    
                    </table>
                </div>
            </div>
        </div>
        <?php require_once('../GestionUsuario/modalnuevousuario.php') ?>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="../GestionUsuario/gestionusuario.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>