<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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
        $crawler = $client->request('GET', '/users');
        $this->assertStatusCode(302, $client); //redirect to login

        // Authenticated but unauthorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users');
        $this->assertStatusCode(403, $client);

        // Authenticated and authorized request
        $this->loginAs($this->fixtures->getReference('admin01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users');
        $this->assertStatusCode(200, $client);
        $this->assertContains('Liste des utilisateurs', $crawler->filter('h1')->text());
    }

    public function testCreateAction()
    {
    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/create');
        $this->assertStatusCode(302, $client); //redirect to login

        // Authenticated but unauthorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/create');
        $this->assertStatusCode(403, $client);

        // Authenticated and authorized request
        $this->loginAs($this->fixtures->getReference('admin01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/create');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => '', 'user[password][first]' => '', 'user[password][second]' => '', 'user[email]' => '', 'user[roles]' => array()]);
        $crawler = $client->submit($form);
        // We should get a validation error for the empty fields
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.username', 'data.email'], $client->getContainer());

        // Try again with the fields filled out but username already used
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => 'user01', 'user[password][first]' => 'user01', 'user[password][second]' => 'user01', 'user[email]' => 'test@test.com', 'user[roles]' => array('ROLE_USER')]);
        $client->submit($form);
        // We should get a validation error for the username field
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.username'], $client->getContainer());

        // Try again with the fields filled out but email already used
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => 'testUser', 'user[password][first]' => 'testUser', 'user[password][second]' => 'testUser', 'user[email]' => 'user01@user.com', 'user[roles]' => array('ROLE_USER')]);
        $client->submit($form);
        // We should get a validation error for the email field
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.email'], $client->getContainer());

        // Try again with the fields filled out correctly
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => 'test01', 'user[password][first]' => 'test01', 'user[password][second]' => 'test01', 'user[email]' => 'test01@test.com', 'user[roles]' => array('ROLE_USER')]);
        $client->followRedirects();
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $crawler2 = $client->getCrawler();
        $this->assertContains('L\'utilisateur a bien été ajouté.', $crawler2->filter('div.alert-success')->text());

        // Test if the new task user is correctly set
        $newUser = $this->em->getRepository('AppBundle:User')->findByUsername('test01');
        $this->assertCount(1, $newUser);
    }

    public function testEditAction()
    {
    	$myUser = $this->fixtures->getReference('user01');
        $adminUser = $this->fixtures->getReference('admin01');
        
    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/edit/'.$adminUser->getId());
        $this->assertStatusCode(302, $client); //redirect to login

        // Authenticated but unauthorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/edit/'.$adminUser->getId());
        $this->assertStatusCode(403, $client);

        // Authenticated and unauthorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/edit/'.$myUser->getId());
        $this->assertStatusCode(200, $client);

        // Modifying the user values incorrectly
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => '', 'user[password][first]' => '', 'user[password][second]' => '', 'user[email]' => '']);
        $crawler = $client->submit($form);
        // We should get a validation error for the empty fields
        $this->assertStatusCode(200, $client);
        $this->assertValidationErrors(['data.username', 'data.email'], $client->getContainer());

        // Try again with the fields filled out correctly
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => 'tests', 'user[password][first]' => 'tests', 'user[password][second]' => 'tests', 'user[email]' => 'tests@tests.com']);
        $client->followRedirects();
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $crawler2 = $client->getCrawler();
        $this->assertContains("L'utilisateur a bien été modifié", $crawler2->filter('div.alert-success')->text());

        // Test if the new task user is correctly set
        $modifiedUser = $this->em->getRepository('AppBundle:User')->find($myUser->getId());
        $this->assertEquals('tests', $modifiedUser->getUsername());

        $this->setUp();
        $myUser = $this->fixtures->getReference('user01');
    	$adminUser = $this->fixtures->getReference('admin01');

        // Test if admin can edit other accounts
        $this->loginAs($this->fixtures->getReference('admin01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/users/edit/'.$myUser->getId());
        $this->assertStatusCode(200, $client);

        // Try again with the fields filled out correctly
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['user[username]' => 'user01', 'user[password][first]' => 'user01', 'user[password][second]' => 'user01', 'user[email]' => 'testAdmin@test.com', 'user[roles]' => array(0 => false, 1 => true)]);
        $client->followRedirects();
        $client->submit($form);
        $this->assertStatusCode(200, $client);
        $crawler3 = $client->getCrawler();
        $this->assertContains("L'utilisateur a bien été modifié", $crawler2->filter('div.alert-success')->text());
        $otherModifiedUser = $this->em->getRepository('AppBundle:User')->find($myUser->getId());
        $this->assertEquals(array('ROLE_ADMIN'), $otherModifiedUser->getRoles());
    }
}
