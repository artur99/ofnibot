<?php

namespace AiClasses;

use Stringy\Stringy as S;

class FetcherClass{
    // private $what_to_fetch, $data_to_fetch;
    private $keys = [];

    function __construct($app){
        $this->keys['tmdb'] = $app['conf.api_keys.tmdb'];
        $this->keys['lastfm'] = $app['conf.api_keys.lastfm'];
    }

    function fetchMovie($movie_name){
        $url1 = $this->apiHelper('tmdb1', $movie_name);
        $data = $this->wget($url1);
        $data2 = @json_decode($data);
        if(!isset($data2->results) || !isset($data2->results[0])){
            return false;
        }
        $movie_data = $data2->results[0];

        $url2 = $this->apiHelper('tmdb3', $movie_data->id);
        $simi = $this->wget($url2);
        $simi2 = @json_decode($data);
        if(!isset($simi2->results)){
            return false;
        }
        $simi_data = $simi2->results;
        $imx = 0;
        $res['simi'] = [];
        for($i=1;$i<sizeof($simi_data) && $i<=5;$i++){
            // [0] is the same
            $res['simi'] []= [
                'title' => $simi_data[$i]->original_title,
                'release_year' => date("Y", strtotime( $simi_data[$i]->release_date)),
                'link' => $this->apiHelper('tmdb2', $simi_data[$i]->id)
            ];
        }

        $res['title'] = $movie_data->original_title;
        $res['release_year'] = date("Y", strtotime($movie_data->release_date));
        $res['link'] = $this->apiHelper('tmdb2', $movie_data->id);

        return $res;
    }

    function fetchSong($song_name){
        $url1 = $this->apiHelper('lastfm1', $song_name);
        $data = $this->wget($url1);
        $data2 = @json_decode($data);
        if(
            !isset($data2->results) ||
            !isset($data2->results->trackmatches) ||
            !isset($data2->results->trackmatches->track) ||
            !isset($data2->results->trackmatches->track[0])
        ){
            return false;
        }
        $song_data = $data2->results->trackmatches->track[0];
        // $url2 = $this->apiHelper('tmdb3', $movie_data->id);
        // $simi = $this->wget($url2);
        // $simi2 = @json_decode($data);
        // if(!isset($simi2->results)){
        //     return false;
        // }
        // $simi_data = $simi2->results;

        $res['name'] = $song_data->name;
        $res['artist'] = $song_data->artist;
        $res['link'] = $song_data->url;

        return $res;

    }

    private function apiHelper($type, $query){
        if($type == 'tmdb1'){
            // Search API (by movie name)
            $key = $this->keys['tmdb'];
            return 'http://api.themoviedb.org/3/search/movie?api_key='.$key.'&language=en-US&query='.urlencode($query).'&page=1&include_adult=false';
        }else if($type == 'tmdb2'){
            // Full page link generator (by movie id)
            return 'https://www.themoviedb.org/movie/'.$query;
        }else if($type == 'tmdb3'){
            // Similar movie API (by movie id)
            $key = $this->keys['tmdb'];
            return 'https://api.themoviedb.org/3/movie/'.urlencode($query).'/similar?api_key='.$key.'&language=en-US&page=1';
        }else if($type == 'lastfm1'){
            // Search API (by song name)
            $key = $this->keys['lastfm'];
            return 'http://ws.audioscrobbler.com/2.0/?method=track.search&track='.urlencode($query).'&api_key='.$key.'&format=json';
        }

    }

    function wget($url){
        return file_get_contents($url);
    }

}
