<?php
require_once("../../config/conexion.php");
if (empty($_GET['token'])) {
    header("Location: " . Conectar::ruta());
    exit();
}
$token = $_GET['token'];
?>
<!DOCTYPE html>
<html>

<head lang="es">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Restablecer Contraseña | Mesa de Ayuda</title>

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
                <p class="lead">Establece tu nueva contraseña.</p>
            </div>
        </div>

        <div class="col-lg-5 col-12 login-form-wrapper">
            <form class="sign-box" id="restablecer-form">
                <header class="sign-title">Nueva Contraseña</header>
                <div class="sign-note">Ingresa tu nueva contraseña.</div>

                <input type="hidden" id="token" name="token" value="<?php echo $token; ?>">

                <div class="form-group">
                    <input type="password" class="form-control" id="usu_pass1" name="usu_pass1" placeholder="Nueva Contraseña" required />
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="usu_pass2" name="usu_pass2" placeholder="Confirmar Contraseña" required />
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="btn-restablecer">Cambiar Contraseña</button>
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
            $('#restablecer-form').on('submit', function(e) {
                e.preventDefault();
                var pass1 = $('#usu_pass1').val();
                var pass2 = $('#usu_pass2').val();
                var token = $('#token').val();

                if (pass1 == '' || pass2 == '') {
                    Swal.fire('Error', 'Por favor complete los campos', 'error');
                    return;
                }

                if (pass1 != pass2) {
                    Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
                    return;
                }

                $('#btn-restablecer').prop('disabled', true).text('Procesando...');

                $.post("../../controller/usuario.php?op=restablecer", {
                    token: token,
                    usu_pass: pass1
                }, function(data) {
                    $('#btn-restablecer').prop('disabled', false).text('Cambiar Contraseña');
                    if (data == '1') {
                        Swal.fire({
                            title: '¡Exito!',
                            text: 'La contraseña ha sido actualizada.',
                            icon: 'success'
                        }).then((result) => {
                            if (result.value) {
                                window.location.href = '../../index.php';
                            }
                        });
                    } else {
                        Swal.fire('Error', 'El enlace es inválido o ha expirado.', 'error');
                    }
                });
            });
        });
    </script>
</body>

</html>