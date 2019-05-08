<?php

include("classes/DomDocumentParser.php");

function createLink($src,$url){

}

function followLinks($url){
  $parser = new DomDocumentParser($url);

  $linksList = $parser->getLinks();
  foreach ($linksList as $link) {
    $href = $link->getAttribute("href");


    if (strpos($href, "#") !== false)
      continue;
    elseif (substr($href, 0,11)=="javascript:"){
      continue;
    }

    createLink($href,$url);
//    echo $href . "<br>";
  }
}

$startUrl = "http://www.reecekenney.com";
followLinks($startUrl);

 ?>
