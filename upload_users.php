<?php
require_once("config/conexion.php");
require_once("models/Usuario.php");
require_once("models/Empresa.php");

// Script para carga masiva de usuarios desde CSV
// Formato CSV esperado: cedula,nombre_completo,cargo,correo,empresa,regional,nota
// Separador: ; (punto y coma) o , (coma)

class BulkUpload extends Conectar
{
    public function run($filename)
    {
        if (!file_exists($filename)) {
            die("Error: El archivo $filename no existe.\n");
        }

        $usuarioModel = new Usuario();
        $empresaModel = new Empresa();
        $conectar = parent::Conexion();
        parent::set_names();

        $handle = fopen($filename, "r");
        $header = fgetcsv($handle, 1000, ";"); // Asumiendo ; como separador por Excel

        echo "Iniciando carga masiva...\n";
        $count = 0;

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Mapeo de columnas (ajustar índices según el CSV real)
            // 0: cedula, 1: nombre, 2: cargo, 3: correo, 4: empresa, 5: regional, 6: nota
            $cedula = trim($data[0]);
            $nombre_completo = trim($data[1]);
            $cargo_nom = trim($data[2]);
            $correo = trim($data[3]);
            $empresa_nom = trim($data[4]);
            $regional_nom = trim($data[5]);

            // 1. Separar Nombre y Apellido
            // Heurística: Las dos primeras palabras son apellidos, el resto nombres (o viceversa según formato imagen)
            // Imagen: "MARTINEZ ORTIZ JULIO CESAR" -> Apellidos: MARTINEZ ORTIZ, Nombres: JULIO CESAR
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
            // "todo lo que estas antes del @ y despues 2025"
            $email_parts = explode('@', $correo);
            $pass_base = $email_parts[0];
            $password = $pass_base . "2025";

            // 3. Buscar IDs (Cargo, Regional, Empresa)
            // Cargo
            $sql = "SELECT car_id FROM tm_cargo WHERE UPPER(car_nom) = UPPER(?) LIMIT 1";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $cargo_nom);
            $stmt->execute();
            $car_row = $stmt->fetch(PDO::FETCH_ASSOC);
            $car_id = $car_row ? $car_row['car_id'] : null;

            if (!$car_id) {
                echo "Advertencia: Cargo '$cargo_nom' no encontrado para $correo. Saltando.\n";
                continue;
            }

            // Regional
            $sql = "SELECT reg_id FROM tm_regional WHERE UPPER(reg_nom) = UPPER(?) LIMIT 1";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $regional_nom);
            $stmt->execute();
            $reg_row = $stmt->fetch(PDO::FETCH_ASSOC);
            $reg_id = $reg_row ? $reg_row['reg_id'] : null;

            // Empresa
            $emp_id = $empresaModel->get_id_por_nombre($empresa_nom);

            // 4. Insertar Usuario
            // insert_usuario($usu_nom, $usu_ape, $usu_correo, $usu_pass, $rol_id, $dp_id, $es_nacional, $reg_id, $car_id, $usu_cedula)
            // Rol por defecto: 1 (Usuario) ?? Asumimos usuario normal
            $rol_id = 1;
            $dp_id = null; // No tenemos departamento en el excel
            $es_nacional = 0; // Asumimos 0

            try {
                $usu_id = $usuarioModel->insert_usuario($usu_nom, $usu_ape, $correo, $password, $rol_id, $dp_id, $es_nacional, $reg_id, $car_id, $cedula);

                if ($usu_id) {
                    echo "Usuario creado: $correo (ID: $usu_id)\n";

                    // 5. Asociar Empresa
                    if ($emp_id) {
                        $empresaModel->insert_empresa_for_usu($usu_id, [$emp_id]);
                        echo "  Empresa asociada: $empresa_nom\n";
                    } else {
                        echo "  Advertencia: Empresa '$empresa_nom' no encontrada.\n";
                    }
                    $count++;
                } else {
                    echo "Error al crear usuario: $correo\n";
                }
            } catch (Exception $e) {
                echo "Excepción al crear $correo: " . $e->getMessage() . "\n";
            }
        }
        fclose($handle);
        echo "Carga finalizada. Total usuarios creados: $count\n";
    }
}

// Ejecutar si se llama desde línea de comandos
if (php_sapi_name() == "cli") {
    $file = 'usuarios_carga.csv'; // Nombre del archivo por defecto
    if (isset($argv[1])) {
        $file = $argv[1];
    }

    $uploader = new BulkUpload();
    $uploader->run($file);
}
