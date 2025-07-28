
<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir todos los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Categoria.php');
require_once('../models/Subcategoria.php');
require_once('../models/Flujo.php');
require_once('../models/FlujoPaso.php');
require_once('../models/Empresa.php');
require_once('../models/Departamento.php');
require_once('../models/Prioridad.php');
require_once('../models/Cargo.php');

// Instanciar todos los modelos
$categoria_model = new Categoria();
$subcategoria_model = new Subcategoria();
$flujo_model = new Flujo();
$flujo_paso_model = new FlujoPaso();
$empresa_model = new Empresa();
$departamento_model = new Departamento();
$prioridad_model = new Prioridad();
$cargo_model = new Cargo();


// --- 1. VERIFICACIÓN DE SUBIDA DE ARCHIVO (CORREGIDO) ---

// Primero, verificamos si el archivo fue enviado y si hay algún código de error
if (!isset($_FILES['archivo_excel']) || $_FILES['archivo_excel']['error'] != UPLOAD_ERR_OK) {
    $error_message = "Ocurrió un error con la subida del archivo.";
    if (isset($_FILES['archivo_excel']['error'])) {
        switch ($_FILES['archivo_excel']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = "El archivo excede el tamaño máximo permitido por el servidor.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = "No se seleccionó ningún archivo para subir.";
                break;
            default:
                $error_message = "Error desconocido en la subida. Código: " . $_FILES['archivo_excel']['error'];
                break;
        }
    }
    // Si hay un error, detenemos la ejecución y mostramos el mensaje.
    die("<h3 style='color:red;'>" . $error_message . "</h3>");
}


// --- 2. PROCESAMIENTO DEL ARCHIVO (SOLO SI LA SUBIDA FUE EXITOSA) ---

$nombre_temporal = $_FILES['archivo_excel']['tmp_name'];
try {
    echo "<h1>Iniciando Cargue Masivo...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    array_shift($rows); // Quitar la fila de encabezados

    // Variables para no repetir inserciones
    $ultimo_cat_id = null;
    $ultima_categoria = '';
    $ultimo_subcat_id = null;
    $ultima_subcategoria = '';
    $ultimo_flujo_id = null;

    foreach ($rows as $row) {
        // Asignar columnas a variables
        $cat_nom = trim($row[0]);
        $cats_nom = trim($row[1]);
        $pd_nom = trim($row[2]);
        $cats_descrip = trim($row[4]);
        $emp_nom = trim($row[7]);
        $paso_orden = trim($row[8]);
        $paso_nombre = trim($row[9]);
        $cargo_nom = trim($row[10]);
        $departamentos_str = isset($row[11]) ? trim($row[11]) : '';

        if (empty($cat_nom) || empty($cats_nom)) continue;

        // Mapear Nombres a IDs
        $emp_id = $empresa_model->get_id_por_nombre($emp_nom);
        $pd_id = $prioridad_model->get_id_por_nombre($pd_nom);
        $cargo_id = $cargo_model->get_id_por_nombre($cargo_nom);
        if (!$emp_id || !$pd_id || !$cargo_id) {
            echo "<p style='color:orange;'>ADVERTENCIA: Se omitió la fila para '{$cats_nom}' porque la Empresa, Prioridad o Cargo no se encontraron en la BD.</p>";
            continue;
        }

        // Procesar Categoría
        if ($ultima_categoria != $cat_nom) {
            $cat_existente = $categoria_model->get_categoria_por_nombre($cat_nom);
            if (!$cat_existente) {
                $categoria_model->insert_categoria_simple($cat_nom);
            }
            $cat_data = $categoria_model->get_categoria_por_nombre($cat_nom);
            $ultimo_cat_id = $cat_data['cat_id'];
            $ultima_categoria = $cat_nom;
            echo "<p><strong>Procesando Categoría: {$cat_nom} (ID: {$ultimo_cat_id})</strong></p>";
        }

        // Procesar Relaciones Muchos a Muchos
        $categoria_model->asociar_empresa($ultimo_cat_id, $emp_id);
        if (!empty($departamentos_str)) {
            $departamentos_array = explode(',', $departamentos_str);
            foreach ($departamentos_array as $dp_nom) {
                $dp_id = $departamento_model->get_id_por_nombre(trim($dp_nom));
                if ($dp_id) {
                    $categoria_model->asociar_departamento($ultimo_cat_id, $dp_id);
                }
            }
        }

        // Procesar Subcategoría y Flujo
        if ($ultima_subcategoria != $cats_nom) {
            $subcat_existente = $subcategoria_model->get_subcategoria_por_nombre($cats_nom);
            if (!$subcat_existente) {
                $subcategoria_model->insert_subcategoria($ultimo_cat_id, $pd_id, $cats_nom, $cats_descrip);
            }
            $subcat_data = $subcategoria_model->get_subcategoria_por_nombre($cats_nom);
            $ultimo_subcat_id = $subcat_data['cats_id'];
            $ultima_subcategoria = $cats_nom;

            $flujo_existente = $flujo_model->get_flujo_por_subcategoria($ultimo_subcat_id);
            if (!$flujo_existente) {
                $flujo_model->insert_flujo("Flujo para " . $cats_nom, $ultimo_subcat_id, 0);
            }
            $flujo_data = $flujo_model->get_flujo_por_subcategoria($ultimo_subcat_id);
            $ultimo_flujo_id = $flujo_data['flujo_id'];
            echo "<p style='padding-left: 20px;'>-- Procesando Subcategoría: {$cats_nom} y su Flujo</p>";
        }

        // Procesar Paso del Flujo
        if ($ultimo_flujo_id) {
            $flujo_paso_model->insert_paso($ultimo_flujo_id, $paso_orden, $paso_nombre, $cargo_id, 1, '');
            echo "<p style='padding-left: 40px;'>-----> Paso Creado: {$paso_nombre}</p>";
        }
    }
    echo "<h3 style='color:green;'>¡Cargue Masivo Finalizado!</h3>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
?>