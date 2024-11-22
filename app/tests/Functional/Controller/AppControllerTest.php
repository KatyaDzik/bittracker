<?php

namespace App\Tests\Functional\Controller;

use App\Enum\TorrentFileStatusEnum;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AppControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('span.pixel-font', 'Torrent');
        $this->assertAnySelectorTextContains('a.pixel-font', 'Sign in');
        $this->assertAnySelectorTextContains('a.pixel-font', 'Sign up');

        $form = $crawler->filter('form[name="torrent_file_filter"]')->form();
        $form['torrent_file_filter[status]'] = TorrentFileStatusEnum::Unverified->getValue();
        $client->submit($form);
        $this->assertResponseIsSuccessful();
    }
}