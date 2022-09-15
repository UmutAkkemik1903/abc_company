<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $users = new User();
        $plaintextPassword1 = 'acompany';
        $plaintextPassword2 = 'bcompany';
        $plaintextPassword3 = 'ccompany';
        $this->hashedPassword1 = $passwordHasher->hashPassword(
            $users,
            $plaintextPassword1
        );
        $this->hashedPassword2 = $passwordHasher->hashPassword(
            $users,
            $plaintextPassword2
        );
        $this->hashedPassword3 = $passwordHasher->hashPassword(
            $users,
            $plaintextPassword3
        );
    }
    public function load(ObjectManager $manager): void
    {
        $users = new User();
        $users2 = new User();
        $users3 = new User();
        $users->setEmail('acompany@gmail.com');
        $users->setPassword($this->hashedPassword1);
        $users2->setEmail('bcompany@gmail.com');
        $users2->setPassword($this->hashedPassword2);
        $users3->setEmail('ccompany@gmail.com');
        $users3->setPassword($this->hashedPassword3);
         $manager->persist($users);
         $manager->persist($users2);
         $manager->persist($users3);

        $manager->flush();
    }
}
