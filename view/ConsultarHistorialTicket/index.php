<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Consultar Tickets con Historial</title>
    </head>

    <body class="with-side-menu">

        <?php require_once('../MainHeader/header.php') ?>
        <div class="mobile-menu-left-overlay"></div>
        <?php require_once('../MainNav/nav.php') ?>

        <!-- Contenido -->
        <div class="page-content">
            <div class="container-fluid">
                <header class="section-header">
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <h3>Tickets con Historial de Asignación</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li class="active">Tickets con Historial</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical box-typical-padding">
                    <table id="historial_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th style="width: 5%;">N° Ticket</th>
                                <th style="width: 15%;">Categoría</th>
                                <th style="width: 40%;">Título</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 10%;">Fecha Creación</th>
                                <th style="width: 15%;">Asignado a</th>
                                <th style="width: 5%;">Ver</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <?php require_once('../MainJs/js.php') ?>
        <script type="text/javascript" src="consultarhistorialticket.js"></script>

    </body>
    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>
