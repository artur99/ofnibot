<?php

namespace AiClasses;

use Stringy\Stringy as S;

class IdentifierClass{
    private $types = [
        'barcode',
        'link',
        'title', // Movie or song
        'mainMessage'
    ];
    private $message, $message_s;

    function __construct($app, $msg){
        $this->message = $msg;
        $this->message_s = $this->slug($msg);
        $this->session = $app['session'];
        $this->fetcher = new FetcherClass($app);
    }

    function firstCheck(){
        $msg = $this->message_s;
        $matches1 = ['help', 'who-are-you'];
        $matches2 = ['cancel', 'cancel-*', 'reset', 'stop', 'stop-*'];

        if($this->matcher($msg, $matches1)){
            $this->rememberToContinue('remove');
            return $this->messageSeries('help');
        }else if($this->matcher($msg, $matches2)){
            $this->rememberToContinue('remove');
            return $this->messageSeries('cancel');
        }
        return false;
    }

    function mainCheck(){
        $msg = $this->message_s;
        if(
            ($movie = $this->grammarMatch($msg, 'movie')) ||
            ($movie = $this->grammarMatch($msg, 'film'))
        ){
            if($movie === true){
                $this->rememberToContinue('set', 'movie');
                return "What's the title of the movie?";
            }
            return $this->extractAndGenerateMessage('movie', $this->simplify($movie));
        }else if(
            ($song = $this->grammarMatch($msg, 'song')) ||
            ($song = $this->grammarMatch($msg, 'melody'))
        ){
            if($song === true){
                $this->rememberToContinue('set', 'song');
                return "What's the title of the song?";
            }
            return $this->extractAndGenerateMessage('song', $this->simplify($song));
        }else if(
            ($barcode1 = $this->barcodeCheck($msg)) ||
            ($barcode2 = $this->grammarMatch($msg, 'barcode'))
        ){
            if($barcode1){
                //direct barcode
                return $this->extractAndGenerateMessage('barcode', $this->simplify($msg));
            }else if($barcode2 && $barcode2 !== true){
                if($this->barcodeCheck($barcode2)){
                    return $this->extractAndGenerateMessage('barcode', $this->simplify($barcode2));
                }else{
                    return "Sorry, that's been an invalid barcode. :(";
                }
            }else if($barcode2 === true){
                $this->rememberToContinue('set', 'barcode');
                return "Just type in here the barcode. :D";
            }else{
                return "Sorry, that's been an invalid barcode. :(";
            }
            // return false;
        }else if($simple = $this->simpleCheck($msg)){
            return $simple;
        }

        return false;
    }

    function expectedReply(){
        $msg = $this->message_s;
        $res = false;

        $reply = $this->rememberToContinue('get');

        if($reply == 'movie'){
            $res = $this->extractAndGenerateMessage('movie', $this->simplify($msg));
        }else if($reply == 'song'){
            $res = $this->extractAndGenerateMessage('song', $this->simplify($msg));
        }else if($reply == 'barcode'){
            $res = $this->extractAndGenerateMessage('barcode', $this->simplify($msg));
        }else if($msg = $this->messageSeries($reply)){
            $res = $msg;
        }
        $this->rememberToContinue('remove');
        return $res;
    }

    function slug($txt){
        return S::create($txt)->slugify()->__toString();
    }

    function unslug($txt){
        $txt = str_replace('-', ' ', $txt);
        return trim($txt);
    }

    function simplify($txt){
        $txt = $this->slug($txt);
        return $this->unslug($txt);
    }

    function unslug2($txt){
        $txt = str_replace('-', ' ', $txt);
        return ucwords(trim($txt));
    }

    function matcher($text, $array_of_matches){
        foreach($array_of_matches as $pattern){
            $pattern = str_replace( '*' , '.*', $pattern);
            $found = preg_match('/^' . $pattern . '$/i', $text);
            if($found) return true;
        }
        return false;
    }

    private function barcodeCheck($msg){
        $msg = trim($msg);
        if(ctype_digit($msg) && strlen($msg) < 20 && strlen($msg) > 6){
            return true;
        }

        return false;
    }

