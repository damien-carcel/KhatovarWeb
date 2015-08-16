<?php

namespace Khatovar\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Main controller for Web bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class WebController extends Controller
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Displays a page with links for redirection.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function campAction()
    {
        return $this->render('KhatovarWebBundle:Web:camp.html.twig');
    }
}
