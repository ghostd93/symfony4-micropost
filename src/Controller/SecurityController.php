<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        

    }

    /**
     * @Route("/confirm/{token}", name="app_confirm")
     */
    public function confirm(
        string $token, 
        UserRepository $userRepository,
        EntityManagerInterface $entityManager    
    ){
        $user = $userRepository->findOneBy([
            'confirmationToken' => $token
        ]);

        if(null !== $user){
            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $entityManager->flush();
        }

        return new Response($this->twig->render('security/confirmation.html.twig', [
            'user' => $user
        ]));
    }
}
