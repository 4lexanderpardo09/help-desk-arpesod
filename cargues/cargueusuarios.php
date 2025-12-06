<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Usuario.php');
require_once('../models/Empresa.php');
require_once('../models/Cargo.php');
require_once('../models/Regional.php');

$usuario_model = new Usuario();
$empresa_model = new Empresa();
// Cargo y Regional se consultan directamente o se pueden instanciar modelos si tienen métodos de búsqueda por nombre
// Usaremos conexión directa para búsquedas simples como en el script anterior, o métodos si existen.
// Para mantener consistencia con el script anterior, usaremos la conexión de Usuario para consultas ad-hoc o instanciamos modelos.
// El script anterior usaba consultas directas para IDs. Aquí podemos hacer lo mismo o usar modelos.
// Usaremos la instancia de $usuario_model para obtener la conexión.
$conectar = $usuario_model->getConexion(); // Método estático o público si existe, o heredado.
// Usuario hereda de Conectar, pero getConexion es estático.
// Mejor instanciamos y usamos la conexión interna si es posible, o simplemente usamos los modelos si tienen los métodos.
// El script anterior usaba $conectar->prepare.
// Vamos a instanciar Conectar para tener acceso a la DB para las búsquedas auxiliares.
$conexion_db = new Conectar();
$dbh = $conexion_db->getConexion();


// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_usuarios']) || $_FILES['archivo_usuarios']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_usuarios']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Usuarios...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    $sheet_name_esperado = isset($_POST['sheet_name']) ? $_POST['sheet_name'] : 'Hoja1'; // Default o post

    // Si el usuario no envía nombre de hoja, intentamos usar la primera
    if (empty($_POST['sheet_name'])) {
        $worksheet = $spreadsheet->getActiveSheet();
        echo "<p>Leyendo la hoja activa...</p>";
    } else {
        if ($spreadsheet->sheetNameExists($sheet_name_esperado)) {
            $worksheet = $spreadsheet->getSheetByName($sheet_name_esperado);
            echo "<p>Leyendo la hoja: '{$sheet_name_esperado}'...</p>";
        } else {
            die("<h3 style='color:red;'>Error: El archivo Excel no contiene una hoja llamada '{$sheet_name_esperado}'.</h3>");
        }
    }

    $rows = $worksheet->toArray();
    array_shift($rows); // Quitar la fila del encabezado

    $creados = 0;
    $errores = 0;

    // 2. Recorrer cada fila del Excel
    foreach ($rows as $row) {
        // Mapeo de columnas (ajustar índices según el Excel)
        // 0: cedula, 1: nombre, 2: cargo, 3: correo, 4: empresa, 5: regional, 6: nota
        $cedula = trim($row[0]);
        $nombre_completo = trim($row[1]);
        $cargo_nom = trim($row[2]);
        $correo = trim($row[3]);
        $empresa_nom = trim($row[4]);
        $regional_nom = trim($row[5]);

        if (empty($correo)) continue;

        // 1. Separar Nombre y Apellido
        $parts = explode(' ', $nombre_completo);
        $usu_ape = '';
        $usu_nom = '';
        
        if (count($parts) >= 3) {
            $usu_ape = $parts[0] . ' ' . $parts[1];
            $usu_nom = implode(' ', array_slice($parts, 2));
        } elseif (count($parts) == 2) {
            $usu_ape = $parts[0];
            $usu_nom = $parts[1];
        } else {
            $usu_nom = $nombre_completo;
            $usu_ape = '';
        }

        // 2. Generar Contraseña
        $email_parts = explode('@', $correo);
        $pass_base = $email_parts[0];
        $password = $pass_base . "2025";

        // 3. Buscar IDs
        // Cargo
        $sql = "SELECT car_id FROM tm_cargo WHERE UPPER(car_nom) = UPPER(?) LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $cargo_nom);
        $stmt->execute();
        $car_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $car_id = $car_row ? $car_row['car_id'] : null;

        if (!$car_id) {
            echo "<p style='color:orange;'>ADVERTENCIA: Cargo '$cargo_nom' no encontrado para $correo. Saltando.</p>";
            $errores++;
            continue;
        }

        // Regional
        $sql = "SELECT reg_id FROM tm_regional WHERE UPPER(reg_nom) = UPPER(?) LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $regional_nom);
        $stmt->execute();
        $reg_row = $stmt->fetch(PDO::FETCH_ASSOC);
        $reg_id = $reg_row ? $reg_row['reg_id'] : null;

        // Empresa
        $emp_id = $empresa_model->get_id_por_nombre($empresa_nom);

        // 4. Insertar Usuario
        $rol_id = 1; // Usuario
        $dp_id = null;
        $es_nacional = 0;

        // Verificar si ya existe por correo
        $sql = "SELECT usu_id FROM tm_usuario WHERE usu_correo = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $correo);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
             echo "<p style='color:orange;'>OMITIDO: El usuario con correo '$correo' ya existe.</p>";
             $errores++;
             continue;
        }

        try {
            $usu_id = $usuario_model->insert_usuario($usu_nom, $usu_ape, $correo, $password, $rol_id, $dp_id, $es_nacional, $reg_id, $car_id, $cedula);
            
            if ($usu_id) {
                echo "<p style='color:green;'>CREADO: Usuario $correo (ID: $usu_id)</p>";
                
                // 5. Asociar Empresa
                if ($emp_id) {
                    $empresa_model->insert_empresa_for_usu($usu_id, [$emp_id]);
                } else {
                    echo "<p style='color:orange;'>  - Empresa '$empresa_nom' no encontrada, no se asoció.</p>";
                }
                $creados++;
            } else {
                echo "<p style='color:red;'>ERROR: No se pudo crear el usuario $correo.</p>";
                $errores++;
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>EXCEPCIÓN al crear $correo: " . $e->getMessage() . "</p>";
            $errores++;
        }
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Usuarios creados: {$creados}</li>";
    echo "<li>Registros omitidos/errores: {$errores}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>
