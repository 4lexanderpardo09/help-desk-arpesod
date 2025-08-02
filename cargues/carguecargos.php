<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Cargo.php');

$cargo_model = new Cargo();

// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_cargos']) || $_FILES['archivo_cargos']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_cargos']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Cargos...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    $sheet_name_esperado = $_POST['sheet_name'];

    if ($spreadsheet->sheetNameExists($sheet_name_esperado)) {
        $worksheet = $spreadsheet->getSheetByName($sheet_name_esperado);
        echo "<p>Leyendo la hoja: '{$sheet_name_esperado}'...</p>";
    } else {
        die("<h3 style='color:red;'>Error: El archivo Excel no contiene una hoja llamada '{$sheet_name_esperado}'.</h3>");
    }

    $rows = $worksheet->toArray();
    array_shift($rows); // Quitar la fila del encabezado

    $creados = 0;
    $omitidos = 0;

    // 2. Recorrer cada fila del Excel
    foreach ($rows as $row) {
        $car_nom = trim($row[0]);

        if (empty($car_nom)) continue;

        // 3. Verificar si el cargo ya existe (insensible a mayúsculas)
        $cargo_existente = $cargo_model->get_cargo_por_nombre($car_nom);

        if ($cargo_existente) {
            echo "<p style='color:orange;'>OMITIDO: El cargo '{$car_nom}' ya existe.</p>";
            $omitidos++;
        } else {
            // Si no existe, lo insertamos
            $cargo_model->insert_cargo($car_nom);
            echo "<p style='color:green;'>CREADO: Se ha añadido el cargo '{$car_nom}'.</p>";
            $creados++;
        }
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Cargos nuevos creados: {$creados}</li>";
    echo "<li>Cargos omitidos (duplicados): {$omitidos}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>