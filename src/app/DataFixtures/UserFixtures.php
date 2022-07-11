<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    protected $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('valid@email.com');
        $user->setPassword($this->encryptPass($user, 'valid-pass'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('valid2@email.com');
        $user->setPassword($this->encryptPass($user, 'valid-pass2'));
        $manager->persist($user);

        $manager->flush();
    }

    public function encryptPass(User $user, string $password): string
    {
        return $this->userPasswordHasher->hashPassword(
            $user,
            $password
        );
    }
}
