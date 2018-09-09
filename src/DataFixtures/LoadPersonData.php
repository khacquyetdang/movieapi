<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPersonData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $person1 = new Person();
        $person1->setLastName('DANG');
        $person1->setFirstName('Khac Quyet');
        $person1->setDateOfBirth(new \DateTime('1987-09-24'));
        $manager->persist($person1);

        $son = new Person();
        $son->setLastName('DANG');
        $son->setFirstName('Léon');
        $son->setDateOfBirth(new \DateTime('2016-11-05'));

        $manager->persist($son);

        $wife = new Person();
        $wife->setLastName('TRAN');
        $wife->setFirstName('Thi Thu Hong');
        $wife->setDateOfBirth(new \DateTime('1988-03-28'));
        $manager->persist($wife);

        $manager->flush();

    }
}
