<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * The method returns a template for the main page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke()
    {
        return $this->render(
            'base.html.twig'
        );
    }
}
