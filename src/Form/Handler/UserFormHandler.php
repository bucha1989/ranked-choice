<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Utils\Manager\UserManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFormHandler
{
    private UserManager $userManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserManager                  $userManager,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userManager = $userManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Form $form
     * @return mixed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function processEditForm(Form $form)
    {
        $plainPassword = $form->get('plainPassword')->getData();
        $newEmail =  $form->get('newEmail')->getData();

        /** @var User $user */
        $user = $form->getData();

        if (!$user->getId()) {
            $user->setEmail($newEmail);
        }

        if ($plainPassword) {
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        $this->userManager->save($user);
        return $user;
    }
}

