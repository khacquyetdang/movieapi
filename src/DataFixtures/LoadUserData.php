<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $user1 = new User();

        $user1->setApiKey("83218ac34c1834c26781fe4bde918ee4");
        $user1->setEmail("khacquyet.dang@gmail.com");
        $user1->setPassword("dhlkhlkdjhfdqs");

        $manager->persist($user1);

        $user2 = new User();

        $user2->setApiKey("9aaa39c281027c8fd7ca333a8a9e022a");
        $user2->setEmail("hongtran283@gmail.com");
        $user2->setPassword("ljdhfqljhld;cnb;,");
        $manager->persist($user2);

        $manager->flush();

    }
}
