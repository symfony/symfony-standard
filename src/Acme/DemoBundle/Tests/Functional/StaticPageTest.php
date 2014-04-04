<?php

namespace Acme\DemoBundle\Tests\Functional;

use Acme\DemoBundle\Tests\WebTestCase;

class StaticPageTest extends WebTestCase
{
    public function testHomePage()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $title = 'Welcome!';
        $this->assertCount(1, $crawler->filter(sprintf('h1:contains("%s")', $title)), 'Page does not contain an h1 tag with: '.$title);
        $subtitle = 'Symfony2 Content Management Framework';
        $this->assertCount(1, $crawler->filter(sprintf('h2:contains("%s")', $subtitle)), 'Page does not contain an h2 tag with: '.$subtitle);
    }

    public function testDemoPage()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/demo');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $title = 'CMF Demo';
        $this->assertCount(1, $crawler->filter(sprintf('h1:contains("%s")', $title)), 'Page does not contain an h1 tag with: '.$title);
        $subtitle = 'Hello! This page is created by the Symfony CMF.';
        $this->assertCount(1, $crawler->filter(sprintf('p:contains("%s")', $subtitle)), 'Page does not contain an p tag with: '.$subtitle);
    }
}
