<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\Categorie;
use App\Entity\User;
use App\Entity\Article;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $userTab = [];
        $catTab = [];
        for($i=0;$i<10;$i++){
            $cat = new Categorie();
            $cat ->setNom('news'.$i+1);
            $manager->persist($cat);
            $catTab[] = $cat;
        }
        for($i=0;$i<5;$i++){
            $user = new User();
            $user->setNom($faker->lastName());
            $user->setPrenom($faker->firstName());
            $user->setEmail($faker->email());
            $user->setPassword(password_hash('1234',PASSWORD_DEFAULT));
            $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
            $manager->persist($user);
            $userTab[] = $user;
        }
        for($i=0;$i<10;$i++){
            $article = new Article();
            $article->setTitre($faker->Words(3,true));
            $article->setContenu($faker->sentence(3));
            $article->setDate(new \DateTimeImmutable($faker->date('y-m-d')));
            $article->setUser($userTab[$faker->numberBetween(0,4)]);
            $article->addCategorie($catTab[$faker->numberBetween(0,2)]);
            $article->addCategorie($catTab[$faker->numberBetween(3,5)]);
            $article->addCategorie($catTab[$faker->numberBetween(6,9)]);
            $manager->persist($article);
        }
        $manager->flush();
    }}
