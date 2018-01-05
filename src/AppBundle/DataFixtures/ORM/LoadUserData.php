<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $encoder = $this->container->get('security.password_encoder');

        $userAdmin = new User();
        $userAdmin->setUsername('admin01');
        $userAdmin->setPassword($encoder->encodePassword($userAdmin, 'admin01'));
        $userAdmin->setEmail('admin01@admin.com');
        $userAdmin->setRoles(array('ROLE_ADMIN'));
        $manager->persist($userAdmin);
        $this->addReference('admin01', $userAdmin);

        $user = new User();
        $user->setUsername('user01');
        $user->setPassword($encoder->encodePassword($user, 'user01'));
        $user->setEmail('user01@user.com');
        $user->setRoles(array('ROLE_USER'));
        $manager->persist($user);
        $this->addReference('user01', $user);

        $anonymous = new User();
        $anonymous->setUsername('anonymous');
        $anonymous->setPassword('anonymous');
        $anonymous->setEmail('anonymous@anonymous.com');
        $anonymous->setRoles(array('ROLE_USER'));
        $manager->persist($anonymous);
        $this->addReference('anonymous', $anonymous);
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
