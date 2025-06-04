<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Home</title>
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
                                <h3>Nuevo ticket</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="#">Home</a></li>
                                    <li class="active">Nuevo ticket</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>
            </div>

            <div class="box-typical box-typical-padding">
                <p>
                    Desde esta ventana podras crear nuevos tickets.
                </p>


                <div class="row">
                    <form method="post" id="ticket_form">
                        <input type="hidden" id="usu_id" name="usu_id" value="<?php echo $_SESSION["usu_id"] ?>">
                        <div class="col-lg-6">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cat_id">Categoria</label>
                                <select class="form-control" id="cat_id" name="cat_id" placeholder="Seleccione una categoria">
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset class="form-group semibold">
                                <label class="form-label" for="tick_titulo">Titulo</label>
                                <input type="text" class="form-control" id="tick_titulo" name="tick_titulo" placeholder="Titulo del ticket">
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group semibold">
                                <label class="form-label" for="tick_descrip">Descripcion</label>
                                <div class="summernote-theme-1">
                                    <textarea id="tick_descrip" name="tick_descrip" class="summernote" name="name"></textarea>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit"  name="action" value="add" class="btn btn-inline">Guardar</button>
                        </div>
                    </form>
                </div><!--.row-->

            </div>


        </div>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="..//NuevoTicket/nuevoticket.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>