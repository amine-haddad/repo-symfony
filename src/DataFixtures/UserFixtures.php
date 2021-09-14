<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;


class UserFixtures extends Fixture
{
    private $passwordHasher;
    //private $passwordEncoder;
    public function __construct( UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher =$passwordHasher;
        //$this->passwordEncoder =$passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker= Faker\Factory::create('fr_FR');
        // Création d’un utilisateur de type “contributeur” (= auteur)
        $contributor = new User() ;
        $contributor->setFirstname($faker->firstName())
        ->setLastname($faker->lastName())
        ->setBirthdayDate($faker->dateTimeBetween('-30 years','-15 years'));
        $contributor->setEmail('contributor@monsite.com');
        $contributor->setRoles(['ROLE_CONTRIBUTOR']);
        $contributor->setPassword($this->passwordHasher->hashPassword(
            $contributor,
            'contributorpassword'
        ));
       
        $manager->persist($contributor);
        
        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setFirstname($faker->firstName())
        ->setLastname($faker->lastName())
        ->setBirthdayDate($faker->dateTimeBetween('-30 years','-15 years'));
        $admin->setEmail('admin@monsite.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        $manager->flush();
    }
}
