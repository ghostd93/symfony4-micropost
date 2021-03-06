<?php

namespace App\Entity;

use App\Entity\Notification;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikeNotificationRepository")
 */
class LikeNotification extends Notification
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MicroPost")
     */
    private $microPost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $likedBy;

    public function getMicroPost()
    {
        return $this->microPost;
    }

    public function setMicroPost($microPost): self
    {
        $this->microPost = $microPost;
        return $this;
    }

    public function getLikedBy()
    {
        return $this->likedBy;
    }

    public function setLikedBy($likedBy): self
    {
        $this->likedBy = $likedBy;
        return $this;
    }
}
