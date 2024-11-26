<?php

namespace App\Tests\Functional\Controller\User;

use App\Entity\User;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    public function testRegistration(): void
    {
        $faker = Factory::create();
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/registration');

        //проверка регистрации
        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('form[name="user_create_form"]')->form();
        $email =  $faker->email;
        $form['user_create_form[email]'] = $email;
        $form['user_create_form[name]'] = $faker->name;
        $password = $faker->password;
        $form['user_create_form[password]'] = $password;
        $form['user_create_form[confirm_password]'] = $password;
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();
        $this->assertAnySelectorTextContains('a.pixel-font', 'Profile');

        //проверка выхода из системы
        $logoutButton = $crawler->filter('input[type="submit"].pixel-font');
        $this->assertCount(1, $logoutButton, 'Logout button not found.');
        $form = $logoutButton->form();
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();

        //проверка входа в систему
        $form = $crawler->filter('form[name="login"]')->form();
        $form['_email'] = $email;
        $form['_password'] = $password;
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }


    public function testLogout(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('doctrine')->getRepository(User::class)
            ->findOneBy([
                'state' => 'active'
            ]);
        $testUser = $userRepository->findOneByEmail('test@example.com');
        $client->loginUser($testUser);

    }
}