<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Task;

class LoadTaskData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tasks = 
            [
                [
                    'title'     => 'Tâche créée par un admin',
                    'content'   => 'Super admin !',
                    'user'      => $this->getReference('admin01'),
                    'reference' => 'admin01-task'
                ],
                [
                    'title'     => 'Tâche créée par user01',
                    'content'   => 'Utilisateur normal !',
                    'user'      => $this->getReference('user01'),
                    'reference' => 'user01-task'
                ],
                [
                    'title'     => 'Ancienne tâche non liée',
                    'content'   => 'Tâche anonyme',
                    'user'      => $this->getReference('anonymous'),
                    'reference' => 'anonymous-task'
                ]
            ];

        foreach ($tasks as $t) {
            $task = new Task();
            $task->setTitle($t['title']);
            $task->setContent($t['content']);
            $task->setUser($t['user']);

            $this->addReference($t['reference'], $task);

            $manager->persist($task);
        }
        
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
