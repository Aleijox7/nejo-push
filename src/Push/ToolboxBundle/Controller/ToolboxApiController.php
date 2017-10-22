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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Firebase\JWT\JWT;

class ToolboxApiController extends FOSRestController
{
  
  /**
	* @Route("/validate-access/")
	* @Method({"POST"})
	* 
	*
	*/
	public function validateAccessAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$token = $request->headers->get('token');

		$view = View::create();

		if ($token != NULL) {
			$userToken = $em->getRepository('PushEntityBundle:UserToken')->findOneByToken($token);

			if ($userToken == NULL) {
				$view->setStatusCode('400');
				$view->setData(array(
					'status' => 0,
					'msg' => 'No se ha encontrado un usuario asignado al token',
					'results' => array()
					));
			} elseif ($userToken->getExpirationDate() < new \DateTime('now')) {
				$view->setStatusCode('400');
				$view->setData(array(
					'status' => 0,
					'msg' => 'La sesion de este token expiro, por favor vuelva a loguearse.',
					'results' => array()
					));
			} else {
				$userToken->setExpirationDate(new \DateTime('+8 hours'));

				$em->persist($userToken);
				$em->flush();

				$view->setStatusCode('200');
				$view->setData(array(
					'status' => 1,
					'msg' => 'Token correcto.',
					'results' => $userToken
					));
			}
		} else {

			$view->setStatusCode('400');
			$view->setData(array(
				'status' => 0,
				'msg' => 'No se ha pasado el token.',
				'results' => array()
				));
		}

		$view->setFormat('json');

		return $this->handleView($view);
	}

	public function enviarEmail($asunto, $para, $cuerpo, $container) {

		$transport = \Swift_SmtpTransport::newInstance($container->getParameter('mailer_host'), $container->getParameter('mailer_port'), $container->getParameter('mailer_encryption'))
		->setUsername($container->getParameter('mailer_user'))
		->setPassword($container->getParameter('mailer_password'))
		;

		if(!filter_var($para, FILTER_VALIDATE_EMAIL)){
			return false;
		}


		$mailer = \Swift_Mailer::newInstance($transport);

		$message = \Swift_Message::newInstance()
		->setSubject($asunto)
		->setFrom($container->getParameter('mailer_user'))
		->setTo($para)
		->setBody($cuerpo, 'text/html');

		if ($mailer->send($message)) {
			return true;
		} else {
			return false;
		}
	}

	public function validateDate($date, $format = 'd/m/Y H:i:s')
	{
		$d = \DateTime::createFromFormat($format, $date);
		if (!$d) {
			return $d;
		} else {
			return $d->format($format) == $date;
		}
	}

	/**
	* @Route("/send-manual-notification/")
	* @Method({"POST"})
	* 
	*
	*/
	public function sendManualNotificationAction(Request $request)
	{
		$idUser = $request->request->get('id');

		if ($idUser == NULL) {
			dump("ERROR");
			exit();
		}

    // dump($this->sendNotification('✅ SUCCESS',$idUser));
    // dump($this->sendNotification('ERROR ❌',$idUser));
		dump($this->sendNotificationAction('✅ ❌ ✅ ❌ ✅ ❌ ✅ ❌ ✅ ❌ ✅ ❌ ✅ ❌ ✅ ❌',$idUser));
		exit();
	}

	public function sendNotificationAction($pushmessage, $idUser, $service_id=0, $id=0) {
    $API_ACCESS_KEY = $container->getParameter('push_api_key');
    
    $view = View::create();
    $em = $this->getDoctrine()->getManager();

        $pushdevices = array();

        $tokenUser = $em->getRepository('AppsInfojicEntityBundle:IaTokenUser')->findOneBy(array('user' => $idUser));

        if ($tokenUser == NULL) {
        	return false;
        }

        $firebaseToken = $tokenUser->getFirebaseToken();

        if ($firebaseToken == NULL) {
        	return false;
        }

        $pushdevices[] = $firebaseToken;
        
       // var_dump($repositoryTM);exit();
        if (count($pushdevices) >= 1) {
            // prep the bundle
            $msg = array
            (
                'body' 	=> $pushmessage,
        				'title'	=> 'RBD APP',
        				'icon'	=> 'myicon',
              	'sound' => 'mySound'
            );

            $headers = array
            (
                'Authorization: key=' . $API_ACCESS_KEY,
                'Content-Type: application/json'
            );
            // loop through devices
            $fields = array
            (
                'to'      => $firebaseToken,
            		// 'registration_ids'      => $pushdevices,
                'data'                  => $msg,
                'notification'			=> $msg,
            );
            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            $result = curl_exec($ch );
            curl_close( $ch );
        }
        $view->setStatusCode('200');

        $view->setData(array(
          'status' => 1,
          'msg' => 'Notificación enviada',
          'results' => $result,
          )
        );

    $view->setFormat('json');

    return $this->handleView($view);
    }
}