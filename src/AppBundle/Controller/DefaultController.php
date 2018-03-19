<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$redirect = $this->forward('ApiBundle:Session:controlSession')->getContent();
        $redirect = json_decode($redirect,true);
        if ($redirect['status'] == 1) {
            return $this->redirectToRoute('login_form');
        }
        return $this->render('AppBundle:Default:index.html.twig');
    }
}
