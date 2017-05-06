<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AiClasses\IdentifierClass;
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

        $pretty = (isset($data['pretty']) && $data['pretty']) ? true : false;
        $format = (isset($data['format'])) ? $data['format'] : 'json';

        if(!in_array($format, ['json', 'yaml'])){
            return $this->genErrorRespose("Invalid format for output", 'json', $pretty);
        }
        if(!isset($data['name'])){
            return $this->genErrorRespose("No movie name supplied", $format, $pretty);
        }

        var_dump($data);
        die();
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

    function genErrorRespose($text, $type = 'json', $beautify = 0, $code = 400){
        $data = [
            'error' => $text
        ];
        return $this->respGenerator($type, $data, $beautify, $code);
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
