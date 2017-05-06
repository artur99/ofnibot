<?php

namespace AiClasses;

use Stringy\Stringy as S;
use PHPHtmlParser\Dom;

class FetcherClass{
    // private $what_to_fetch, $data_to_fetch;
    private $keys = [];

    function __construct($app){
        $this->keys['tmdb'] = $app['conf.api_keys.tmdb'];
        $this->keys['lastfm'] = $app['conf.api_keys.lastfm'];
        $this->keys['eandata'] = $app['conf.api_keys.eandata'];
        $this->keys['digiteyes_app'] = $app['conf.api_keys.digiteyes_app'];
        $this->keys['digiteyes_auth'] = $app['conf.api_keys.digiteyes_auth'];
    }

    function fetchMovie($movie_name, $limit = 5, $orderByScoreDesc = false, $shuffle = 0){
        $url1 = $this->apiHelper('tmdb1', $movie_name);
        $data = $this->wget($url1);
        $data2 = @json_decode($data);
        if(!isset($data2->results) || !isset($data2->results[0])){
            return false;
        }
        $movie_data = $data2->results[0];
        $main_movie_id = $movie_data->id;
        $url2 = $this->apiHelper('tmdb3', $movie_data->id);
        $simi = $this->wget($url2);
        $simi2 = @json_decode($data);
        if(!isset($simi2->results)){
            return false;
        }
        $simi_data = $simi2->results;
        if($shuffle){
            shuffle($simi_data);
        }else{
            usort($simi_data, function($a, $b){
                if($a->popularity == $b->popularity)
                    return 0;
                return $a->popularity > $b->popularity ? -1 : 1;
            });
        }
        if($orderByScoreDesc){
            usort($simi_data, function($a, $b){
                if($a->user_score == $b->user_score)
                    return 0;
                return $a->user_score > $b->user_score ? -1 : 1;
            });
        }

        if(!strlen($movie_data->overview))
            $movie_data->overview = 'There\'s no overview of the movie in the database yet.';
        $res['title'] = $movie_data->original_title;
        $res['overview'] = $movie_data->overview;
        $res['release_year'] = date("Y", strtotime($movie_data->release_date));
        $res['link'] = $this->apiHelper('tmdb2', $movie_data->id);
        $res['image'] = $this->apiHelper('tmdb4', $movie_data->poster_path);
        $res['score'] = $movie_data->vote_average;

        $res['simi'] = [];

        for($i=1,$k=1;$i<sizeof($simi_data) && $k<=$limit;$i++){
            // [0] is the same
            if($main_movie_id == $simi_data[$i]->id){
                continue;
            }
            if(!strlen($simi_data[$i]->overview))
                $simi_data[$i]->overview = 'There\'s no overview of the movie in the database yet.';
            $res['simi'] []= [
                'title' => $simi_data[$i]->original_title,
                'overview' => $simi_data[$i]->overview,
                'release_year' => date("Y", strtotime( $simi_data[$i]->release_date)),
                'link' => $this->apiHelper('tmdb2', $simi_data[$i]->id),
                'image' => $this->apiHelper('tmdb4', $simi_data[$i]->poster_path),
                'score' => $simi_data[$i]->vote_average
            ];
            $k++;
        }

        return $res;
    }

    function fetchSong($song_name, $limit = 8, $orderByPopularityDesc = true, $shuffle = 1){
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
        $url2 = $this->apiHelper('lastfm2', $song_data->artist);
        $simi = $this->wget($url2);
        $simi2 = @json_decode($simi);
        if(!isset($simi2->toptracks->track)){
            return false;
        }
        $simi_data = array_slice($simi2->toptracks->track, 1, 100);
        if($orderByPopularityDesc){
            usort($simi_data, function($a, $b){
                if($a->listeners == $b->listeners)
                    return 0;
                return $a->listeners > $b->listeners ? -1 : 1;
            });
        }elseif($shuffle){
            shuffle($simi_data);
        }

        $res['name'] = $song_data->name;
        $res['link'] = $song_data->url;
        $res['artist'] = $song_data->artist;
        $res['artist_link'] = $simi2->toptracks->track[0]->artist->url;

        $res['simi'] = [];
        for($i=0;$i<sizeof($simi_data) && $i<=$limit;$i++){
            // [0] is the same
            $res['simi'] []= [
                'name' => $simi_data[$i]->name,
                'artist' => $simi_data[$i]->artist->name,
                'artist_link' => $simi_data[$i]->artist->url,
                'link' => $simi_data[$i]->url,
            ];
        }

        return $res;

    }

