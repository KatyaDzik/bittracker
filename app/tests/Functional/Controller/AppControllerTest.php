<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AppControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('span.pixel-font', 'Torrent');
        $this->assertAnySelectorTextContains('a.pixel-font', 'Sign in');
        $this->assertAnySelectorTextContains('a.pixel-font', 'Sign up');
    }
}