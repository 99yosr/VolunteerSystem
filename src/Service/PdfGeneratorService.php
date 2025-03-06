<?php

namespace App\Service;
use Dompdf\Dompdf;
use Dompdf\Options;
use Nucleos\DompdfBundle\Factory\DompdfFactoryInterface;

class PdfGeneratorService
{
    private DompdfFactoryInterface $dompdfFactory;

    public function __construct(DompdfFactoryInterface $dompdfFactory)
    {
        $this->dompdfFactory = $dompdfFactory;
    }

    public function generate(string $html): string
    {
        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setChroot('%kernel.project_dir%/public');

        //tsstt
        /*$pdfOptions->setDebugCss(true);
        $pdfOptions->setDebugPng(true);
        $pdfOptions->setDebugKeepTemp(true); // Conserve les fichiers temporaires pour déboguer

*/

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isPhpEnabled', true);
        // Create a Dompdf instance
        //$dompdf = $this->dompdfFactory->create();

        // Load the HTML content
        $dompdf->loadHtml($html);

        // Set the paper size and orientation (optional)
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Return the generated PDF output
        return $dompdf->output();
    }


}