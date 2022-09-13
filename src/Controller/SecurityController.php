<?php

namespace App\Controller;

use App\Form\Model\RegistrationFormModel;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var RegistrationFormModel $data */
            $data = $form->getData();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
