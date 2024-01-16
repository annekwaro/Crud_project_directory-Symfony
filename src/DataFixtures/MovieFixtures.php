<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The dark Knight');
        $movie->setReleaseYear(2008);
        $movie->setDescription('This is the description of the dark Knight');
        $movie->setImagePath('https://images.unsplash.com/photo-1527538079466-b6297ad15363?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        
        //Adds data to the pivot table
        $movie->addActor($this->getReference('actor'));
        $movie->addActor($this->getReference('actor_2'));

        
        $manager->persist($movie);
   
        $movie2 = new Movie();
        $movie2->setTitle('Avengers: The Gladiator');
        $movie2->setReleaseYear(2000);
        $movie2->setDescription('This is the description of the gladiator');
        $movie2->setImagePath('https://images.unsplash.com/photo-1576507271147-48237d97f182?q=80&w=2369&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');

        //Adds data to the pivot table

        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));

        
        $manager->persist($movie2);
        $manager->flush();
   
   
   
   
   
    }
}