<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once("../MainHead/head.php"); ?>
    <title>Perfil de Usuario</title>
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
                                <h3>Perfil de Usuario</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="../Home/">Inicio</a></li>
                                    <li class="active">Perfil</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <h5 class="m-t-lg with-border">Información Personal</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="usu_nom">Nombre</label>
                                <input type="text" class="form-control" id="usu_nom" readonly value="<?php echo $_SESSION['usu_nom']; ?>">
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="usu_ape">Apellido</label>
                                <input type="text" class="form-control" id="usu_ape" readonly value="<?php echo $_SESSION['usu_ape']; ?>">
                            </fieldset>
                        </div>
                    </div>

                    <h5 class="m-t-lg with-border">Firma Digital</h5>
                    <div class="row">
                        <div class="col-lg-6">
                            <p>Sube una imagen de tu firma para usarla automáticamente en los tickets.</p>
                            <form id="firma_form" method="post" enctype="multipart/form-data">
                                <fieldset class="form-group">
                                    <label class="form-label semibold" for="usu_firma">Imagen de Firma (PNG)</label>
                                    <input type="file" class="form-control" id="usu_firma" name="usu_firma" accept="image/png">
                                </fieldset>
                                <button type="submit" class="btn btn-primary">Guardar Firma</button>
                            </form>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label semibold">Firma Actual:</label>
                            <div id="firma_preview" style="border: 1px dashed #ccc; padding: 10px; text-align: center;">
                                <span class="text-muted">No hay firma cargada</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php require_once("../MainJs/js.php"); ?>
        <script type="text/javascript" src="../../view/Perfil/perfil.js"></script>

    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>