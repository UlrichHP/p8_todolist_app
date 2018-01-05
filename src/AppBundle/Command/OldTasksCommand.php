<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;

class OldTasksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('OldTasks')
            ->setDescription('Old Tasks will be linked to an anonymous user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $oldTasksUnlinked = $em->getRepository('AppBundle:Task')->findByUser(null);

        if (!empty($oldTasksUnlinked)) {

            $anonymousUser = new User();
            $anonymousUser->setUsername('anonymous');
            $anonymousUser->setEmail('anonymous@anonymous.com');
            $anonymousUser->setPassword('anonymous');
            $em->persist($anonymousUser);

            foreach ($oldTasksUnlinked as $task) {
                $task->setUser($anonymousUser);
            }

            $em->flush();

            $output->writeln('All the Old Tasks are connected to an anonymous user.');

        } else {

            $output->writeln('The Old Tasks are already linked to an anonymous user.');

        }
        
    }

}
