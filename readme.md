# OfniBot (v1.1.2)
The prototype of a new index. An intelligent bot made in PHP by [artur99] and [iulyus01].
Then updated by developing a public REST API (using OpenApi standards) for the bot's services, as requested in the final phase of the [FiiCode] 2017 contest.

## Demo
[ofnibot.artur99.net]

## About
Ofnibot offers you an interesting, funny and easy way of finding and comparing different entities on the internet, using some of the biggest databases of movies, songs, product barcodes and a lot more.

### Some GIFs:
<a href="http://i.imgur.com/6p3lZUW.gif" target="_blank"><img src="http://i.imgur.com/6p3lZUW.gif" height="320"></a>

## Installation
Requirements: `git`, `composer`, `node.js`.

Config file: `app/conf.yaml`.
```
$ npm install -g bower grunt-cli spectacle-docs
$ npm update
$ composer install
$ bower install
$ grunt
$ spectacle -t public_html/documentation/ -d OpenApi.yaml
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
   [FiiCode]: <http://fiicode.com>
   [artur99]: <http://github.com/artur99>
   [iulyus01]: <http://github.com/iulyus01>
