<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EventFixtures extends Fixture
{
    public $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");
        for ($i = 0; $i < 10; $i++) { 
            $user = new User();
            $user->setNom($faker->lastName)
            ->setPrenom($faker->firstName)
            ->setPassword($this->encoder->encodePassword($user, 'azerty'))
            ->setEmail($faker->email)
            ->setBirthDate($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'));
            $manager->persist($user);

            $category = new Category();
            $category->setTitle($faker->company)
                  ->setDescription($faker->text($maxNbChars = 20));
                  $manager->persist($category);
         
            for ($j = 0; $j < rand(1,5); $j++) { 
            $event= new Event();
            
            $event->setTitle($faker->jobTitle)
                  ->setDescription($faker->paragraph())
                  ->setImage("https://picsum.photos/200/200")
                  ->setCreatedAt($faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'))
                  ->setUser($user)
                  ->setCategory($category);

            $manager->persist($event);
            }

           
           
            
        }

        
        $manager->flush();
    }
}
