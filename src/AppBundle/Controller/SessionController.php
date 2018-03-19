<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Firebase\JWT\JWT;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Push\EntityBundle\Entity\User;
use Push\EntityBundle\Entity\UserToken;

class SessionController extends FOSRestController
{
	/**
	* @Route("/api/login/")
	* @Method({"POST", "GET"})
	* 
	*/
	public function loginUserAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

        $token = $request->headers->get('token');

		$username = $request->request->get('username');
		$password = $request->request->get('password');

		$view = View::create();

		if ($username != "" && $password != "") {

			$password = hash('sha512', $password);

			$usuario = $em->getRepository('PushEntityBundle:User')->findOneBy(array('username' => $username, 'password' => $password));

			if ($usuario != NULL && $usuario->getValidated() == 1) {

				if ($usuario->getVerified() == 1) {

					$idUsuario = $usuario->getId();

					$view->setStatusCode('200');

					$tokenKey = $this->container->getParameter('tokenKey');

					$now = time();
					$then = $now + (24 * 60 * 60);

					$token = array(
						"sub" => $username.$password,
						"iat" => $now,
						"exp" => $then,
						"jti" => 'idusr-' . $idUsuario
						);
					$jwt = JWT::encode($token, $tokenKey);

					$tokenUser = $em->getRepository('PushEntityBundle:UserToken')->findOneByUser($idUsuario);

					if ($tokenUser == NULL) {
						$tokenUser = new UserToken();
						$tokenUser->setToken($jwt);
					} else if ($tokenUser->getExpirationDate() < new \DateTime()) {
						$tokenUser->setToken($jwt);
					}
					$tokenUser->setUser($usuario);
					$tokenUser->setCreationDate( new \DateTime('now') );
					$tokenUser->setExpirationDate( new \DateTime('+8 hours') );

					// $em->clear();
					$em->persist($tokenUser);
					$em->flush();

					// ----------------------------------------------------------------------
	                // ----------------------- BOF: CONTROL DEL LOGIN -----------------------
	                // Creamos la siguiente variable de sesion para usarla en los controllers y poder comprobar si se ha hecho login
					$session = $request->getSession();
	                $session->set('jwt', $tokenUser->getToken()); // Guardamos el token como 'jwt'
	                $session->set('sessionUsername', $usuario->getUsername());
	                // ----------------------- EOF: CONTROL DEL LOGIN -----------------------
	                // ----------------------------------------------------------------------
	                
	                $view->setData(array(
	                	'status' => 1,
	                	'msg' => 'Usuario encontrado',
	                	'results' => array('token' => $tokenUser->getToken()),
	                	)
	                );

	            } else {
	            	$view->setStatusCode('400');

	            	$view->setData(array(
	            		'status' => 0,
	            		'msg' => 'El usuario no está verificado',
	            		'results' => "verify",
	            		)
	            	);
	            }

	        } else {
	        	$view->setStatusCode('400');

	        	$view->setData(array(
	        		'status' => 0,
	        		'msg' => 'No hay un usuario con esos datos o está deshabilitado',
	        		'results' => NULL,
	        		)
	        	);
	        }
	    } else {
	    	$view->setStatusCode('400');

	    	$view->setData(array(
	    		'status' => 0,
	    		'msg' => 'No se han proporcionado todos los datos',
	    		'results' => NULL,
	    		)
	    	);
	    }

	    $view->setFormat('json');

	    return $this->handleView($view);
	}

    public function controlSessionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tokenKey = $this->container->getParameter('tokenKey');
        // ----------------------------------------------------------------------
        // ----------------------- BOF: CONTROL DEL LOGIN -----------------------
        // Comprobamos el token si es correcto, si no lo es redirigiremos al login, si lo es, no hacemos nada:
        
        $sessionLogin = new Session();
        $tokenLogin = $sessionLogin->get('jwt');
        $urlARedirigir = 'login_form'; // IMPORTANTE: url a redirigir dada de alta en el archivo routing.yml correspondiente
        $urlActual = $request->attributes->get("_forwarded")->get("_route");
        $response = array();

        $response['url'] = $urlARedirigir;

        $noLogged = true;
        
        if (is_null($tokenLogin)) {
            $redirect = true;
        } else {
            $datosTokenUser = $em->getRepository('PushEntityBundle:UserToken')->findOneByToken($tokenLogin);

            if ($datosTokenUser == NULL || $datosTokenUser->getExpirationDate() < new \DateTime('now')) {
                $redirect = true;
            } else {
                $datosTokenUser->setExpirationDate(new \DateTime('+8 hours'));

                $em->persist($datosTokenUser);
                $em->flush();

                $usuario = $datosTokenUser->getUser();
                //Creamos la variable de session para poder comprovar el rol de usuario en control de flotas
                
                // $rolUser = $em->getRepository('PushEntityBundle:UserRol')->findOneByUser($datosTokenUser->getUser());
                // if ($rolUser != NULL) {
                //     $sessionLogin->set('sessionRolName', array('name' => $rolUser->getRol()->getName(), 'id' => $rolUser->getRol()->getId()));
                //     $sessionLogin->set('sessionIdUser', $usuario->getId());
                //     $sessionLogin->set('sessionUsername', $usuario->getUsername());
                //     $sessionLogin->set('sessionImage', $usuario->getUrlImage());

                //     $redirect = false;
                //     $noLogged = false;
                // } else {
                //     $redirect = true;
                //     $noLogged = true;
                // }

                $redirect = false;
                $noLogged = false;
            }
        }

        $view = View::create();

        if ($redirect == false && $urlActual != "index" && $urlActual != "login_form") {
            $urlARedirigir = $sessionLogin->get('url');

            // $redirect = $this->controlRoutingAction($usuario, $urlActual);
        }

        if ($redirect) {
            if ($noLogged == true) {
                $sessionLogin->remove('jwt');
                $sessionLogin->remove('sessionUsername');
                $sessionLogin->remove('sessionImage');
            }
            
            $view->setData(array(
                'status' => 1,
                'msg' => 'Redirect: YES',
                'results' => $urlARedirigir,
                )
            );
        } else {

            $sessionLogin->set('url', $urlActual);

            $view->setData(array(
                'status' => 0,
                'msg' => 'Redirect: NO',
                'results' => $tokenLogin,
                )
            );
        }

        $view->setFormat('json');

        return $this->handleView($view);
        // ----------------------- EOF: CONTROL DEL LOGIN -----------------------
        // ----------------------------------------------------------------------
    }

    public function controlRoutingAction($usuario, $urlActual) {
        $em = $this->getDoctrine()->getManager();

        $rolesUsuario = $em->getRepository('PushEntityBundle:UserRol')->findByUser($usuario);

        $roles = array();

        if ($rolesUsuario == NULL) {
            return true;
        }

        foreach ($rolesUsuario as $rolUsuario) {
            array_push($roles, $rolUsuario->getRol());
        }

        $aplicaciones = array();

        foreach ($roles as $rol) {
            if ($rol->getName() == "SUPER_ADMINISTRADOR" || $rol->getName() == "TRAFICO" || $rol->getName() == "CLIENT_TRAFICO") {
                return false;
            } else {
                $routingsRol = $em->getRepository('PushEntityBundle:IaRoutingRol')->findByRol($rol);
                foreach ($routingsRol as $routingRol) {
                    $routing = $routingRol->getRouting();
                    if ($routing->getRoutingTitle() == $urlActual) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}
