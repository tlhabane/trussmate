<?php

namespace App\Util\Files;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use App\Exception\ValidationException;
use Mpdf\Mpdf;
use Exception;

ini_set('memory_limit', '1500M');
ini_set('max_execution_time', '300');
ini_set('pcre.backtrack_limit', '500000000');

final class CreatePDFFileService
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ValidationException
     * @throws Exception
     */
    public static function createFile(array $data, string $orientation = 'P'): array
    {
        $validatedData = ValidateFileCreationParams::validateParams($data);

        try {
            $pdfDocument = new Mpdf([
                'orientation' => $orientation,
                'setAutoBottomMargin' => 'stretch',
                'setAutoTopMargin' => 'stretch'
            ]);
            $pdfDocument->SetTitle($validatedData['fileName']);
            if (isset($validatedData['headerContent'])) {
                $pdfDocument->SetHTMLHeader($validatedData['headerContent']);
            }
            if (isset($validatedData['footerContent'])) {
                $pdfDocument->SetHTMLFooter($validatedData['footerContent']);
            }
            $pdfDocument->WriteHTML($validatedData['fileContent']);
            $pdfDocument->output("{$validatedData['fileDir']}{$validatedData['fileName']}.pdf", 'F');
            unset($pdfDocument);

            return [
                'success' => "{$validatedData['fileName']}.pdf created successfully.",
                'filename' => "{$data['fileName']}.pdf",
                'fileSource' => "{$validatedData['fileDir']}{$validatedData['fileName']}.pdf",
                'file' => "{$validatedData['fileLink']}/{$data['fileName']}.pdf"
            ];
        } catch (Exception $exception) {
            throw new ValidationException($exception->getMessage(), $exception->getCode());
        }
    }
}
