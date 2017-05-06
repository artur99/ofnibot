<?php

namespace AiClasses;

use Stringy\Stringy as S;

class ApiHelperClass{

    function __construct($app, $msg){
        $this->message = $msg;
        $this->message_s = $this->slug($msg);
        $this->session = $app['session'];
        $this->fetcher = new FetcherClass($app);
        $this->appname = $app['conf.appname'];
    }

    function extractAndGenerateMessage($type, $name){
        if($type == 'movie'){
            $md = $this->fetcher->fetchMovie($name);
            if(!$md){
                return "Sorry, I couldn't find the movie in any database. :(";
            }else{
                $txt = 'I found the movie:';
                $txt.= '<a class="dbox dbox_movie" href="'.$md['link'].'" target="_blank">';
                $txt.= '<img src="'.$md['image'].'">';
                $txt.= '<h3>'.$md['title'].' ('.$md['release_year'].')</h3>';
                $txt.= '<small>'.$this->truncate($md['overview'], 130).'</small>';
                $txt.= '</a>';
                if(sizeof($md['simi']) == 0){
                    $txt .= 'But I couldn\'t find any related movies. :(';
                }else{
                    $txt.= 'And I also found some related movies: :D'."\n";
                    $k = 1;
                    foreach($md['simi'] as $md){
                        $txt.= '<a class="dbox dbox_movie" href="'.$md['link'].'" target="_blank">';
                        $txt.= '<img src="'.$md['image'].'">';
                        $txt.= '<h3>'.$md['title'].' ('.$md['release_year'].')</h3>';
                        $txt.= '<small>'.$this->truncate($md['overview'], 130).'</small>';
                        $txt.= '</a>';
                    }
                }
                return $txt;
            }
        }else if($type == 'song'){
            $md = $this->fetcher->fetchSong($name);
            if(!$md){
                return "Sorry, I couldn't find the song in any database. :(";
            }else{
                $txt = 'I found the song:';
                $txt.= '<a class="dbox dbox_song" href="'.$md['link'].'" target="_blank">';
                $txt.= '<h3>'.$md['name'].'</h3>';
                $txt.= '<small>'.$md['artist'].'</small>';
                $txt.= '</a>';

                if(sizeof($md['simi']) == 0){
                    $txt .= 'But I couldn\'t find any related songs. :(';
                }else{
                    $txt.= 'And I also found some related songs: :D'."\n";
                    $k = 1;
                    foreach($md['simi'] as $md){
                        $txt.= '<a class="dbox dbox_song" href="'.$md['link'].'" target="_blank">';
                        $txt.= '<h3>'.$md['name'].'</h3>';
                        $txt.= '<small>'.$md['artist'].'</small>';
                        $txt.= '</a>';
                    }
                }
                return $txt;
            }
        }else if($type == 'barcode'){
            $md = $this->fetcher->fetchBarcode($name);
            if(!$md){
                return "Sorry, I couldn't find the barcode in any database. :(\n"
                    .'You could try a search on <a href="http://google.com/search?q='.$name.'" target="_blank">Google</a>.';
            }else{
                $txt = 'I found the barcode! :D ';
                $txt.= '<div class="dbox dbox_product">';
                $txt.= '<h3>'.$md['name'].'</h3>';
                $txt.= '<small>'.($md['ingredients']?'Ingredients: '.$md['ingredients']:'No info about ingredients...').'</small>';
                $txt.= '</div>';
                $txt.= 'You can compare the product here:'."\n";

                foreach($md['links'] as $md2){
                    $txt.= '<a class="dbox dbox_shop" href="'.$md2['link'].'" target="_blank">';
                    $txt.= '<h3>'.$md2['name'].'</h3>';
                    $txt.= '</a>';
                }
                $txt.= 'Or, would you like to take a look on Google?'."\n";
                $txt.= '<a class="dbox dbox_shop" href="'.$md['google_link'].'" target="_blank">';
                $txt.= '<h3>Google</h3>';
                $txt.= '</a>';
            }
            return $txt;
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
