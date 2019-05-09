<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();

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

    insertLink($url,$title,$description,$keyWords);

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
        } else {
            return;
        }


    }

    array_shift($crawling);

    foreach ($crawling as $site) {
        followLinks($site);
    }
}

$startUrl = "http://www.bbc.com";
followLinks($startUrl);

?>