    function grammarMatch($msg, $match){
        $msg = $this->slug($msg);
        $fins = $fins2 = [];
        $fins[] = $match.'-';
        $fins[] = 'a-'.$match;
        $fins[] = 'an-'.$match;
        $fins[] = 'the-'.$match;
        $fins[] = 'this-'.$match;
        $fins[] = '-'.$match;
        foreach($fins as $lim){
            $dt = explode($lim, $msg, 2);
            if(sizeof($dt) > 1){
                if(end($dt) != ''){
                    // .... entity_type <entity_name>
                    $fin = end($dt);
                }else{
                    // .... <entity_name> entity_type
                    $kr = explode('identify', $dt[0], 2);
                    $fin = end($kr);
                }

                if(strlen(trim($this->unslug($fin))) == 0)
                    return true;
                return $fin;
            }
        }
        foreach($fins as $lim){
            $t1 = trim($lim, '-');
            $t2 = 'identify-'.$t1;
            if(
                $msg == $t1 ||
                strpos($msg, $t2) !== false
            ){
                return true;
            }
        }
        return false;
    }
    function fallbackMessage(){
        return $this->messageSeries('fallback');
    }

    private function simpleCheck($msg){
        $msg = $this->slug($msg);
        if($this->matcher($msg, ['hi', 'hello', 'hey', 'heyy', 'hi-*robot', 'hey-*robot', 'hello-*robot']))
            return $this->messageSeries('hello');
        if($this->matcher($msg, ['who-are-you', 'what-are-you', 'wh*your-name'])){
            $this->rememberToContinue('name2');
            return $this->messageSeries('name1');
        }
        if($this->matcher($msg, ['thank-you', 'thank-you-*', 'thanks', 'thanks-*'])){
            return $this->messageSeries('welcome');
        }
    }

    function messageSeries($which){
        $replies = [];
        if($which == 'help'){
            $replies = [
                'My name is <...>. :) I can help you identifying different things like producs by barcode, movies, songs, etc., and then giving you related information on them.'
                .' For more help, <a href="/help" target="_blank">click here</a>.'."\n"
                .' (For eg., you can simply tell me "This movie: `movie-name`", or "A barcode: `123456789`", or you can just drag a picture of a barcode here)'."\n"
                .' What would you like me to identify? :D '
                // 'I am a robot1. :D'
            ];
        }elseif($which == 'cancel'){
            $replies = [
                'Ok, I\'ve canceled any active task, so you can now make a new query on me. :D'
            ];
        }elseif($which == 'hello'){
            $replies = [
                'Hello! :D',
                'Hi! :)',
                'Hello, my dear visitor! :)',
                'Heyy! :D',
                'Howdy! How can I help you? :D'
            ];
        }elseif($which == 'name1'){
            $replies = [
                'My name is <...>. But what\'s your name? :D'
            ];
        }elseif($which == 'name2'){
            $replies = [
                'Nice to meet you! '.$this->unslug2($this->splitter2($this->message_s, 'is')).':)'
            ];
        }elseif($which == 'welcome'){
            $replies = [
                'You\'re welcome! It\'s been a pleasure for me to help you! :)'
            ];
        }elseif($which == 'fallback'){
            $replies = [
                'I\'m sorry, I can\'t understand what you mean. :( I still have to lern. Type `help` for more info.',
                'Sorry, but I am not yet developed enough to understand what you meant. :( Type `help` for more info.'
            ];
        }
        $loc = rand(0, sizeof($replies)-1);
        return isset($replies[$loc]) ? $replies[$loc] : false;
    }

    function extractAndGenerateMessage($type, $name){
        if($type == 'movie'){
            $md = $this->fetcher->fetchMovie($name);
            if(!$md){
                return "Sorry, I couldn't find the movie in any database. :(";
            }else{
                $txt = 'I found the movie: <a href="'.$md['link'].'" target="_blank">'.$md['title'].' ('.$md['release_year'].')</a>'."\n";
                $txt.= 'Here you have some related movies:'."\n";
                $k = 1;
                foreach($md['simi'] as $mov){
                    $txt.= ($k++).'. <a href="'.$mov['link'].'" target="_blank">'.$mov['title'].' ('.$mov['release_year'].')</a>'."\n";
                }
                return $txt;
            }
        }else if($type == 'song'){
            $md = $this->fetcher->fetchSong($name);
            if(!$md){
                return "Sorry, I couldn't find the song in any database. :(";
            }else{
                $txt = 'I found the song: <a href="'.$md['link'].'" target="_blank">'.$md['artist'].' - '.$md['name'].'</a>'."\n";
                $txt.= '';
                return $txt;
            }
        }else{
            return 'No data found. :(';
        }
    }

    function rememberToContinue($action, $value = false){
        if($action == 'set'){
            return $this->session->set('remember', $value);
        }else if($action == 'get'){
            return $this->session->has('remember') ? $this->session->get('remember') : false;
        }else if($action == 'remove'){
            return $this->session->clear();
        }
        return false;
    }


}
