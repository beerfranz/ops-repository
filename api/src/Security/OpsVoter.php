<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OpsVoter extends Voter
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        $supportsAttribute = in_array($attribute, ['OPS_READ', 'OPS_WRITE']);

        return $supportsAttribute;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case 'OPS_READ':
                if ( in_array('OPS_ADMIN', $user->getRoles()) or in_array('OPS_READONLY', $user->getRoles()) ) { return true; }
                break;
            case 'OPS_WRITE':
                if ( in_array('OPS_ADMIN', $user->getRoles())) { return true; }
        }

        return false;
    }
}