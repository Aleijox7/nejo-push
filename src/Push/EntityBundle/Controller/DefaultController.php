<?php

namespace Push\EntityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PushEntityBundle:Default:index.html.twig');
    }
}
