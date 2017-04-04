<?php
namespace Misc;

use Silex\Application;

class MiscClass{
    function __construct(){
        //nothing here :D
    }
    public function filter($data){
        if(isset($data['email'])){
            $data['email'] = substr($data['email'], 0, 100);
        }
        if(isset($data['password'])){
            $data['password'] = substr($data['password'], 0, 100);
        }
        if(isset($data['title'])){
            $data['title'] = substr($data['title'], 0, 100);
        }
        if(isset($data['color'])){
            $colors = explode(',', 'blue,red,purple,yellow,green,indigo,orange,pink,teal,amber');
            if(!in_array($data['color'], $colors))$data['color'] = 'blue';
        }
        return $data;
    }
    public function validate($data, $type){
        if(!isset($data[$type]))return 0;
        if($type=='email'){
            if(strlen($data[$type])<6 || !filter_var($data[$type], FILTER_VALIDATE_EMAIL)) return 0;
        }elseif($type=='password'){
            if(strlen($data['password'])<6) return 0;
        }
        return 1;
    }
    public function encode($data){
        return md5($data);
    }
    public function shorten($text){
        $text = strtolower($text);
        if($text=='profesor') return 'Prof.';
        if($text=='director') return 'Dir.';
        if($text=='director adjunct') return 'Adj.';
    }
}
