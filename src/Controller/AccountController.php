<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class AccountController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($account);
                $entityManager->flush();
                $this->addFlash('ok', "Compte créé !!");
                return $this->redirectToRoute('app_home');
            }

            dd($form->getErrors(true, false));
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list-account', name: 'app_account_list')]
    public function listAccounts(AccountRepository $accountRepository): Response
    {
        $accounts = $accountRepository->findAll();

        return $this->render('account/list.html.twig', [
            'accounts' => $accounts,
        ]);
    }
}
