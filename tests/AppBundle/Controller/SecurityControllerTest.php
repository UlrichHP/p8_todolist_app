<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $fixtures;

    protected function setUp()
    {
        $this->fixtures = $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadUserData',
            'AppBundle\DataFixtures\ORM\LoadTaskData',
        ))->getReferenceRepository();
    }

    public function testLoginAction()
    {
        $client = $this->makeClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // Test with wrong credentials
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['_username' => 'user01', '_password' => 'wrongpassword']);
        $client->submit($form);
        // We should be redirected to login
        $this->assertStatusCode(200, $client);
        $this->assertContains('Nom d\'utilisateur', $crawler->filter('label')->text());

        // Test with good credentials
        $form = $crawler->selectButton('submit')->form();
        $form->setValues(['_username' => 'user01', '_password' => 'user01']);
        $client->submit($form);
        $crawler2 = $client->getCrawler();
        $this->assertContains('Bienvenue sur TodoList, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', $crawler2->filter('h1')->text());
    }
}
