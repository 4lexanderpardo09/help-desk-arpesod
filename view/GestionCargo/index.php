<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once("../MainHead/head.php"); ?>
    <title>Gestion de cargos</title>
    </head>
    <body class="with-side-menu">
        <?php require_once("../MainHeader/header.php"); ?>
        <div class="mobile-menu-left-overlay"></div>
        <?php require_once("../MainNav/nav.php"); ?>

        <div class="page-content">
            <div class="container-fluid">
                <header class="section-header">
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <h3>Gestion de cargos</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="#">Home</a></li>
                                    <li class="active">Gestion de cargos</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <button type="button" id="btn_cargue_masivo" class="btn btn-inline btn-success" data-toggle="modal" data-target="#modalCargueMasivo">
                        <i class="fa fa-upload"></i> Cargue Masivo
                    </button>
                    <button type="button" id="btnnuevocargo" class="btn btn-inline btn-primary">Nuevo Registro</button>
                    <table id="cargo_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr>
                                <th style="width: 80%;">Nombre</th>
                                <th class="text-center" style="width: 10%;"></th>
                                <th class="text-center" style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php require_once("modalnuevocargo.php"); ?>
        <?php require_once("../MainJs/js.php"); ?>
        <script type="text/javascript" src="gestioncargo.js"></script>
    </body>
    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>