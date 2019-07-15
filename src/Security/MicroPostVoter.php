<?php

namespace App\Security;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    public function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        } 

        if(!$subject instanceof MicroPost){
            return false;
        }

        return true;
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])){
            return true;
        }

        $authenticatedUser = $token->getUser();
        
        if(!$authenticatedUser instanceof User){
            return false;
        }

        return $subject->getUser()->getId() === $authenticatedUser->getId();
    }
}
