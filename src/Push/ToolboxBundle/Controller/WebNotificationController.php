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
use Minishlink\WebPush\Subscription;
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
        $reference = $request->request->get('reference');

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
		        if ($reference != NULL) {
		        	$webNotification->setReference($reference);
		        } else {
		        	$webNotification->setReference($user->getId());
		        }

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
	* @Route("/send-notifications/", name="send_notifications")
	* @Method({"POST"})
	* 
	*
	*/
	public function sendNotificationsAction(Request $request) {
		// dump($request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() .'/' );exit();
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

		$idUser = $tokenValidate['results']['user']['id'];

		$requestIdUser = $request->request->get('id');

		if ($requestIdUser != NULL && is_numeric($requestIdUser)) {
			$idUser = $requestIdUser;
		}

		$webNotifications = $em->getRepository('PushEntityBundle:WebNotification')->findByUser($idUser);

		foreach ($webNotifications as $webNotification) {
			$aux = array();
		    // $aux['payload'] = 'TEST';
		    $payload = array();
		    $payload['body'] = "Nueva gama de sabores Chicles 5";
		    $payload['title'] = "Chicles 5!!";
		    $payload['badge'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/five-badge.png";
		    $payload['icon'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/five-logo.png";
		    $payload['image'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/five.jpg";
		    // $payload['silent'] = true;
		    $payload['renotify'] = true;
		    $payload['tag'] = 'go-to-service-action';		    

		    $payload['actions'] = array();

		    $actionAccept = array();
		    $actionAccept['action'] = "go-to-service-action";
		    $actionAccept['title'] = "Ver trailer";
		    $actionAccept['icon'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/check.png";
		    $actionAccept['url'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/five-sound.mp3";
		    // $actionAccept['url'] = $this->generateUrl('login_form');
		    array_push($payload['actions'], $actionAccept);

		    $actionCancel = array();
		    $actionCancel['action'] = "cancel-service-action";
		    $actionCancel['title'] = "Cancelar";
		    $actionCancel['icon'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/cancel.png";
		    $actionCancel['url'] = $this->container->getParameter('server_url') . "git/pushNotification/web/" . "public/img/push/five-sound.mp3";
		    // array_push($payload['actions'], $actionCancel);


		    $aux['payload'] = json_encode($payload,true);
			
			$aux['subscription'] = Subscription::create([
				'endpoint' => $webNotification->getEndpoint(), // Firefox 43+,
				'publicKey' => $webNotification->getPublicKey(), // base 64 encoded, should be 88 chars
				'authToken' => $webNotification->getAuth(), // base 64 encoded, should be 24 chars
			]);

		    $notifications[] = $aux;
		}

		dump($notifications);

		$auth = array(
		    // 'GCM' => $this->container->getParameter('push_api_key'), // deprecated and optional, it's here only for compatibility reasons
		    'VAPID' => array(
		        'subject' => 'mailto:aleijox_seven@hotmail.com', // can be a mailto: or your website address
		        'publicKey' => $this->container->getParameter('vapid_public_key'), // (recommended) uncompressed public key P-256 encoded in Base64-URL
		        'privateKey' => $this->container->getParameter('vapid_private_key'), // (recommended) in fact the secret multiplier of the private key encoded in Base64-URL
		        // 'pemFile' => 'path/to/pem', // if you have a PEM file and can link to it on your filesystem
		        // 'pem' => 'pemFileContent', // if you have a PEM file and want to hardcode its content
		    ),
		);

		dump($auth);
		$webPush = new WebPush($auth);
		dump($webPush);

		// send multiple notifications with payload
		foreach ($notifications as $notification) {
		    dump($webPush->sendNotification(
		        $notification['subscription'],
		        'Hi'
		    ));
		}
		$webPush->flush();

		$view->setStatusCode('200');

		$view->setData(array(
			'status' => 1,
			'msg' => 'Notificaciones enviadas correctamente',
			'results' => NULL,
		));

		$view->setFormat('json');

		return $this->handleView($view);
	}
}