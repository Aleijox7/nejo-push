<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class AccessController extends Controller
{
	public function formLoginAction()
    {
        return $this->render('AppBundle:Default:login.html.twig');
    }

    public function loginAction(Request $request)
    {
    	$tokenLogin = $request->getSession()->get('jwt');

    	// dump(hash('sha512', $request->request->get('password')));
    	// exit();

        $request->headers->set('token', $tokenLogin);

    	if ($tokenLogin == NULL) {
    		$em = $this->getDoctrine()->getManager();

    		$username = $request->request->get('username');
    		$password = $request->request->get('password');

    		$loginUser = $this->forward('AppBundle:Session:loginUser', array($request))->getContent();
    		$loginUser = json_decode($loginUser,true);

    		if ($loginUser['status'] != 1) {
                if($loginUser['results'] == "verify") {
                    $link = "apps_infojic_enviar_verificar_usuario";
                    return $this->render('error.html.twig', array(
                        'msg' => $loginUser['msg'],
                        'link' => $link,
                        'username' => $username,
                        'btnPalabra' => "Verificar"
                        )
                    );
                } else {
                    return $this->render('error.html.twig', array(
                        'msg' => $loginUser['msg']
                        )
                    );
                }
    		} else {
    			return $this->redirectToRoute('push_toolbox_homepage');
    		}
    	} else {
    		return $this->redirectToRoute('push_toolbox_homepage');
    	}
    }
}
