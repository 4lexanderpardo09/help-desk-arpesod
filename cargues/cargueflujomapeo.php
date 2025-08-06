<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/FlujoMapeo.php');
require_once('../models/Subcategoria.php');
require_once('../models/Cargo.php');

$flujo_mapeo_model = new FlujoMapeo();
$subcategoria_model = new Subcategoria();
$cargo_model = new Cargo();

// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_mapeo']) || $_FILES['archivo_mapeo']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_mapeo']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Mapeo de Flujos...</h1>";
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
        $cats_nom = trim($row[0]);
        $creadores_str = trim($row[1]);
        $asignados_str = trim($row[2]);

        if (empty($cats_nom)) continue;

        // 3. Mapear Nombres a IDs
        $cats_id = $subcategoria_model->get_id_por_nombre($cats_nom);

        $creador_car_ids = [];
        $creadores_array = explode(',', $creadores_str);
        foreach ($creadores_array as $cargo_nom) {
            $id = $cargo_model->get_id_por_nombre(trim($cargo_nom));
            if ($id) $creador_car_ids[] = $id;
        }

        $asignado_car_ids = [];
        $asignados_array = explode(',', $asignados_str);
        foreach ($asignados_array as $cargo_nom) {
            $id = $cargo_model->get_id_por_nombre(trim($cargo_nom));
            if ($id) $asignado_car_ids[] = $id;
        }

        if (!$cats_id || empty($creador_car_ids) || empty($asignado_car_ids)) {
            echo "<p style='color:orange;'>OMITIDO: La regla para '{$cats_nom}' no se pudo crear porque la Subcategoría o los Cargos no se encontraron en la BD.</p>";
            $omitidos++;
            continue;
        }

        // 4. Insertar la regla de mapeo
        // NOTA: Para simplificar, este script asume que cada subcategoría solo tiene una regla de mapeo.
        // Si ya existe, la omite.
        $regla_existente = $flujo_mapeo_model->get_regla_por_subcategoria($cats_id);
        if ($regla_existente) {
             echo "<p style='color:orange;'>OMITIDO: Ya existe una regla de mapeo para '{$cats_nom}'.</p>";
             $omitidos++;
        } else {
            $flujo_mapeo_model->insert_flujo_mapeo($cats_id, $creador_car_ids, $asignado_car_ids);
            echo "<p style='color:green;'>CREADA: Se ha añadido la regla de mapeo para '{$cats_nom}'.</p>";
            $creados++;
        }
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Reglas nuevas creadas: {$creados}</li>";
    echo "<li>Reglas omitidas (duplicadas o con errores): {$omitidos}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>