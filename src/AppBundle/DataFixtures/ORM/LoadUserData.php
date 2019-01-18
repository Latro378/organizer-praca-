<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUserData extends  Fixture

{
    private $e;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->e = $encoder;

    }


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@admin.com');

        $user->setPassword('admin');
              $user->setImie('admin');
              $user->setNazwisko('admin');


        $plainPassword = 'admin';
        $encoded = $this->e->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);
        $manager->persist($user);
        $manager->flush();
    }

//    public function setContainer(ContainerInterface $container = null)
//    {
//        $this->container = $container;
//    }
}