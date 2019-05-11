<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url)
{
    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0;
}

function insertLink($url, $title, $description, $keywords)
{
    global $con;

    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
}

function insertImage($url, $src, $alt, $title)
{
    global $con;

    $query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");

    $query->bindParam(":siteUrl", $url);
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    $query->execute();
}

function createLink($src, $url)
{

    $scheme = parse_url($url)["scheme"];
    $host = parse_url($url)["host"];

    if (substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    } elseif (substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    } elseif (substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    } elseif (substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    } elseif (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }


    return $src;
}

function getDetails($url)
{
    global $alreadyFoundImages;

    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitle();

    if (sizeof($titleArray) == 0 || $titleArray->item(0) == null) {
        return;
    }
    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);

    if ($title == "")
        return;

    $description = "";
    $keyWords = "";

    $metaArray = $parser->getMetaTags();

    foreach ($metaArray as $meta) {
        if ($meta->getAttribute('name') == "description") {
            $description = $meta->getAttribute('content');
        }
        if ($meta->getAttribute('name') == "content") {
            $keyWords = $meta->getAttribute('content');
        }
    }

    $description = str_replace("\n", "", $description);
    $keyWords = str_replace("\n", "", $keyWords);


    if (linkExists($url)) {
        echo "$url already in database<br>";
    } elseif (insertLink($url, $title, $description, $keyWords)) {
        echo "Success";
    }

    $imageArray = $parser->getImages();
    foreach ($imageArray as $image) {
        $src = $image->getAttribute('src');
        $alt = $image->getAttribute('alt');
        $title = $image->getAttribute('title');

        if (!$title && !$alt) {
            continue;
        }

        $src = createLink($src,$url);
        if (!in_array($src, $alreadyFoundImages)){
            $alreadyFoundImages[] = $src;
            insertImage($url,$src,$alt,$title);
        }
    }
}

function followLinks($url)
{
    global $alreadyCrawled;
    global $crawling;
    $parser = new DomDocumentParser($url);

    $linksList = $parser->getLinks();
    foreach ($linksList as $link) {
        $href = $link->getAttribute("href");


        if (strpos($href, "#") !== false)
            continue;
        elseif (substr($href, 0, 11) == "javascript:") {
            continue;
        }

        $href = createLink($href, $url);

        if (!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;

            getDetails($href);
        }



    }

    array_shift($crawling);

    foreach ($crawling as $site) {
        followLinks($site);
    }
}

$startUrl = "https://en.wikipedia.org/wiki/Main_Page";
followLinks($startUrl);

?>
