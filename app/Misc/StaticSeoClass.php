<?php
namespace Misc;

class StaticSeoClass{
    function get($what){
        global $app;
        if($what == 'home'){
            $res['title'] = $app['conf.title'];
        }
    }
}
