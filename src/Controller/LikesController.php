<?php 

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/likes")
 */
class LikesController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="likes_like")
     */
    public function like(MicroPost $post)
    {
        $currentUser = $this->getUser();

        if(!$currentUser instanceof User){
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $post->like($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ]);
    }

    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     */
    public function unlike(MicroPost $post)
    {
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $post->getLikedBy()->removeElement($currentUser);

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $post->getLikedBy()->count()
        ]);
    }
}
