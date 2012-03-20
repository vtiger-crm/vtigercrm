<?php
/*
* Filename.......: image_cache.php
* Author.........: Troy Wolf [troy@troywolf.com]
* Last Modified..: Date: 2005/06/21 10:30:00
* Description....: Companion script to clas_http.php. When used in conjunction
                   with class_http.php, can be used to "screen-scrape" images
                   and cache them locally for any number of seconds. You use
                   this script in-line within img tags like so:
<img src="image_cache.php?ttl=300&url=http%3A%2F%2Fwww.somedomain.com%2Fsomeimage.gif" />
                  (You must url encode the url within the src attribute.)
*/

/*
Include the http class. Modify path according to where you put the class
file.
*/
require_once('class_http.php');

$h = new http();
$h->fetch($_GET['url'], $_GET['ttl']);
header("Content-Type: image/jpeg");
echo $h->body;
?>
