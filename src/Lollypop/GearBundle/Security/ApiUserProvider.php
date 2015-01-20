<?php

namespace Lollypop\GearBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ApiUserProvider implements UserProviderInterface {

    private $doctrine;
    private $user;

    public function __construct(RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function getUsernameForApiKey($apiKey) {
        $gcmRegIdRepo = $this->doctrine->getRepository('LollypopGearBundle:GCMRegID');
        $gcmRegId = $gcmRegIdRepo->findOneBy(array('value' => $apiKey));
        if ($gcmRegId) {
            $this->user = $gcmRegId->getUser();
            $username = null;
            if($this->user){
                $username = $this->user->getUsername();
            }

            return empty($username) ? $this->user->getEmail() : $username;
        }
        
        return null;
    }

    public function loadUserByUsername($username) {
        return new User(
                $username, null,
                // the roles for the user - you may choose to determine
                // these dynamically somehow based on the user
                empty($this->user) ? array() : $this->user->getRoles()
        );
    }

    public function refreshUser(UserInterface $user) {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class) {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }

}
