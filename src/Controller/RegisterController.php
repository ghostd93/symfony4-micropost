<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Event\UserRegisterEvent;
use App\Security\TokenGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="user_register")
     */
    public function register(
        Request $request, 
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $eventDispatcher,
        TokenGenerator $tokenGenerator
    ){
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);

            $eventDispatcher->dispatch($userRegisterEvent, UserRegisterEvent::NAME);

            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
