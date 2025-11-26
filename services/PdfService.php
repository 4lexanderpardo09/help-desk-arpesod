<?php
require_once('../vendor/autoload.php');

use setasign\Fpdi\Fpdi;

class PdfService
{
    public function firmarPdf($rutaPdfOriginal, $rutaImagenFirma, $coordX, $coordY, $pagina = 1)
    {
        error_log("PdfService::firmarPdf - Start. PDF: $rutaPdfOriginal, Img: $rutaImagenFirma, X: $coordX, Y: $coordY, Pag: $pagina");
        try {
            if (!file_exists($rutaPdfOriginal)) {
                error_log("PdfService::firmarPdf - PDF original no existe: " . $rutaPdfOriginal);
                throw new Exception("El archivo PDF original no existe: " . $rutaPdfOriginal);
            }

            $pdf = new Fpdi();

            // Obtener número de páginas
            $pageCount = $pdf->setSourceFile($rutaPdfOriginal);
            error_log("PdfService::firmarPdf - Page count: $pageCount");

            // Iterar sobre todas las páginas
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Añadir página con el mismo tamaño y orientación
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Si es la página donde va la firma, la insertamos
                if ($pageNo == $pagina) {
                    // Insertar imagen (firma)
                    // Ajustar tamaño según necesidad, por ahora ancho 40 (ajustable)
                    // Validar que la imagen exista
                    if (file_exists($rutaImagenFirma)) {
                        $pdf->Image($rutaImagenFirma, $coordX, $coordY, 40);
                        error_log("PdfService::firmarPdf - Image inserted at page $pageNo");
                    } else {
                        error_log("Imagen de firma no encontrada: " . $rutaImagenFirma);
                    }
                }
            }

            // Guardar el nuevo PDF (sobrescribiendo o creando uno nuevo)
            // Vamos a sobrescribir el original para mantener la referencia, o crear uno nuevo y actualizar BD
            // Por simplicidad inicial, sobrescribimos el original
            $pdf->Output('F', $rutaPdfOriginal);
            error_log("PdfService::firmarPdf - PDF saved.");

            return true;
        } catch (Exception $e) {
            error_log("Error firmando PDF: " . $e->getMessage());
            return false;
        }
    }

    public function estamparTexto($rutaPdfOriginal, $textDataArray)
    {
        error_log("PdfService::estamparTexto - Start. PDF: $rutaPdfOriginal");
        try {
            if (!file_exists($rutaPdfOriginal)) {
                error_log("PdfService::estamparTexto - PDF original no existe: " . $rutaPdfOriginal);
                throw new Exception("El archivo PDF original no existe: " . $rutaPdfOriginal);
            }

            $pdf = new Fpdi();

            // Obtener número de páginas
            $pageCount = $pdf->setSourceFile($rutaPdfOriginal);

            // Iterar sobre todas las páginas
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Añadir página con el mismo tamaño y orientación
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Estampar textos correspondientes a esta página
                foreach ($textDataArray as $data) {
                    // $data = ['text' => '...', 'x' => 10, 'y' => 20, 'page' => 1]
                    if (isset($data['page']) && $data['page'] == $pageNo) {
                        $pdf->SetFont('Arial', '', 10); // Fuente por defecto
                        $pdf->SetXY($data['x'], $data['y']);
                        $pdf->Write(0, iconv('UTF-8', 'ISO-8859-1', $data['text']));
                    }
                }
            }

            $pdf->Output('F', $rutaPdfOriginal);
            error_log("PdfService::estamparTexto - PDF saved.");
            return true;
        } catch (Exception $e) {
            error_log("Error estampando texto en PDF: " . $e->getMessage());
            return false;
        }
    }
}
