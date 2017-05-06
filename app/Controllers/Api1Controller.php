<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AiClasses\IdentifierClass;

class Api1Controller implements ControllerProviderInterface{
    public function connect(Application $app){
        $indexController = $app['controllers_factory'];
        $indexController->match('/', [$this, 'index']);
        $indexController->get('/movie', [$this, 'index']);
        return $indexController;
    }
    public function index(Application $app){
        // return $app['twig']->render('app.twig');
        return 1;
    }
    public function handleMessage(Application $app){
        // return $app['twig']->render('app.twig');
        $data = $app['request']->request->all();

        $idf = new IdentifierClass($app, $data['message']);

        if($msss = $idf->firstCheck()){
            $message = $msss;
        }else if($msss = $idf->expectedReply()){
            $message = $msss;
        }else if($msss = $idf->mainCheck()){
            $message = $msss;
        }else{
            $message = $idf->fallbackMessage();
        }

        $resp = new JsonResponse();
	    $resp->setData([
            'response' => $message
        ]);
	    return $resp;
    }
}
