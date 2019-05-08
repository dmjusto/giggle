<?php

include("classes/DomDocumentParser.php");

function followLinks($url){
  $parser = new DomDocumentParser($url);

  $linksList = $parser->getLinks();
  foreach ($linksList as $link) {
    $href = $link->getAttribute("href");
    echo $href . "<br>";
  }
}

$startUrl = "http://www.reecekenney.com";
followLinks($startUrl);

 ?>
