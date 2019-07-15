<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @isGranted("ROLE_USER")
 * @Route("/following")
 */
class FollowingController extends AbstractController
{

    /**
     * @Route("/follow/{id}", name="following_follow")
     */
    public function follow(User $userToFollow)
    {
        if($userToFollow->getId() != $this->getUser()->getId()){
            $this->getUser()->follow($userToFollow);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('micro_post_user', ['username' => $userToFollow->getUsername()]);
    }

    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     */
    public function unfollow(User $userToUnfollow)
    {
        if ($userToUnfollow != $this->getUser()) {
            $this->getUser()->getFollowing()->removeElement($userToUnfollow);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->redirectToRoute('micro_post_user', ['username' => $userToUnfollow->getUsername()]);
    }
}

