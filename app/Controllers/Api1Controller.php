<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AiClasses\IdentifierClass;
use AiClasses\Api1MainClass;
use Symfony\Component\Yaml\Yaml;

class Api1Controller implements ControllerProviderInterface{
    public function connect(Application $app){
        $indexController = $app['controllers_factory'];
        $indexController->match('/', [$this, 'index']);
        $indexController->get('/movies', [$this, 'movies']);
        $indexController->get('/songs', [$this, 'movies']);
        $indexController->get('/barcodes/{barcode}', [$this, 'barcodes']);
        $indexController->get('/barcodes/{countryCode}/{barcode}', [$this, 'barcodes_country']);
        return $indexController;
    }
    public function index(Application $app){
        // return $app['twig']->render('app.twig');
        return 1;
    }

    public function movies(Application $app){
        // Get query parameters
        $data = $app['request']->query->all();

        $apiController = new Api1MainClass($app, $data);
        if(($err_rp = $this->evvErrCheck($apiController)) !== false) return $err_rp;

        $movie_data = $apiController->checkForMovie();
        if(($err_rp = $this->evvErrCheck($apiController)) !== false) return $err_rp;

        return $this->rafRespGenerator($apiController, $movie_data);
    }

    public function songs(Application $app){
        // Get query parameters
        $data = $app['request']->query->all();

        $apiController = new Api1MainClass($app, $data);
        if(($err_rp = $this->evvErrCheck($apiController)) !== false) return $err_rp;

        $movie_data = $apiController->checkForMovie();
        if(($err_rp = $this->evvErrCheck($apiController)) !== false) return $err_rp;

        return $this->rafRespGenerator($apiController, $movie_data);
    }

    function evvErrCheck($apiController){
        if($apiController->error){
            // First check, global
            $pretty = $apiController->isPretty();
            $format = $apiController->getFormat();

            $error = $apiController->error;
            $error_code = $apiController->error_code;
            return $this->genErrorRespose($error, $format, $pretty, $error_code);
        }
        return false;
    }

    function genErrorRespose($text, $type = 'json', $beautify = 0, $code = 400){
        $data = [
            'error' => $text
        ];
        return $this->respGenerator($type, $data, $beautify, $code);
    }

    function rafRespGenerator($apiController, $data){
        $pretty = $apiController->isPretty();
        $format = $apiController->getFormat();
        $code = 200;
        return $this->respGenerator($format, $data, $pretty, $code);
    }

    function respGenerator($type = 'json', $data, $beautify = 0, $code = 200){
        $resp = new Response();

        if($type == 'yaml'){
            $content_type = 'text/yaml';
            $content = Yaml::dump($data);
        }else{
            $content_type = 'application/json';
            if($beautify){
                $content = json_encode($data, JSON_PRETTY_PRINT);
            }else{
                $content = json_encode($data);
            }
        }


        $resp->setCharset('UTF-8');
        $resp->headers->set('Content-Type', $content_type);
        $resp->setContent($content);
        $resp->setStatusCode($code);

        return $resp;
    }
}
