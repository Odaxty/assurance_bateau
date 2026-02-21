<?php

namespace App\Controller;

use App\Entity\Boat;
use App\Form\BoatType;
use App\Repository\AccountRepository;
use App\Repository\BoatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BoatController extends AbstractController
{
    #[Route('/boat/new', name: 'app_boat_new')]
    public function new(
        Request $request,
        AccountRepository $accountRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $firstname = $request->query->get('firstname');

        $account = $accountRepository->findOneBy(['firstname' => $firstname]);

        if (!$account) {
            $this->addFlash('danger', "Le client '$firstname' n'existe pas. Veuillez le créer d'abord.");
            return $this->redirectToRoute('app_home');
        }

        $boat = new Boat();
        $boat->setAccount($account);

        $form = $this->createForm(BoatType::class, $boat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boat);
            $entityManager->flush();

            $this->addFlash('success', "Le bateau a été ajouté au compte de " . $account->getFirstname());
            return $this->redirectToRoute('app_home');
        }

        return $this->render('boat/new.html.twig', [
            'form' => $form->createView(),
            'account' => $account
        ]);
    }

    #[Route('/account/{firstname}/boats', name: 'app_account_boats')]
    public function listByAccount(string $firstname, AccountRepository $accountRepository): Response
    {
        $account = $accountRepository->findOneBy(['firstname' => $firstname]);

        return $this->render('account/boats.html.twig', [
            'account' => $account,
            'boats' => $account->getBoats()
        ]);
    }

    #[Route('/liste-des-bateaux', name: 'app_boat_list')]
    public function list(BoatRepository $boatRepository): Response
    {
        $boats = $boatRepository->findAll();

        return $this->render('boat/list.html.twig', [
            'boats' => $boats,
        ]);
    }

    #[Route('/liste-des-bateaux-luxury', name: 'app_boat_luxury')]
    public function luxury(BoatRepository $boatRepository): Response
    {
        $boats = $boatRepository->findLuxuryBoats();
        return $this->render('boat/list.html.twig', [
            'boats' => $boats,
        ]);
    }
}
