<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $fixtures;

    protected function setUp()
    {
        $this->fixtures = $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadUserData',
            'AppBundle\DataFixtures\ORM\LoadTaskData',
        ))->getReferenceRepository();
    }

    public function testIndexAction()
    {
    	// Unauthorized request
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(302, $client);

        // Authorized request
        $this->loginAs($this->fixtures->getReference('user01'), 'main');
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/');
        $this->assertStatusCode(200, $client);
        $this->assertContains('ToDoList App', $crawler->filter('a')->text());
    }

}
