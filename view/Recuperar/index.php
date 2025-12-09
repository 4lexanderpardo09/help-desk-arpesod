<?php
require_once("../../config/conexion.php");
?>
<!DOCTYPE html>
<html>

<head lang="es">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Recuperar Contraseña | Mesa de Ayuda</title>

    <link rel="stylesheet" href="../../public/css/lib/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="../../public/css/lib/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/main.css">
    <link href="../../public/img/favicon.ico" rel="shortcut icon">

    <style>
        body {
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        .login-branding {
            background-image: linear-gradient(rgba(233, 228, 228, 1), rgba(109, 134, 197, 1)), url('https://images.unsplash.com/photo-1554224155-8d04421cd6e2?auto=format&fit=crop&q=80&w=2070');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
            padding: 5rem;
        }

        .login-branding h1 {
            font-weight: 300;
            font-size: 3rem;
            color: black;
            margin-top: 1.5rem;
        }

        .login-branding p {
            font-size: 1.2rem;
            color: black;
            opacity: 0.8;
        }

        .login-form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            background-color: #fff;
        }

        .sign-box {
            max-width: 400px;
            width: 100%;
            background: transparent;
            padding: 20px;
        }

        .sign-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .sign-note {
            text-align: center;
            color: #777;
            margin-bottom: 2rem;
        }

        @media (max-width: 992px) {
            .login-branding {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="col-lg-7 login-branding">
            <div>
                <img src="../../public/img/electro-logo.png" alt="Logo" style="max-width: 20rem; max-height: 20rem;">
                <h1>Plataforma de Soporte</h1>
                <p class="lead">Recuperación de acceso al sistema.</p>
            </div>
        </div>

        <div class="col-lg-5 col-12 login-form-wrapper">
            <form class="sign-box" id="recuperar-form">
                <header class="sign-title">Recuperar Contraseña</header>
                <div class="sign-note">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</div>

                <div class="form-group">
                    <input type="email" class="form-control" id="usu_correo" name="usu_correo" placeholder="Correo Electrónico" required />
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="btn-recuperar">Enviar Enlace</button>
                <br>
                <div class="text-center">
                    <a href="../../index.php">Volver al Inicio</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../../public/js/lib/jquery/jquery.min.js"></script>
    <script src="../../public/js/lib/tether/tether.min.js"></script>
    <script src="../../public/js/lib/bootstrap/bootstrap.min.js"></script>
    <script src="../../public/js/plugins.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
        $(document).ready(function() {
            $('#recuperar-form').on('submit', function(e) {
                e.preventDefault();
                var correo = $('#usu_correo').val();
                if (correo == '') {
                    Swal.fire('Error', 'Por favor ingrese su correo', 'error');
                    return;
                }

                $('#btn-recuperar').prop('disabled', true).text('Enviando...');

                $.post("../../controller/usuario.php?op=recuperar", {
                    usu_correo: correo
                }, function(data) {
                    $('#btn-recuperar').prop('disabled', false).text('Enviar Enlace');
                    if (data == '1') {
                        Swal.fire({
                            title: '¡Enviado!',
                            text: 'Revisa tu correo para restablecer la contraseña.',
                            icon: 'success'
                        });
                        $('#usu_correo').val('');
                    } else if (data == '2') {
                        Swal.fire('Error', 'El correo no se encuentra registrado.', 'warning');
                    } else {
                        Swal.fire('Error', 'Ocurrió un error al enviar el correo.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>