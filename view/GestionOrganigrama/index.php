<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once("../MainHead/head.php"); ?>
    <title>Gestion de Organigrama</title>
    </head>
    <body class="with-side-menu">

        <?php require_once("../MainHeader/header.php"); ?>

        <div class="mobile-menu-left-overlay"></div>

        <?php require_once("../MainNav/nav.php"); ?>

        <!-- Contenido -->
        <div class="page-content">
            <div class="container-fluid">
                <header class="section-header">
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <h3>Gestion de Organigrama</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="#">Home</a></li>
                                    <li class="active">Gestion de Organigrama</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <button type="button" id="btnnuevo" class="btn btn-inline btn-primary">Nueva Relación</button>
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="m-t-lg with-border">Organigrama Visual</h5>
                            <div id="chart_div" style="width: 100%; max-height: 600px; overflow: auto; border: 1px solid #ddd; padding: 10px;"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="m-t-lg with-border">Listado de Jerarquías</h5>
                            <table id="organigrama_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Cargo</th>
                                        <th style="width: 40%;">Reporta a (Jefe)</th>
                                        <th class="text-center" style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <!-- Contenido -->

        <?php require_once("modalorganigrama.php"); ?>

        <?php require_once("../MainJs/js.php"); ?>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="gestionorganigrama.js"></script>

    </body>
    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location:" . $conectar->ruta() . "index.php");
}
?>