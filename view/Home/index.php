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

                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-sm-4">
                                <article class="statistic-box purple">
                                    <div>
                                        <div class="number" id="lbltotal"></div>
                                        <div class="caption">
                                            <div>Total tickets</div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-sm-4">
                                <article class="statistic-box green">
                                    <div>
                                        <div class="number" id="lblabiertos"></div>
                                        <div class="caption">
                                            <div>Total tickets abiertos</div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-sm-4">
                                <article class="statistic-box red">
                                    <div>
                                        <div class="number" id="lblcerrados"></div>
                                        <div class="caption">
                                            <div>Total tickets cerrados</div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="card">
                    <header class="card-header">
                        Grafico estadístico
                    </header>
                    <div class="card-block">
                        <canvas id="bar-chart" style="height: 20rem;"></canvas>
                    </div>
                </section>
            </div>
        </div>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="../Home/home.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>



    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>