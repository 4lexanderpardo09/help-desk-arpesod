<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Gestion del mapeo del flujo</title>
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
                                <h3>Gestion del mapeo del flujo</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li><a href="#">Gestion</a></li>
                                    <li class="active">Gestion del mapeo del flujo</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical box-typical-padding">
                    <button type="button" id="btnnuevoflujomapeo" class="btn btn-inline btn-primary">Nuevo mapeo</button>
                    <table id="flujomapeo_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr role="row">
                                <th style="width: 25%;">Subcategoria</th>
                                <th style="width: 25%;">Cargo creador</th>
                                <th style="width: 25%;">Cargo del asigando</th>
                                <th style="width: 2%;">Editar</th>
                                <th style="width: 2%;">Eliminar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <?php require_once('../GestionMapeoFlujo/modalnuevoflujomapeo.php') ?>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="../GestionMapeoFlujo/gestionmapeoflujo.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>