# Code #

How to quickly setup your browser capabilities script without get\_browser():
  1. Download the package and unpack it
  1. Upload everything on your server (You haven't to modify the Browscap class!)
  1. Change permissions on Browscap/cache to 666 (you might need 777)
  1. Create a .php file with the following content:
```
<?php

// Loads the class
require 'path/to/Browscap.php';

// Creates a new Browscap object (loads or creates the cache)
$bc = new Browscap('path/to/the/cache/dir');

// Gets information about the current browser's user agent
$current_browser = $bc->getBrowser();

// Output the result
echo '<pre>'; // some formatting issues ;)
print_r($current_browser);
echo '</pre>';
```

# Result #

Now open your web browser and navigate to the file you just created, you should see a result similar to the following but with your browser's information:

```
stdClass Object
(
    [browser_name] => Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_2; en-us) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.1 Safari/525.18
    [browser_name_regex] => ^mozilla/5\.0 \(macintosh; .; .*mac os x.*\) applewebkit/.* \(.*\) version/3\.1.* safari/.*$
    [browser_name_pattern] => Mozilla/5.0 (Macintosh; ?; *Mac OS X*) AppleWebKit/* (*) Version/3.1* Safari/*
    [Parent] => Safari 3.1
    [Platform] => MacOSX
    [Browser] => Safari
    [Version] => 3.1
    [MajorVer] => 3
    [MinorVer] => 1
    [Frames] => 1
    [IFrames] => 1
    [Tables] => 1
    [Cookies] => 1
    [BackgroundSounds] => 1
    [JavaApplets] => 1
    [JavaScript] => 1
    [CSS] => 2
    [CssVersion] => 2
    [supportsCSS] => 1
    [Alpha] => 
    [Beta] => 
    [Win16] => 
    [Win32] => 
    [Win64] => 
    [AuthenticodeUpdate] => 
    [CDF] => 
    [VBScript] => 
    [ActiveXControls] => 
    [Stripper] => 
    [isBanned] => 
    [WAP] => 
    [isMobileDevice] => 
    [isSyndicationReader] => 
    [Crawler] => 
    [AOL] => 
    [aolVersion] => 0
    [netCLR] => 
    [ClrVersion] => 0
)
```