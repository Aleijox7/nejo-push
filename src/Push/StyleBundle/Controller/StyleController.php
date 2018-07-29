<?php

namespace Push\StyleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class StyleController extends Controller
{
	public function styleAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$token = $request->getSession()->get('jwt');

		$colorPrincipal = 'ff0052';
		$colorSecundario = '8e2b88';

		// $colorPrincipal = 'd92d77';
		$colorSecundario = 'ee583f';

		if ($token != NULL) {
			$tokenUser = $em->getRepository('PushEntityBundle:UserToken')->findOneByToken($token);

			if ($tokenUser != NULL) {
				$user = $tokenUser->getUser();
				$client = $user->getClient();
				if ($client != NULL) {
					$colorPrincipal = $client->getPrimaryColor();
					$colorSecundario = $client->getPrimaryColor();
					if ($client->getSecondaryColor() != NULL) {
						$colorSecundario = $client->getSecondaryColor();
					}
				}
			}
		}

		$coloresPrincipales = $this->getGradient($colorPrincipal);
		$coloresSecundarios = $this->getGradient($colorSecundario);
		
		// $response = new Response($this->renderView('PushStyleBundle:Default:style_old.css.twig',array(
		// 		'colorPrincipal' => $colorPrincipal, 
		// 		'colorSecundario' => $colorSecundario, 
		// 		'coloresPrincipales' => $coloresPrincipales, 
		// 		'coloresSecundarios' => $coloresSecundarios, 
		// )));

		$response = new Response($this->renderView('PushStyleBundle:Default:style.css.twig',array(
				'colorPrincipal' => $colorPrincipal, 
				'colorSecundario' => $colorSecundario, 
				'coloresPrincipales' => $coloresPrincipales, 
				'coloresSecundarios' => $coloresSecundarios, 
		)));
		$response->headers->set('Content-Type', 'text/css');

		return $response;
	}

	public function rainJSAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$token = $request->getSession()->get('jwt');

		$colorPrincipal = 'ff0052';
		$colorSecundario = '8e2b88';

		// $colorPrincipal = 'd92d77';
		$colorSecundario = 'ee583f';

		if ($token != NULL) {
			$tokenUser = $em->getRepository('PushEntityBundle:UserToken')->findOneByToken($token);

			if ($tokenUser != NULL) {
				$user = $tokenUser->getUser();
				$client = $user->getClient();
				if ($client != NULL) {
					$colorPrincipal = $client->getPrimaryColor();
					$colorSecundario = $client->getPrimaryColor();
					if ($client->getSecondaryColor() != NULL) {
						$colorSecundario = $client->getSecondaryColor();
					}
				}
			}
		}

		$coloresPrincipales = $this->getGradient($colorPrincipal);
		$coloresSecundarios = $this->getGradient($colorSecundario);
		
		// $response = new Response($this->renderView('PushStyleBundle:Default:style_old.css.twig',array(
		// 		'colorPrincipal' => $colorPrincipal, 
		// 		'colorSecundario' => $colorSecundario, 
		// 		'coloresPrincipales' => $coloresPrincipales, 
		// 		'coloresSecundarios' => $coloresSecundarios, 
		// )));

		$response = new Response($this->renderView('PushStyleBundle:Default:rain.js.twig',array(
				'colorPrincipal' => $colorPrincipal, 
				'colorSecundario' => $colorSecundario, 
				'coloresPrincipales' => $coloresPrincipales, 
				'coloresSecundarios' => $coloresSecundarios, 
		)));
		$response->headers->set('Content-Type', 'text/javascript');

		return $response;
	}

	function getGradient($HexFrom, $HexTo=0, $ColorSteps=9) {
		$FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
		$FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
		$FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

		$ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
		$ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
		$ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

		$StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps - 0.97);
		$StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps - 0.97);
		$StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps - 0.97);

		$gradientColors = array();

		for($i = 0; $i <= $ColorSteps; $i++) {
			$RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
			$RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
			$RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

			$HexRGB['r'] = sprintf('%02x', ($RGB['r']));
			$HexRGB['g'] = sprintf('%02x', ($RGB['g']));
			$HexRGB['b'] = sprintf('%02x', ($RGB['b']));

			$aux = array();
			$aux['HEX'] = implode(NULL, $HexRGB);
			$aux['RGB'] = $RGB;

			$gradientColors[] = $aux;
		}
		$gradientColors = array_filter($gradientColors, array($this,"len"));
		return $gradientColors;
	}

	function len($val){
		return (strlen($val['HEX']) == 6 && $val['HEX'] != '000000' ? true : false );
	}
}
