<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class AppController implements ControllerProviderInterface{
    public function connect(Application $app){
        $indexController = $app['controllers_factory'];
        $indexController->get('/', [$this, 'index']);
        return $indexController;
    }
    public function index(Application $app){
        return $app['twig']->render('app.twig', [
            'title' => 'Chat'
        ]);
    }
}
