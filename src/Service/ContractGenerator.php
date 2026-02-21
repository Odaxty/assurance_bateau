<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;


class ContractGenerator
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generate($boat, float $premium): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');//police

        $dompdf = new Dompdf($options);

        $html = $this->twig->render('pdf/contract.html.twig', [
            'boat' => $boat,
            'premium' => $premium,
            'date' => new \DateTime(),
        ]);

        // chargement de html dans dompdf et création du pdf
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}
