<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {
	
    public function testIndex() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
	
	public function testCreateTask() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/task/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
		
		$buttonCrawlerNode = $crawler->selectButton('Save');
		$form = $buttonCrawlerNode->form();
		
		$client->submit($form, array(
			'form[task]' => 'Wash socks',
			'form[dueDate][year]'  => 2016,
			'form[dueDate][month]'  => 2,
			'form[dueDate][day]'  => 29
		));
		
		$result = $client->followRedirect();
		
		$this->assertContains('Wash socks', $client->getResponse()->getContent());
	}
}
