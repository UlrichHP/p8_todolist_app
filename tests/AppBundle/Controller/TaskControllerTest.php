<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $fixtures;
    
	/**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

	protected function setUp()
	{
		self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

		$this->fixtures = $this->loadFixtures(array(
			'AppBundle\DataFixtures\ORM\LoadUserData',
			'AppBundle\DataFixtures\ORM\LoadTaskData',
		))->getReferenceRepository();
	}

    public function testListAction()
    {
    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertStatusCode(302, $client); //redirect to login

        // Authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertStatusCode(200, $client);
        $this->assertContains('Créer une tâche', $crawler->filter('div.col-md-12 a')->text());
    }

    public function testCreateAction()
    {
    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertStatusCode(302, $client); //redirect to login

        // authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertStatusCode(200, $client);
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form);
        // We should get a validation error for the empty fields
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title', 'data.content'], $client->getContainer());

        // Try again with the fields filled out
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['task[title]' => 'Tâche de test', 'task[content]' => 'Tout fonctionne correctement']);
        $client->followRedirects();
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $crawler2 = $client->getCrawler();
        $this->assertContains('La tâche a bien été ajoutée.', $crawler2->filter('div.alert-success')->text());

        // Test if the new task user is correctly set
        $task = $this->em->getRepository('AppBundle:Task')->findByTitle('Tâche de test');
        $this->assertCount(1, $task);
    }

    public function testEditAction()
    {
    	$task = $this->fixtures->getReference('user01-task');

    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/edit/'.$task->getId());
        $this->assertStatusCode(302, $client); //redirect to login

    	// Authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/edit/'.$task->getId());
        $this->assertStatusCode(200, $client);
        
        $form = $crawler->selectButton('submit')->form();
        // Test if values are displayed
        $this->assertEquals($form['task[title]']->getValue(), $task->getTitle());
        $this->assertEquals($form['task[content]']->getValue(), $task->getContent());
        // We should get a validation error for the empty fields
        $form->setValues(['task[title]' => '', 'task[content]' => '']);
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.title', 'data.content'], $client->getContainer());

        // Try again with the fields filled out
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['task[title]' => 'Tâche modifiée par user01', 'task[content]' => 'Le propriétaire est toujours le même !']);
        $client->followRedirects();
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $crawler2 =  $client->getCrawler();
        $this->assertContains('La tâche a bien été modifiée.', $crawler2->filter('div.alert-success')->text());

        // Test if the new task values are correctly registered
        $taskUpdated = $this->em->getRepository('AppBundle:Task')->find($task->getId());
        $this->assertEquals('Tâche modifiée par user01', $taskUpdated->getTitle());
        $this->assertEquals('Le propriétaire est toujours le même !', $taskUpdated->getContent());
    }

    public function testToggleAction()
    {
    	$task = $this->fixtures->getReference('user01-task');
    	// Verify task is not done by default
    	$this->assertEquals(false, $task->isDone());

    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/toggle/'.$task->getId());
        $this->assertStatusCode(302, $client); //redirect to login

        // Authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/toggle/'.$task->getId());
        $this->assertStatusCode(302, $client);

        // Test if it's toggled
        $taskToggled = $this->em->getRepository('AppBundle:Task')->find($task->getId());
        $this->assertEquals(true, $taskToggled->isDone());
    }

    public function testDeleteAction()
    {
    	$task = $this->fixtures->getReference('user01-task');
    	$tasksBeforeDeleting = $this->em->getRepository('AppBundle:Task')->findAll();

    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/delete/'.$task->getId());
        $this->assertStatusCode(302, $client); //redirect to login

        // Authenticated request but not authorized to delete other users tasks
        $this->loginAs($this->fixtures->getReference('admin01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/delete/'.$task->getId()); // user01-task
        $this->assertStatusCode(302, $client);
        $noTaskDeleted = $this->em->getRepository('AppBundle:Task')->findAll();
        $this->assertEquals(count($tasksBeforeDeleting), count($noTaskDeleted));

        // Authenticated and authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/delete/'.$task->getId());
        $this->assertStatusCode(302, $client);
        $taskAfterDeleting = $this->em->getRepository('AppBundle:Task')->findAll();
        $this->assertGreaterThan(count($taskAfterDeleting), count($tasksBeforeDeleting));

	    $anonymousTask = $this->fixtures->getReference('anonymous-task');
	    // Authenticated request but not authorized to delete anonymous task
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/delete/'.$anonymousTask->getId());
        $this->assertStatusCode(302, $client);
        $noTaskDeleted = $this->em->getRepository('AppBundle:Task')->findAll();
        $this->assertEquals(count($tasksBeforeDeleting)-1, count($noTaskDeleted));

        // Authenticated and authorized request
        $this->loginAs($this->fixtures->getReference('admin01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/tasks/delete/'.$anonymousTask->getId());
        $this->assertStatusCode(302, $client);
        $taskAfterDeleting = $this->em->getRepository('AppBundle:Task')->findAll();
        $this->assertGreaterThan(count($taskAfterDeleting)-1, count($tasksBeforeDeleting));
    }
}
