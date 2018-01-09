<?php

namespace tests\AppBundle\Command;

use AppBundle\Command\OldTasksCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/*
 * The goal of this test is to confirm the command doesn't need to be run
 * because the old tasks are already linked to an anonymous user
 */
class OldTasksCommandTest extends KernelTestCase
{

    public function testExecute() 
    {
       	self::bootKernel();
	    $application = new Application(self::$kernel);

	    $application->add(new OldTasksCommand());

	    $command = $application->find('OldTasks');
	    $commandTester = new CommandTester($command);
	    $commandTester->execute(array(
	        'command'  => $command->getName(),
	    ));

	    // Verify the output of the command in the console
	    $output = $commandTester->getDisplay();
	    $this->assertContains('The Old Tasks are already linked to an anonymous user.', $output);
    }
}