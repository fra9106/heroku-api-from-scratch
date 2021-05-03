<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder )
    {
        $this->encoder = $encoder;
    }
    
    private $model = ['iPhone', 'Samsung'];
    private $color = ['black', 'white', 'night green', 'or', 'red', 'blue', 'argent', 'graphite', 'sideral grey'];
    private $go = [ '32', '64', '128', '256'];
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        $shop = new Shop();
        $password = $this->encoder->encodePassword($shop,'toto');
        $shop->setName("La boutique a toto")
        ->setEmail("totoshop@gmail.com")
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword($password)
        ->setAddress($faker->streetAddress())
        ->setCity($faker->city())
        ->setArrivalDate(new \DateTime());
        
        $shop1 = new Shop();
        $password = $this->encoder->encodePassword($shop1,'titi');
        $shop1->setName("La boutique a titi")
        ->setEmail("titishop@gmail.com")
        ->setRoles(['ROLE_USER'])
        ->setPassword($password)
        ->setAddress($faker->streetAddress())
        ->setCity($faker->city())
        ->setArrivalDate(new \DateTime());
        
        $shop2 = new Shop();
        $password = $this->encoder->encodePassword($shop2,'tata');
        $shop2->setName("La boutique a tata")
        ->setEmail("tatashop@gmail.com")
        ->setRoles(['ROLE_USER'])
        ->setPassword($password)
        ->setAddress($faker->streetAddress())
        ->setCity($faker->city())
        ->setArrivalDate(new \DateTime());
        
        for($i = 1; $i <= 20; $i++) {
            $phone = new Phone();
            $phone->setModel($this->model[rand(0,1)]. ' ' . rand(5, 12));
            $phone->setColor($this->color[rand(0,8)]);
            $phone->setPrice(rand(500, 1000));
            $phone->setDescription('A wonderful phone with ' . $this->go[rand(0,3)] . ' Go');
            
            $manager->persist($phone);
        }
        
        for ( $u = 0; $u < 10; $u++){
            $user = new User();
            $user->setEmail("user$u@orange.fr")
            ->setFirstName($faker->firstNameMale())
            ->setLastName($faker->lastName())
            ->setAddress($faker->streetAddress())
            ->setPostalCode($faker->postcode())
            ->setCity($faker->city())
            ->setCreatedAt(new \DateTime())
            ->setShop($shop);
            
            $manager->persist($user);
        }
        
        for ( $u = 0; $u < 5; $u++){
            $user = new User();
            $user->setEmail("user$u@sfr.fr")
            ->setFirstName($faker->firstNameMale())
            ->setLastName($faker->lastName())
            ->setAddress($faker->streetAddress())
            ->setPostalCode($faker->postcode())
            ->setCity($faker->city())
            ->setCreatedAt(new \DateTime())
            ->setShop($shop1);
            
            $manager->persist($user);
        }
        
        for ( $u = 0; $u < 5; $u++){
            $user = new User();
            $user->setEmail("user$u@free.fr")
            ->setFirstName($faker->firstNameMale())
            ->setLastName($faker->lastName())
            ->setAddress($faker->streetAddress())
            ->setPostalCode($faker->postcode())
            ->setCity($faker->city())
            ->setCreatedAt(new \DateTime())
            ->setShop($shop2);
            
            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
