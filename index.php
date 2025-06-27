<?php
require_once("config/conexion.php");


if (isset($_POST["enviar"]) and $_POST["enviar"] == "si") {
    require_once("models/Usuario.php");
    $usuario = new Usuario();
    $usuario->login();
}


?>
<!DOCTYPE html>
<html>

<head lang="es">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Inicio de sesion</title>

    <link href="public/img/favicon.144x144.png" rel="apple-touch-icon" type="public/image/png" sizes="144x144">
    <link href="public/img/favicon.114x114.png" rel="apple-touch-icon" type="public/image/png" sizes="114x114">
    <link href="public/img/favicon.72x72.png" rel="apple-touch-icon" type="public/image/png" sizes="72x72">
    <link href="public/img/favicon.57x57.png" rel="apple-touch-icon" type="public/image/png">
    <link href="img/favicon.png" rel="icon" type="public/image/png">
    <link href="public/img/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="/public/css/separate/pages/login.min.css">
    <link rel="stylesheet" href="/public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="/public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/main.css">
</head>

<body>

    <div class="page-center">
        <div class="page-center-in">
            <div class="container-fluid">



                <form class="sign-box" action="" method="post" id="login-form">
                    <input type="hidden" name="rol_id" id="rol_id" value="1">
                    <div class="sign-avatar">
                        <img  src="public/img/user-1.png" alt="" id="imgtipo">
                    </div>
                    <header class="sign-title" id="lbltitulo">Usuario</header>
                    <?php
                    if (isset($_GET["m"])) {
                        switch ($_GET["m"]) {
                            case "1":
                    ?>
                                <div class="alert alert-success alert-no-border alert-close alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    Correo o contraseña incorrectos.
                                </div>
                            <?php
                                break;
                            case "2":
                            ?>
                                <div class="alert alert-success alert-no-border alert-close alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    Los campos estan vacios, por favor ingrese su correo y contraseña.
                                </div>

                    <?php
                                break;
                        }
                    }
                    ?>
                    <div class="form-group">
                        <input type="text" class="form-control" id="usu_correo" name="usu_correo" placeholder="Correo" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="usu_pass" name="usu_pass" placeholder="Contraseña" />
                    </div>
                    <div class="form-group">
                        <!-- <div class="float-right reset">
                            <a href="reset-password.html">Cambiar contraseña</a>
                        </div> -->

                        <div class="float-left reset">
                            <a href="#" id="btnsoporte">Acceso soporte</a>
                        </div>
                    </div>
                    <input type="hidden" name="enviar" value="si" class="form-control">
                    <button type="submit" class="btn btn-rounded">Ingresar</button>
                </form>
            </div>
        </div>
    </div><!--.page-center-->


    <script src="public/js/lib/jquery/jquery.min.js"></script>
    <script src="public/js/lib/tether/tether.min.js"></script>
    <script src="public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script src="public/js/plugins.js"></script>
    <script type="text/javascript" src="public/js/lib/match-height/jquery.matchHeight.min.js"></script>
    <script src="public/js/lib/hide-show-password/bootstrap-show-password.min.js"></script>
    <script>
        $(function() {
            $('.page-center').matchHeight({
                target: $('html')
            });

            $(window).resize(function() {
                setTimeout(function() {
                    $('.page-center').matchHeight({
                        remove: true
                    });
                    $('.page-center').matchHeight({
                        target: $('html')
                    });
                }, 100);
            });
        });
    </script>
    <script src="public/js/app.js"></script>
    <script src="/index.js"></script>
</body>

</html>