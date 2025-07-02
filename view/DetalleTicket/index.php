<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Detalle ticket</title>
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
                                <h3 id="lblticketid"></h3>
                                <span id="lbltickestado"></span>
                                <span class="label label-primary" id="lblnomusuario"></span>
                                <span class="label label-default" id="lblfechacrea"></span>
                                <span id="lblprioridad"></span>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li><a href="..\ConsultarTicket\">Consultar ticket</a></li>
                                    <li class="active">Detalle ticket</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="box-typical box-typical-padding">
                    <div class="row">

                        <div class="col-lg-12">
                            <fieldset class="form-group semibold">
                                <label class="form-label" for="tick_titulo">Titulo</label>
                                <input type="text" class="form-control" id="tick_titulo" name="tick_titulo" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cat_id">Categoria</label>
                                <input class="form-control" id="cat_id" name="cat_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3    ">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cats_id">Subcategoria</label>
                                <input class="form-control" id="cats_id" name="cats_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tick_titulo">Documentos adicionales</label>
                                <table id="documentos_data" class="table table-bondered table-striped table-vcenter js-datatable-full">
                                    <thead>
                                        <tr>
                                            <th style="width: 90%;">Nombre</th>
                                            <th class="text-center" style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se llenaran mediante AJAX -->
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tick_descripusu">Descripcion</label>
                                <div class="summernote-theme-1">
                                    <textarea id="tickd_descripusu" name="tickd_descripusu" class="summernote" name="name" readonly></textarea>
                                </div>
                            </fieldset>
                        </div>
                    </div><!--.row-->

                </div>

                <section class="activity-line" id="lbldetalle">

                </section>

                <div id="boxdetalleticket" class="box-typical box-typical-padding">
                    <p>
                        Ingrese su duda o consulta
                    </p>


                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" id="detalle_form">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold" for="cat_id">Documento adicional</label>
                                            <input type="file" name="fileElem" id="fileElem" class="form-control" multiple>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-4">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold" for="answer_id">Respuesta rapida</label>
                                            <select class="form-control" id="answer_id" name="answer_id" placeholder="Seleccione la prioridad">
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-4">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold" for="dest_id">Destinatario</label>
                                            <select class="form-control" id="dest_id" name="dest_id" placeholder="Seleccione la prioridad">
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                            <fieldset class="form-group semibold">
                                <label class="form-label" for="tickd_descrip">Descripcion</label>
                                <div class="summernote-theme-1">
                                    <textarea id="tickd_descrip" name="tickd_descrip" class="summernote" name="name"></textarea>
                                </div>
                            </fieldset>
                        </div>
                        <div style="display: inline-flex;">
                            <div class="col-lg-12">
                                <button type="button" id="btnenviar" class="btn btn-inline">Enviar</button>
                            </div>
                            <div class="col-lg-12">
                                <button type="button" id="btncerrarticket" class="btn btn-danger">Cerrar ticket</button>
                            </div>
                        </div>
                    </div><!--.row-->

                </div>

            </div>
        </div>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="../DetalleTicket/detalleticket.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>