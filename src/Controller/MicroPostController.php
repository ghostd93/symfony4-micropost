<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use App\Repository\UserRepository;

/**
 * @Route("/micro-post")
 */
class MicroPostController
{
    private $microPostRepository;
    private $twig;
    private $formFactory;
    private $entityManager;
    private $router;
    private $flashBag;
    private $security;


    public function __construct(
        MicroPostRepository $microPostRepository,
        \Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        Security $security
    ) {
        $this->microPostRepository = $microPostRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->security = $security;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index(UserRepository $userRepository)
    {
        $currentUser = $this->security->getUser();

        $usersToFollow = [];

        if($currentUser instanceof User){
            $posts = $this->microPostRepository->findByUsers($currentUser->getFollowing());

            $usersToFollow = count($posts) === 0 ? $userRepository->findByMinNumOfPosts(5, $currentUser) : [];

        } else {
            $posts = $this->microPostRepository->findBy([], ['time' => 'DESC']);
        }

        $html = $this->twig->render('micro-post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);

        return new Response($html, 200);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @IsGranted("edit", subject="microPost")
     */
    public function edit(MicroPost $microPost, Request $request)
    {
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @IsGranted("delete", subject="microPost")
     */
    public function delete(MicroPost $microPost)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Micro post has been deleted successfully');

        return new RedirectResponse($this->router->generate('micro_post_index'));
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @isGranted("ROLE_USER")
     */
    public function add(Request $request)
    {
        $microPost = new MicroPost();

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost->setUser($this->security->getUser());
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response(
            $this->twig->render('micro-post/add.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @Route("/{id}", name="micro_post_show")
     */
    public function show(MicroPost $post)
    {
        return new Response(
            $this->twig->render('micro-post/show.html.twig', [
                'post' => $post
            ])
        );
    }

    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $user)
    {
        $html = $this->twig->render('micro-post/user-posts.html.twig', [
            // 'posts' => $this->microPostRepository->findBy(
            //     ['user' => $user], 
            //     ['time' => 'DESC']
            //     )
            'posts' => $user->getPosts(),
            'user' => $user
            ]);

        return new Response($html, 200);
    }
}
