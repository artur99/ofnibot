<?php

namespace AiClasses;

use Stringy\Stringy as S;

class Api1MainClass{
    private $request_data;
    private $allw_formats = ['json', 'yaml'];
    public $error = false;
    public $error_code = 400;

    private $is_pretty = false;
    private $format = 'json';
    private $limit = 40;
    private $sortByScore = false;
    private $sortByPopularity = false;

    function __construct($app, $request_data){
        $this->request_data = $request_data;
        $this->fetcher = new FetcherClass($app);
        $this->mainCheck();
    }

    function mainCheck(){
        $qp = $this->request_data;
        $pretty = (isset($qp['pretty']) && filter_var($qp['pretty'], FILTER_VALIDATE_BOOLEAN)) ? true : false;
        $this->is_pretty = $pretty;

        $format = (isset($qp['format'])) ? $qp['format'] : 'json';
        if(!in_array($format, $this->allw_formats)){
            $this->format  = false;
            $this->error = "Invalid format for output";
            return;
        }else{
            $this->format = $format;
        }

        if(isset($qp['limit'])){
            $limit = intval($qp['limit']);
            if($limit <= 0){
                $this->limit = false;
                $this->error = "Invalid limit for similar entities";
                return;
            }else{
                $this->limit = $limit;
            }
        }

        $sbs =  (isset($qp['sort_by_score']) && filter_var($qp['sort_by_score'], FILTER_VALIDATE_BOOLEAN)) ? true : false;
        $sbp =  (isset($qp['sort_by_popularity']) && filter_var($qp['sort_by_popularity'], FILTER_VALIDATE_BOOLEAN)) ? true : false;
        $this->sortByScore = $sbs;
        $this->sortByPopularity = $sbp;

    }

    function isPretty(){
        return $this->is_pretty;
    }
    function getFormat(){
        return $this->format;
    }

    function checkForMovie(){
        $rd = $this->request_data;
        if(!isset($rd['name']) || !is_string($rd['name'])){
            $this->error = "No movie name supplied";
            $this->error_code = 401;
            return false;
        }

        $data = $this->fetcher->fetchMovie($rd['name'], $this->limit, $this->sortByScore, 0);
        if(!$data){
            $this->error = "Movie not found";
            $this->error_code = 404;
            return false;
        }
        if($this->sortByScore){
            uasort($data['simi'], function($a, $b) {
                return($a['score'] < $b['score']);
            });
        }

        $rp_array = [
            'movie' => [
                'title' => $data['title'],
                'overview' => $data['overview'],
                'link' =>  $data['link'],
                'release_year' =>  $data['release_year'],
                'image' =>  $data['image'],
                'score' =>  $data['score']
            ],
            'similar_movies_count' => count($data['simi']),
            'similar_movies' => []
        ];
        foreach($data['simi'] as $s_movie){
            $rp_array['similar_movies'][] = [
                'title' => $s_movie['title'],
                'overview' => $s_movie['overview'],
                'link' =>  $s_movie['link'],
                'release_year' =>  $s_movie['release_year'],
                'image' =>  $s_movie['image'],
                'score' =>  $s_movie['score']
            ];
        }

        return $rp_array;
    }

    function checkForSong(){
        $rd = $this->request_data;
        if(!isset($rd['name']) || !is_string($rd['name'])){
            $this->error = "No song name supplied";
            $this->error_code = 401;
            return false;
        }

        $data = $this->fetcher->fetchSong($rd['name'], $this->limit, $this->sortByPopularity, 1);

        if(!$data){
            $this->error = "Song not found";
            $this->error_code = 404;
            return false;
        }

        $rp_array = [
            'movie' => [
                'name' => $data['name'],
                'link' =>  $data['link'],
                'artist' => $data['artist'],
                'artist_link' =>  $data['artist_link']
            ],
            'similar_songs_count' => count($data['simi']),
            'similar_songs' => []
        ];
        foreach($data['simi'] as $s_song){
            $rp_array['similar_songs'][] = [
                'name' => $s_song['name'],
                'link' =>  $s_song['link'],
                'artist' => $s_song['artist'],
                'artist_link' =>  $s_song['artist_link']
            ];
        }

        return $rp_array;
    }

}
