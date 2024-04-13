<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_')]
class AppController extends AbstractController
{
    /**
     * The method returns a template for the main page.
     *
     * @param Security $security
     * @return Response
     */
    #[Route(name: 'home')]
    public function __invoke(Security $security): Response
    {
        return $this->render(
            'admin/base.html.twig'
        );
    }
}
