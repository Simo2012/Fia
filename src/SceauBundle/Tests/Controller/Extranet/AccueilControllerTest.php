<?php

namespace SceauBundle\Tests\Controller\Extranet;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccueilControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/extranet/fr/login');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
