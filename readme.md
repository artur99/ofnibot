# OfniBot (v1.0.0)
The prototype of a new index

## Demo
[ofnibot.artur99.net]

## About
Ofnibot offers you an interesting, funny and easy way of finding and comparing different entities on the internet, using some of the biggest databases of movies, songs, product barcodes and a lot more.

## Installation
Requirements: `git`, `composer`, `node.js`.

Config file: `app/conf.yaml`.
```
$ npm install -g bower grunt-cli
$ npm update
$ composer install
$ bower install
$ grunt
```
Development watcher fro bower:
```
$ grunt prep && grunt watch
```
and a `grunt` at the end for them to be compressed and cleaned up.

## 3rd party services
We've been using for this app these websites as APIs:
* [Nasticom.ro]
* [Enevila.ro]
* [EanData.com]
* [Digit-Eyes.com]
* [TMDb]
* [Last.fm]
* [FreeGeoIP]

### Technologies
* [Composer]
* [Node.js]
* [Bower]
* [Grunt]
* [Silex Micro Framework]

## Security
The messages are filtered each time, so there couldn't be any XSS in the page, and also, there would be no way of running malicious code sent by the user. Each ajax request has a CSRF key, so it would be impossible to run any unwanted code.

### Languages
* HTML
* CSS
* JS (client + backend for development)
* PHP

   [Composer]: <https://getcomposer.org/>
   [node.js]: <http://nodejs.org>
   [bower]: <http://bower.io/>
   [materializecss]: <http://materializecss.com/>
   [Silex Micro Framework]: <http://silex.sensiolabs.org/>
   [grunt]: <http://gruntjs.com/>
   [ofnibot.artur99.net]: <http://ofnibot.artur99.net/>
   [Nasticom.ro]: <http://nasticom.ro>
   [Enevila.ro]: <http://enevila.ro>
   [EanData.com]: <http://eandata.com>
   [Digit-Eyes.com]: <http://digit-eyes.com>
   [TMDb]: <https://www.themoviedb.org>
   [Last.fm]: <https://www.last.fm>
   [FreeGeoIP]: <http://freegeoip.net>
