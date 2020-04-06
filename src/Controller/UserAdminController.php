<?php

namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persistUserEntity($user)
    {
        $this->updatePassword($user);
        parent::persistEntity($user);
    }

    public function updateUserEntity($user)
    {
        $this->updatePassword($user);
        parent::updateEntity($user);
    }

    public function updatePassword(User $user)
    {
        if (!empty($user->getPassword())) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
}