    function fetchBarcode($barcode){
        $ip = ($_SERVER["REMOTE_ADDR"] == '127.0.0.1' ? '31.5.80.114' : $_SERVER["REMOTE_ADDR"]);
        $urlc = $this->apiHelper('country', $ip);
        $country0 = @json_decode(@file_get_contents($urlc));
        $country = isset($country0->country_code) ? $country0->country_code : false;

        $ingredients = $google_link = false;
        $sitelinks = [];

        $found = false;
        if($country == 'RO'){
            $found_ro = false;
            $url1 = $this->apiHelper('shop_ro1', $barcode); //enevila
            $url2 = $this->apiHelper('shop_ro2', $barcode); //nasticom

            $html2 = $this->wgetPost($url2, ['src'=>$barcode,'src_action'=>'go']);
            if(strpos($html2, 'nu au fost gasite') === false){
                // Found results
                if($name = $this->domFind($html2, '.product-name')){
                    $found_ro = $name;
                }
            }

            if($found_ro === false){
                $html1 = $this->wget($url1);
                if(strpos($html1, 'Nu au fost găsite') === false){
                    if($name = $this->domFind($html1, '.entry-title a')){
                        $found_ro = $name;
                    }
                }
            }

            if($found_ro){
                $f_sim = $this->productNameFix($found_ro);
                $found = $found_ro;

                $sitelinks[]= ['name' => 'Carrefour Online', 'link' => $this->apiHelper('shop_ro3', $f_sim)];
                $sitelinks[]= ['name' => 'Enevila', 'link' => $this->apiHelper('shop_ro1', $f_sim)];
                // $sitelinks[]= ['name' => 'Nasticom', 'link' => $this->apiHelper('shop_ro2', $f_sim)];
                $sitelinks[]= ['name' => 'Cora.ro', 'link' => $this->apiHelper('shop_ro4', $f_sim)];
                $sitelinks[]= ['name' => 'eMag Supermarket', 'link' => $this->apiHelper('shop_ro5', $f_sim)];
                $sitelinks[]= ['name' => 'Supermarket Claudia', 'link' => $this->apiHelper('shop_ro6', $f_sim)];
            }
        }
        if($found === false){
            $found_en = false;
            $url1 = $this->apiHelper('barcode1', $barcode); //eandata
            // $url2 = $this->apiHelper('barcode2', $barcode); //digit eyes
            $url2 = "http://31.5.80.114/mirror.php?url=".urlencode($this->apiHelper('barcode2', $barcode)); //digit eyes
            $api1 = @json_decode($this->wget($url1));
            if($api1 && isset($api1->product, $api1->product->attributes, $api1->product->attributes->product)){
                $found_en = $api1->product->attributes->product;
                if(isset($api1->product->attributes->ingredients) && strlen($api1->product->attributes->ingredients) > 0)
                    $ingredients = $api1->product->attributes->ingredients;
            }
            if($found_en === false){
                $api2 = @json_decode($this->wget($url2));
                if(isset($api2->description)){
                    $found_en = $api2->description;
                    if(isset($api2->ingredients) && strlen($api2->ingredients) > 0)
                        $ingredients = $api2->ingredients;
                }
            }

            if($found_en){
                $f_sim = $this->productNameFix($found_en);
                $found = $found_en;

                $sitelinks[]= ['name' => 'Amazon', 'link' => $this->apiHelper('shop_1', $f_sim)];
                $sitelinks[]= ['name' => 'eBay', 'link' => $this->apiHelper('shop_2', $f_sim)];
                $sitelinks[]= ['name' => 'Walmart', 'link' => $this->apiHelper('shop_3', $f_sim)];
            }
        }
        if(!$found)
            return false;

        $ingredients = $this->ingredientsFix($ingredients);

        $return = [
            'name' => $found,
            'ingredients' => $ingredients,
            'links' => $sitelinks,
            'google_link' => $this->apiHelper('google', $found)
        ];
        return $return;
    }

