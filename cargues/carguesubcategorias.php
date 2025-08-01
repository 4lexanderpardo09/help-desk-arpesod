<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Subcategoria.php');
require_once('../models/Categoria.php');
require_once('../models/Prioridad.php');

$subcategoria_model = new Subcategoria();
$categoria_model = new Categoria();
$prioridad_model = new Prioridad();

// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_subcategorias']) || $_FILES['archivo_subcategorias']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_subcategorias']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Subcategorías...</h1>";
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

    $creadas = 0;
    $omitidas = 0;

    // 2. Recorrer cada fila del Excel
    foreach ($rows as $row) {
        $cats_nom = trim($row[0]);
        $cat_nom_padre = trim($row[1]);
        $pd_nom = trim($row[2]);
        $cats_descrip = trim($row[3]);

        if (empty($cats_nom) || empty($cat_nom_padre)) continue;

        // 3. Mapear Nombres a IDs
        $cat_id = $categoria_model->get_id_por_nombre($cat_nom_padre);
        $pd_id = $prioridad_model->get_id_por_nombre($pd_nom);

        if (!$cat_id || !$pd_id) {
            echo "<p style='color:orange;'>OMITIDA: La subcategoría '{$cats_nom}' no se pudo crear porque su Categoría Padre o Prioridad no existen en la BD.</p>";
            $omitidas++;
            continue;
        }

        // 4. Verificar si la subcategoría ya existe
        $subcat_existente = $subcategoria_model->get_subcategoria_por_nombre($cats_nom);

        if ($subcat_existente) {
            echo "<p style='color:orange;'>OMITIDA: La subcategoría '{$cats_nom}' ya existe.</p>";
            $omitidas++;
        } else {
            // Si no existe, la insertamos
            $subcategoria_model->insert_subcategoria($cat_id, $pd_id, $cats_nom, $cats_descrip);
            echo "<p style='color:green;'>CREADA: Se ha añadido la subcategoría '{$cats_nom}'.</p>";
            $creadas++;
        }
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Subcategorías nuevas creadas: {$creadas}</li>";
    echo "<li>Subcategorías omitidas (duplicadas o con errores): {$omitidas}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>