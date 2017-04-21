<?php

namespace AiClasses;

use Stringy\Stringy as S;

class ValidatorClass{

    function is_mainMessage($data){
        return ValidatorClass::what_mainMessage($data);
    }

    function is_barcode($data){
        return true;
    }

    function what_mainMessage($data){
        $keywords = [];
        $keywords['help'] = ['help-*', 'could-*-help*', 'can-*-help*', '*-need-*help'];
        $keywords['whatsup'] = ['*whats-up*', '*how-*-you', '*how-*-you*'];
        $keywords['whoareyou'] = ['who-*-you'];
        $keywords['hi'] = ['hi-*', 'hello-*', 'hey-*'];

        $message_simplified = S::create($data)->slugify()->__toString();
        foreach($keywords as $type => $val){
            foreach($val as $pattern){
                $pattern = str_replace( '*' , '.*', $pattern);
                $found = preg_match('/^' . $pattern . '$/i', $message_simplified);
                if($found) return $type;
            }
        }
        return $message_simplified;
    }
}