    private function apiHelper($type, $query, $query2 = null){
        if($type == 'tmdb1'){
            // Search API (by movie name)
            $key = $this->keys['tmdb'];
            return 'http://api.themoviedb.org/3/search/movie?api_key='.$key.'&language=en-US&query='.urlencode($query).'&page=1&include_adult=false';
        }elseif($type == 'tmdb2'){
            // Full page link generator (by movie id)
            return 'http://www.themoviedb.org/movie/'.$query;
        }elseif($type == 'tmdb3'){
            // Similar movie API (by movie id)
            $key = $this->keys['tmdb'];
            return 'http://api.themoviedb.org/3/movie/'.urlencode($query).'/similar?api_key='.$key.'&language=en-US&page=1';
        }elseif($type == 'tmdb4'){
            // Full image link generator (by poster path)
            if(strlen($query) == 0)
                return 'http://ipsumimage.appspot.com/154x231?l=No|image&f=fff&b=333';
            return 'http://image.tmdb.org/t/p/w154/'.$query;
        }elseif($type == 'lastfm1'){
            // Search API (by song name)
            $key = $this->keys['lastfm'];
            return 'http://ws.audioscrobbler.com/2.0/?method=track.search&track='.urlencode($query).'&api_key='.$key.'&format=json';
        }elseif($type == 'lastfm2'){
            // Similar API (by artist name)
            $key = $this->keys['lastfm'];
            return 'http://ws.audioscrobbler.com/2.0/?method=artist.gettoptracks&artist='.urlencode($query).'&api_key='.$key.'&format=json';
        }elseif($type == 'barcode1'){
            // Search DB1
            $key = $this->keys['eandata'];
            return 'http://eandata.com/feed/?v=3&keycode='.$key.'&mode=json&find='.urlencode($query);
        }elseif($type == 'barcode2'){
            // Search DB2
            $k_app = $this->keys['digiteyes_app'];
            $k_auth = $this->keys['digiteyes_auth'];
            $signature = base64_encode(hash_hmac('sha1', $query, $k_auth, $raw_output = true));
            return 'https://www.digit-eyes.com/gtin/v2_0/?upcCode='.urlencode($query).'&field_names=description,ingredients,image&language=en&app_key='.$k_app.'&signature='.$signature;
        }elseif($type == 'shop_ro1'){
            // Search Shop1
            return 'http://enevila.ro/?search_category=&s='.urlencode($query).'&search_posttype=product';
        }elseif($type == 'shop_ro2'){
            // Search Shop2
            return 'http://www.nasticom.ro/eshop/';
        }elseif($type == 'shop_ro3'){
            // Search Shop3
            return 'https://www.carrefour-online.ro/toate_produsele/?searchQuery='.urlencode($query).'&sort=score&direction=DESC';
        }elseif($type == 'shop_ro4'){
            // Search Shop4
            return 'https://www.cora.ro/search?categoryId=&queryStr='.urlencode($query).'';
        }elseif($type == 'shop_ro5'){
            // Search Shop5
            return 'http://www.emag.ro/supermarket/search/'.urlencode($query).'';
        }elseif($type == 'shop_ro6'){
            // Search Shop6
            return 'http://supermarketclaudia.ro/index.php?route=product/search&keyword='.urlencode($query).'&category_id=0';
        }elseif($type == 'shop_1'){
            // Search Shop1
            return 'https://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords='.urlencode($query).'';
        }elseif($type == 'shop_2'){
            // Search Shop2
            return 'http://www.ebay.com/sch/i.html?_from=R40&_trksid=p2050601.m570.l1313.TR0.TRC0.H0.Xaa+b.TRS0&_nkw='.urlencode($query).'&_sacat=0';
        }elseif($type == 'shop_3'){
            // Search Shop3
            return 'https://www.walmart.com/search/?query='.urlencode($query).'';
        }elseif($type == 'google'){
            // Search Google
            return 'http://google.com/search?q='.urlencode($query).'';
        }elseif($type == 'country'){
            return 'http://freegeoip.net/json/'.urlencode($query);
        }
    }

    function wget($url){
        return file_get_contents($url);
    }

    function wgetPost($url, $data){
        $result = file_get_contents($url, false, stream_context_create(['http' =>[
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded'."\r\n",
                        // .'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36'."\r\n",
            'content' => http_build_query($data)
        ]]));
        return $result;
    }

    function domFind($html, $selector){
        $dom = new Dom;
        $dom->load($html);
        $elem = @$dom->find($selector, 0);
        return is_object($elem) ? $elem->text : false;
    }

    function productNameFix($str){
        $str = preg_replace('/(\d+)( ?(G|L) ?)(\s|$)/i', '$1 ', $str);
        $str = str_replace(['(', ')'], '', $str);
        return $str;
    }

    function ingredientsFix($str){
        $str = str_replace('(', '( ', $str);
        $str = ucwords(strtolower($str));
        $str = str_replace('( ', '(', $str);
        return $str;
    }

}
