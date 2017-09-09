<?php

namespace MeVisa\ERPBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APIControllerTest extends WebTestCase
{

  public function testGetorders()
  {
    $client = static::createClient();

    $crawler = $client->request('GET', '/getOrders');
  }

}
