# OfniBot
The prototype of a new index

## About
Ofnibot offers you an interesting, funny and easy way of finding and comparing different entities on the internet, using some of the biggest databases of movies, songs, product barcodes and a lot more.

## Fist configuration
For this app to run, you will need to have PHP composer installed. It is a tool just like npm, but for PHP libs. (Download: getcomposer.org)
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
We've been using for this app these websites for searching things:
* Nasticom.ro
* Enevila.ro
* EanData.com
* Digit-Eyes.com
* TMDb
* Live.fm
