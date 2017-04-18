<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class AjaxController implements ControllerProviderInterface{
    public function connect(Application $app){
        $indexController = $app['controllers_factory'];
        $indexController->get('/', [$this, 'index']);
        $indexController->post('/message', [$this, 'handleMessage']);
        return $indexController;
    }
    public function index(Application $app){
        // return $app['twig']->render('app.twig');
        return 1;
    }
    public function handleMessage(Application $app){
        // return $app['twig']->render('app.twig');
        return 1;
    }
}
