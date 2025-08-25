<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/config/conexion.php');
require_once(__DIR__ . '/models/Usuario.php');
require_once(__DIR__ . '/models/Organigrama.php');
require_once(__DIR__ . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

// Load .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$conectar = new Conectar();
$usuario = new Usuario();
$organigrama = new Organigrama();

// Get JWT secret key from .env
$jwt_secret_key = $_ENV['JWT_SECRET_KEY'] ?? null;

if (!$jwt_secret_key) {
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_config");
    exit();
}

if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_no_token");
    exit();
}

$token = $_GET['token'];

try {
    $decoded = JWT::decode($token, new Key($jwt_secret_key, 'HS256'));

    // Access data from the 'data' claim in the JWT payload
    $user_email = $decoded->data->email ?? null;

    if (!$user_email) {
        header("Location: " . $conectar->ruta() . "index.php?m=sso_error_no_email_in_token");
        exit();
    }

    // Find user by email
    $user_data = $usuario->get_usuario_por_correo($user_email);

    if ($user_data && is_array($user_data) && count($user_data) > 0) {
        // User found, create session
        session_start();

        $_SESSION["usu_id"] = $user_data["usu_id"];
        $_SESSION["usu_nom"] = $user_data["usu_nom"];
        $_SESSION["usu_ape"] = $user_data["usu_ape"];
        $_SESSION["rol_id"] = $user_data["rol_id"];
        $_SESSION["rol_id_real"] = $user_data["rol_id"];
        $_SESSION["dp_id"] = $user_data["dp_id"];
        $_SESSION["car_id"] = $user_data["car_id"];

        // Determine if user is a boss using the Organigrama model
        $_SESSION["is_jefe"] = $organigrama->es_jefe($user_data['car_id']);

        header("Location: " . $conectar->ruta() . "view/Home/");
        exit();
    } else {
        // User not found in local DB
        header("Location: " . $conectar->ruta() . "index.php?m=sso_error_user_not_found");
        exit();
    }

} catch (ExpiredException $e) {
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_expired");
    exit();
} catch (SignatureInvalidException $e) {
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_signature");
    exit();
} catch (BeforeValidException $e) {
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_not_yet_valid");
    exit();
} catch (UnexpectedValueException $e) {
    // Catch for malformed JWT or invalid claims
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_invalid_token");
    exit();
} catch (DomainException $e) {
    // Catch for invalid claims or other domain-specific errors
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_invalid_token");
    exit();
} catch (InvalidArgumentException $e) {
    // Catch for invalid arguments passed to JWT::decode
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_invalid_token");
    exit();
} catch (LogicException $e) {
    // Catch for logic errors within the JWT library
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_invalid_token");
    exit();
} catch (RuntimeException $e) {
    // Catch for runtime errors within the JWT library
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_invalid_token");
    exit();
} catch (Exception $e) {
    // Generic error for any other JWT decoding issues
    header("Location: " . $conectar->ruta() . "index.php?m=sso_error_generic");
    exit();
}
?>