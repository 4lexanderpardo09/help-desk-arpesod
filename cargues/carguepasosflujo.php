<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Flujo.php');
require_once('../models/Cargo.php');
require_once('../models/Subcategoria.php');

$flujo_paso_model = new FlujoPaso();
$flujo_model = new Flujo();
$cargo_model = new Cargo();
$subcategoria_model = new Subcategoria();

// ... (tu código para verificar la subida del archivo y leer la hoja es correcto) ...
if (!isset($_FILES['archivo_pasos']) || $_FILES['archivo_pasos']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_pasos']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Pasos de Flujo...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    $sheet_name_esperado = $_POST['sheet_name'];

    if ($spreadsheet->sheetNameExists($sheet_name_esperado)) {
        $worksheet = $spreadsheet->getSheetByName($sheet_name_esperado);
        echo "<p>Leyendo la hoja: '{$sheet_name_esperado}'...</p>";
    } else {
        die("<h3 style='color:red;'>Error: El archivo Excel no contiene una hoja llamada '{$sheet_name_esperado}'.</h3>");
    }
    
    $rows = $worksheet->toArray();
    array_shift($rows);

    $creados = 0;
    $omitidos = 0;
    
    foreach ($rows as $row) {
        // --- LÓGICA CORREGIDA Y COMPLETA ---

        // 1. Asignar TODAS las columnas a variables claras
        $cats_nom = trim($row[0]);
        $paso_orden = trim($row[1]);
        $paso_nombre = trim($row[2]);
        $cargo_nom = trim($row[3]);
        $paso_tiempo_habil = isset($row[4]) && is_numeric($row[4]) ? intval($row[4]) : 1; // Días hábiles
        $paso_descripcion = isset($row[5]) ? trim($row[5]) : ''; // Descripción
        $seleccion_manual_str = isset($row[6]) ? trim($row[6]) : 'NO'; // Selección Manual

        if (empty($cats_nom) || empty($paso_nombre)) continue;

        // 2. Mapear Nombres a IDs (lógica sin cambios)
        $cats_id = $subcategoria_model->get_id_por_nombre($cats_nom);
        $flujo_id = null;
        if ($cats_id) {
            $flujo_data = $flujo_model->get_flujo_por_subcategoria($cats_id);
            if ($flujo_data) {
                $flujo_id = $flujo_data['flujo_id'];
            }
        }
        $cargo_id = $cargo_model->get_id_por_nombre($cargo_nom);

        if (!$flujo_id || !$cargo_id) {
            echo "<p style='color:orange;'>OMITIDO: El paso '{$paso_nombre}' no se pudo crear porque su Subcategoría o Cargo no existen.</p>";
            $omitidos++;
            continue;
        }
        
        // Convertimos el texto "SI" a un 1 o 0 para la base de datos
        $req_seleccion_manual = (strtoupper($seleccion_manual_str) == 'SI') ? 1 : 0;

        // 4. Insertar el paso con TODOS los datos
        $flujo_paso_model->insert_paso(
            $flujo_id,
            $paso_orden,
            $paso_nombre,
            $cargo_id,
            $paso_tiempo_habil,
            $paso_descripcion,
            $req_seleccion_manual
        );
        echo "<p style='color:green;'>CREADO: Se añadió el paso '{$paso_nombre}' al flujo de la subcategoría '{$cats_nom}'.</p>";
        $creados++;
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Pasos de flujo nuevos creados: {$creados}</li>";
    echo "<li>Pasos omitidos (con errores): {$omitidos}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>