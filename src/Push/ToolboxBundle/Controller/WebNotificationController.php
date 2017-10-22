<?php

namespace Push\ToolboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Doctrine\ORM\EntityRepository;
use Firebase\JWT\JWT;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\VAPID;
use Push\EntityBundle\Entity\WebNotification;

class WebNotificationController extends FOSRestController
{
	/**
	* @Route("/register-notification-user/", name="register_notification_user")
	* @Method({"POST"})
	*
	*/
	public function registerNotificationUserAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$view = View::create();

		$tokenValidate = $this->forward('PushToolboxBundle:ToolboxApi:validateAccess')->getContent();
        $tokenValidate = json_decode($tokenValidate,true);

        if ($tokenValidate['status'] == 0) {
        	$view->setStatusCode('400');

        	$view->setData(array(
        		'status' => 0,
        		'msg' => $tokenValidate['msg'],
        		'results' => NULL,
        		)
        	);
        	$view->setFormat('json');

			return $this->handleView($view);
        }

        // $business = $em->getRepository('PushEntityBundle:UserBusiness')->findOneByUser($tokenValidate['results']['user']['id'])->getBusiness();
        $user = $em->getRepository('PushEntityBundle:User')->findOneById($tokenValidate['results']['user']['id']);

        $endpoint = $request->request->get('endpoint');
        $auth = $request->request->get('auth');
        $publicKey = $request->request->get('publicKey');

        if ($endpoint != NULL && $auth != NULL && $publicKey != NULL && $user != NULL) {
        	$webNotificationVerify = $em->getRepository('PushEntityBundle:WebNotification')->findOneBy(array('user'=>$user->getId(),'endpoint'=>$endpoint,'auth'=>$auth,'publicKey'=>$publicKey));
        	if ($webNotificationVerify != NULL) {
        		$webNotification = $webNotificationVerify;
        	} else {
        		$webNotification = new WebNotification();

        		$webNotification->setUser($user);
		        // $webNotification->setBusiness($business);
		        $webNotification->setAuth($auth);
		        $webNotification->setPublicKey($publicKey);
		        $webNotification->setEndpoint($endpoint);

		        $em->persist($webNotification);
		        $em->flush();
        	}

	        $view->setStatusCode('200');
	        $view->setData(array(
	            'status' => 1,
	            'msg' => 'Usuario registrado correctamente',
	            'results' => $webNotification
	            ));
        } else {
        	$view->setStatusCode('400');
	        $view->setData(array(
	            'status' => 0,
	            'msg' => 'Faltan datos',
	            'results' => NULL
	            ));
	                
	        $view->setFormat('json');

			return $this->handleView($view);
        }
                
        $view->setFormat('json');

		return $this->handleView($view);
	}

    /**
	* @Route("/send-notifications/")
	* @Method({"POST"})
	* 
	*
	*/
	public function sendNotificationsAction(Request $request) {
		// var_dump(VAPID::createVapidKeys());
		// exit();
		$em = $this->getDoctrine()->getManager();
		$view = View::create();

		$tokenValidate = $this->forward('PushToolboxBundle:ToolboxApi:validateAccess')->getContent();
        $tokenValidate = json_decode($tokenValidate,true);

        if ($tokenValidate['status'] == 0) {
        	$view->setStatusCode('400');

        	$view->setData(array(
        		'status' => 0,
        		'msg' => $tokenValidate['msg'],
        		'results' => NULL,
        		)
        	);
        	$view->setFormat('json');

			return $this->handleView($view);
        }

        // $business = $em->getRepository('PushEntityBundle:UserBusiness')->findOneByUser($tokenValidate['results']['user']['id'])->getBusiness();

		$notifications = array();

		$webNotifications = $em->getRepository('PushEntityBundle:WebNotification')->findByUser($tokenValidate['results']['user']['id']);

		foreach ($webNotifications as $webNotification) {
			$aux = array();

			$aux['endpoint'] = $webNotification->getEndpoint();
		    $aux['payload'] = '{"msg":"Nuevos servicios añadidos para asignar a los mensajeros", "title":"Aplicación RBD", "icon":"https://rimdevblog.files.wordpress.com/2015/04/push-button.png","badge":"https://rimdevblog.files.wordpress.com/2015/04/push-button.png", "image":"https://rimdevblog.files.wordpress.com/2015/04/push-button.png", "cancel":"https://rimdevblog.files.wordpress.com/2015/04/push-button.png"}';
		    $aux['userPublicKey'] = $webNotification->getPublicKey();
		    $aux['userAuthToken'] = $webNotification->getAuth();

		    $notifications[] = $aux;
		}

		$auth = array(
		    'GCM' => 'MY_GCM_API_KEY', // deprecated and optional, it's here only for compatibility reasons
		    'VAPID' => array(
		        'subject' => 'mailto:aleijox_seven@hotmail.com', // can be a mailto: or your website address
		        'publicKey' => $this->container->getParameter('vapid_public_key'), // (recommended) uncompressed public key P-256 encoded in Base64-URL
		        'privateKey' => $this->container->getParameter('vapid_private_key'), // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
		        // 'pemFile' => 'path/to/pem', // if you have a PEM file and can link to it on your filesystem
		        // 'pem' => 'pemFileContent', // if you have a PEM file and want to hardcode its content
		    ),
		);

		$webPush = new WebPush($auth);

		// send multiple notifications with payload
		foreach ($notifications as $notification) {
			var_dump('e.e');
		    $webPush->sendNotification(
		        $notification['endpoint'],
		        $notification['payload'], // optional (defaults null)
		        $notification['userPublicKey'], // optional (defaults null)
		        $notification['userAuthToken'] // optional (defaults null)
		    );
		}
		exit();
		$webPush->flush();

		// send one notification and flush directly
		// $webPush->sendNotification(
		//     $notifications[0]['endpoint'],
		//     $notifications[0]['payload'], // optional (defaults null)
		//     $notifications[0]['userPublicKey'], // optional (defaults null)
		//     $notifications[0]['userAuthToken'], // optional (defaults null)
		//     true // optional (defaults false)
		// );
	}
}