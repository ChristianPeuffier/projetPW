<?php

namespace App\Controller;

use App\Entity\Educateur;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login',methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/deconnexion', name: 'security.logout',methods: ['GET'])]
    public function logout(): void
    {
        //nothing to do here
    }

    #[Route('/inscription', name: 'security.registration',methods: ['GET', 'POST'])]
    public function registration(Request $request, EntityManagerInterface $manager): Response
    {
        $educateur = new Educateur();
        $educateur->setRoles(['ROLE_EDUCATEUR']);
        $form = $this->createForm(RegistrationType::class, $educateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $educateur = $form->getData();
            $this->addFlash('success', 'Votre compte a bien été créé.');
            $manager->persist($educateur);
            $manager->flush();
            return $this->redirectToRoute('security.login');
        }
        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
