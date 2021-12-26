<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('maxime_the_best_rh@omnilog.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'verybadpassword'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);
        $manager->flush();
    }
}
