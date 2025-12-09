<?php
require_once('../vendor/autoload.php');

use setasign\Fpdi\Fpdi;

class PdfService
{
    /**
     * Firma el PDF con múltiples firmas en una sola pasada.
     * @param string $rutaPdfOriginal Ruta al PDF origen.
     * @param string $rutaImagenFirma Ruta a la imagen de la firma.
     * @param array $firmas Array de configuraciones: [['x' => 10, 'y' => 20, 'pagina' => 1], ...]
     */
    public function firmarPdfMultiple($rutaPdfOriginal, $rutaImagenFirma, $firmas = [])
    {
        error_log("PdfService::firmarPdfMultiple - Start. PDF: $rutaPdfOriginal, Firmas: " . count($firmas));
        try {
            if (!file_exists($rutaPdfOriginal)) {
                error_log("PdfService::firmarPdfMultiple - PDF original no existe: " . $rutaPdfOriginal);
                throw new Exception("El archivo PDF original no existe: " . $rutaPdfOriginal);
            }

            if (!file_exists($rutaImagenFirma)) {
                error_log("PdfService::firmarPdfMultiple - Imagen de firma no existe: " . $rutaImagenFirma);
                throw new Exception("La imagen de la firma no existe.");
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($rutaPdfOriginal);
            error_log("PdfService::firmarPdfMultiple - Page count: $pageCount");

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);

                // Check all signatures to see if any apply to this page
                foreach ($firmas as $firma) {
                    $firmaPage = isset($firma['pagina']) ? $firma['pagina'] : 1;
                    if ($pageNo == $firmaPage) {
                        $coordX = $firma['x'];
                        $coordY = $firma['y'];
                        // Insert image
                        $pdf->Image($rutaImagenFirma, $coordX, $coordY, 60);
                        error_log("PdfService::firmarPdfMultiple - Signature inserted at page $pageNo (X:$coordX, Y:$coordY)");
                    }
                }
            }

            $pdf->Output('F', $rutaPdfOriginal);
            error_log("PdfService::firmarPdfMultiple - PDF saved.");
            return true;
        } catch (Exception $e) {
            error_log("Error firmando PDF (Multiple): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Backward compatibility wrapper
     */
    public function firmarPdf($rutaPdfOriginal, $rutaImagenFirma, $coordX, $coordY, $pagina = 1)
    {
        return $this->firmarPdfMultiple($rutaPdfOriginal, $rutaImagenFirma, [
            ['x' => $coordX, 'y' => $coordY, 'pagina' => $pagina]
        ]);
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
                        $fontSize = isset($data['font_size']) ? $data['font_size'] : 10;
                        $pdf->SetFont('Arial', '', $fontSize);
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
