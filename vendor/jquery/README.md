# jQuery

##Intro##

This asset allows you to load actual version of the jQuery library.

##Versions##

Using version of the jQuery library is 2.2.4

##Modes##

You can choose minified or uncompressed js file include. It can be done using specific parameter in the asset's constructor:

```PHP
$Asset = new jQueryAsset( 'uncompressed' ); // loading uncompressed files
$Asset = new jQueryAsset( 'min' ); // loading minified files
```

Note that all files are included from jQuery's CDN.

See more for assets [here](https://github.com/alexdodonov/mezon/tree/master/vendor/asset#assets-with-css-and-js-files)