<?php

namespace App\Controller;

use App\Service\ContractGenerator;
use App\Service\InsuranceCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Boat;
use App\Repository\BoatRepository;


final class InsuranceController extends AbstractController
{
    #[Route('/insurance/download', name: 'app_insurance_download')]
    public function download(
        Request $request,
        BoatRepository $boatRepository,
        ContractGenerator $generator,
        InsuranceCalculator $calculator
    ): Response {
        $id = $request->query->get('id');

        $boat = $boatRepository->find($id);

        if (!$boat) {
            $this->addFlash('danger', "Le bateau avec l'ID #$id n'existe pas. Impossible de générer le contrat.");
            return $this->redirectToRoute('app_home');
        }

        $premium = $calculator->calculatePremium($boat);

        $pdfContent = $generator->generate($boat, $premium);

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="contrat_' . $boat->getName() . '.pdf"');

        return $response;
    }

    #[Route('/insurance/{id}', name: 'app_insurance')]
    public function index(
        ?Boat $boat, //bateau automatiquement trouver grace a Symfony
        InsuranceCalculator $calculator
    ): Response {
        if (!$boat) {
            $this->addFlash('error', "Ce bateau n'existe pas dans notre base de données.");
            return $this->redirectToRoute('app_boat_list');
        }
        $price = $calculator->calculatePremium($boat);

        return $this->render('insurance/index.html.twig', [
            'boat' => $boat,
            'priceAssurance' => $price
        ]);
    }
}
