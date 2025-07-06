<?php
require_once("../../config/conexion.php");
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once("../MainHead/head.php"); ?>
    <title>Detalle Ticket</title>
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
                                <h3 id="lblticketidh"></h3>
                                <span id="lbltickestadoh"></span>
                                <span class="label label-primary" id="lblnomusuarioh"></span>
                                <span class="label label-default" id="lblfechacreah"></span>
                                <span id="lblprioridadh"></span>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="#">Home</a></li>
                                    <li class="active">Detalle Ticket</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <div class="row">
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tick_titulo">Título</label>
                                <input type="text" class="form-control" id="tick_titulo" name="tick_titulo" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-4">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cat_nom">Categoria</label>
                                <input type="text" class="form-control" id="cat_nom" name="cat_nom" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-4">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cats_nom">Subcategoria</label>
                                <input type="text" class="form-control" id="cats_nom" name="cats_nom" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tickd_descripusu">Descripción</label>
                                <div class="summernote-theme-1">
                                    <textarea id="tickd_descripusu" name="tickd_descripusu" class="summernote-content-view" readonly></textarea>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Aquí se dibujará la línea de tiempo -->
                <section class="activity-line" id="lbldetalle">
                    <!-- El contenido se cargará aquí con JavaScript -->
                </section>

            </div>
        </div>
        <!-- Contenido -->

        <?php require_once("../MainJs/js.php"); ?>
        <script type="text/javascript" src="detallehistorialticket.js"></script>

    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location:" . $conectar->ruta() . "index.php");
}
?>