<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Categoria.php');
require_once('../models/Empresa.php');
require_once('../models/Departamento.php');

$categoria_model = new Categoria();
$empresa_model = new Empresa();
$departamento_model = new Departamento();

// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_categorias']) || $_FILES['archivo_categorias']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_categorias']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Categorías...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    
     $sheet_name_esperado = $_POST['sheet_name'];

    // b. Verificamos si una hoja con ese nombre existe en el archivo
    if ($spreadsheet->sheetNameExists($sheet_name_esperado)) {
        // c. Si existe, la seleccionamos
        $worksheet = $spreadsheet->getSheetByName($sheet_name_esperado);
        echo "<p>Leyendo la hoja: '{$sheet_name_esperado}'...</p>";
    } else {
        // d. Si no existe, detenemos el proceso con un error claro
        die("<h3 style='color:red;'>Error: El archivo Excel no contiene una hoja llamada '{$sheet_name_esperado}'.</h3>");
    }

    $rows = $worksheet->toArray();
    array_shift($rows); // Quitar la fila del encabezado

    $categorias_creadas = 0;
    $categorias_actualizadas = 0;

    // 2. Recorrer cada fila del Excel
    foreach ($rows as $row) {
        $cat_nom = trim($row[0]);
        $empresas_str = trim($row[1]);
        $departamentos_str = trim($row[2]);

        if (empty($cat_nom)) continue;

        // 3. Obtener los IDs de las empresas asociadas
        $emp_ids = [];
        $empresas_array = explode(',', $empresas_str);
        foreach ($empresas_array as $emp_nom) {
            $id = $empresa_model->get_id_por_nombre(trim($emp_nom));
            if ($id) $emp_ids[] = $id;
        }

        // 4. Obtener los IDs de los departamentos asociados
        $dp_ids = [];
        $departamentos_array = explode(',', $departamentos_str);
        foreach ($departamentos_array as $dp_nom) {
            $id = $departamento_model->get_id_por_nombre(trim($dp_nom));
            if ($id) $dp_ids[] = $id;
        }

        // 5. Verificar si la categoría ya existe
        $cat_existente = $categoria_model->get_categoria_por_nombre($cat_nom);

        if ($cat_existente) {
            // Si ya existe, la actualizamos con las nuevas relaciones
            $cat_id = $cat_existente['cat_id'];
            $categoria_model->update_categoria($cat_id, $cat_nom, $emp_ids, $dp_ids);
            echo "<p style='color:blue;'>ACTUALIZADA: Se actualizaron las relaciones para la categoría '{$cat_nom}'.</p>";
            $categorias_actualizadas++;
        } else {
            // Si no existe, la insertamos
            $categoria_model->insert_categoria($cat_nom, $emp_ids, $dp_ids);
            echo "<p style='color:green;'>CREADA: Se ha añadido la categoría '{$cat_nom}'.</p>";
            $categorias_creadas++;
        }
    }
    
    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Categorías nuevas creadas: {$categorias_creadas}</li>";
    echo "<li>Categorías actualizadas: {$categorias_actualizadas}</li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>