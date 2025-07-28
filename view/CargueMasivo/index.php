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
                    <h3>Cargue Masivo de Flujos y Categorías</h3>
                </header>
                <div class="box-typical box-typical-padding">
                    <form action="../../controller/carguemasivo.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label" for="archivo_excel">Seleccionar Archivo Excel</label>
                            <input type="file" name="archivo_excel" id="archivo_excel" class="form-control" accept=".xlsx, .xls" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Cargue Masivo</button>
                    </form>
                </div>
            </div>
        </div>
        <? //php require_once("modalnuevocargo.php"); 
        ?>
        <? //php require_once("../MainJs/js.php"); 
        ?>
        <script type="text/javascript" src="gestioncargo.js"></script>
    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>