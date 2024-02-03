<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    public function __invoke()
    {
        return $this->render(
            'base.html.twig'
        );
    }
}
