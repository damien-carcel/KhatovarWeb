<?php

namespace Khatovar\Bundle\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Main Controller for Contact bundle.
 *
 * @author Damien Carcel (https://github.com/damien-carcel)
 */
class ContactController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('KhatovarContactBundle:Contact:index.html.twig');
    }
}
