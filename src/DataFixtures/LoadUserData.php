<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    public function load(ObjectManager $manager)
    {
        $passwordEncoder = $this->container->get("security.password_encoder");

        $user1 = new User();

        $user1->setEmail("khacquyet.dang@gmail.com");
        $user1->setPassword($passwordEncoder->encodePassword($user1, "dhlkhlkdjhfdqs", $user1->getSalt()));

        $manager->persist($user1);

        $user2 = new User();

        $user2->setEmail("hongtran283@gmail.com");
        $user2->setPassword($passwordEncoder->encodePassword($user2, "ljdhfqljhld;cnb;,", $user2->getSalt()));
        $manager->persist($user2);

        $manager->flush();

    }

/**
 *
 */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
