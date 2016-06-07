# Assets with CSS and JS files
##Intro##
All html page resources can be composed in a single bundle wich is called 'asset'.

##Creating your own asset##

First create a class wich extends base Asset class like this:

```PHP
global          $MEZON_PATH;
require_once( $MEZON_PATH.'/vendor/asset/asset.php' );

class   MyAsset extends Asset
{
}
```

Then you need to include resource files:

```PHP
class   MyAsset extends Asset
{
    /**
    *   Constructor.
    */
    function __construct()
    {
        $this->CSSFiles = array(
            './res/css/file1.css' , './res/css/file2.css'
        );

        $this->JSFiles = array(
            './include/js/test.js' , './include/js/test2.js'
        );
    }
}
```

At last create object of this class and call it's include_files method, wich adds all it's resources to the TemplateResources storage:

```PHP
$Asset = new MyAsset();

$Asset->include_files();
```

Pretty simple eah? )