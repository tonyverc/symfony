<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
  {
     private $faker;
     private $hasher;
     private $manager;

     public function __construct(UserPasswordHasherInterface $hasher)
     {
         $this->faker = FakerFactory::create('fr_FR');
         $this->hasher = $hasher;
     }

     public static function getGroups(): array
     {
         return ['prod', 'test'];
     }




    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
        $this->manager = $manager;
        $this->loadAdmins();
    }
   
    public function loadAdmins(): void
    {   
        // données statiques
        $datas =[
            [
                'email' => 'admin@example.com',
                'password' => '123',
                'roles' => ['ROLE_ADMIN'],
            ],
        ];

        foreach ($datas as $data){

            $user = new User();
            $user ->setEmail('admin@example.com');
            $password = $this->hasher->hashPassword($user,'123');
            $user ->setPassword($password);
            $user ->setRoles(['ROLE_ADMIN']);

        /* INSERT INTO user
        (email,password,roles)
        VALUES
        ('admin@example.com','123','[ROLE_ADMIN]')

        code SQL génerer par cette requete*/

            $this ->manager->persist($user); /*commande pour stocker en base de données*/
        }

        $this ->manager->flush(); /* genere le code SQL pour rentrer le code en base e données*/
    }
  }