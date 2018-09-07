<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $movie = new Movie();
        $movie->setTitle("Green Mile");
        $movie->setYear(1999);
        $movie->setTime(189);
        $movie->setDescription("Greate movie to watch");

        $manager->persist($movie);

        $movie = new Movie();
        $movie->setTitle("Go with the wind");
        $movie->setYear(1980);
        $movie->setTime(289);
        $movie->setDescription("Very romantic movie");

        $manager->persist($movie);

        $manager->flush();
    }
}
