<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;

class IndexController implements ControllerProviderInterface{
    public function connect(Application $app){
        $indexController = $app['controllers_factory'];
        $indexController->get('/', [$this, 'index']);
        $indexController->get('/help', [$this, 'help']);
        $indexController->get('/about', [$this, 'about']);
        return $indexController;
    }
    public function index(Application $app){
        return $app['twig']->render('index.twig', [
            'fulltitle' => $app['conf.title'].' - The prototype of a new index',
            'meta' => [
                'og' => [
                    'title' => $app['conf.title'].' - The prototype of a new index',
                    'description' => 'Ofnibot offers you an interesting, funny and easy way of finding and comparing different entities on the internet, using some of the biggest databases of movies, songs, product barcodes and a lot more.',
                    'image' => a_link('/images/cover.png')
                ]
            ]
        ]);
    }
    public function help(Application $app){
        return $app['twig']->render('help.twig');
    }
    public function about(Application $app){
        return $app['twig']->render('about.twig');
    }
}
