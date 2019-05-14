<?php
include("config.php");
include ("classes/SitesResultsProvider.php");
include ("classes/imagesResultsProvider.php");

if (isset($_GET["term"])) {
    $term = $_GET["term"];
} else {
    exit("Please enter a search term");
}

//if (isset($_GET["type"])) {
//    $type = $_GET["type"];
//} else {
//    $type = "sites";
//}
$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;


?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>

    <meta charset="utf-8">
    <title>Giggle Search</title>


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous">

    </script>

</head>
<body>

    <article class="header">
        <section class="headerContent">
            <section class="logoContainer">
                <a href="index.php">
                    <img src="assets/img/giggleLogo.png">
                </a>
            </section>

            <section class="searchContainer">
                <form class="" action="search.php" method="get">
                    <section class="searchBarContainer">
                        <input type="hidden" name="type" value="<?php echo $type ?>">
                        <input type="text" name="term" class="searchBox" value="<?php echo $term ?>">
                        <button type="button" class="searchButton" name="button">
                            <img src="assets/img/searchIcon.png" alt="">
                        </button>
                    </section>
                </form>
            </section>

        </section>

        <section class="tabsContainer">
            <ul class="tabList">
                <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                    <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>Sites</a>
                </li>
                <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                    <a href='<?php echo "search.php?term=$term&type=images"; ?>'>Images</a>
                </li>
            </ul>
        </section>

    </article>

    <div class="mainResultsSection">

        <?php
        if ($type == "sites"){
            $resultsProvider = new SitesResultsProvider($con);
            $pageSize = 20;
        }
        else{
            $resultsProvider = new imagesResultsProvider($con);
            $pageSize = 30;
        }

        $numResults = $resultsProvider->GetNumResults($term);
        echo "<p class='resultsCount'>$numResults results found</p>";
        echo $resultsProvider->GetResultsHTML($page,$pageSize,$term);
        ?>
    </div>

    <div class="paginationContainer">



        <div class="pageButtons">

            <div class="pageNumberContainer">
                <img src="assets/img/pageStart.png" alt="">
            </div>

            <?php
            $pagesToShow = 10;
            $numPages = ceil($numResults/$pageSize);
            $pagesLeft = min($pagesToShow,$numPages);
            $currentPage = $page - floor($pagesToShow / 2);
            if ($currentPage < 1)
            {
                $currentPage = 1;
            }
            if ($currentPage + $pagesLeft > $numPages + 1){
                $currentPage = $numPages - $pagesLeft + 1;
            }

            while ($pagesLeft != 0 && $currentPage <=$numPages)
            {
                if ($currentPage == $page)
                {
                    echo "<div class='pageNumberContainer'>  
                        <img src='assets/img/pageSelected.png' alt = ''>
                        <span class='pageNumber'>$currentPage</span>
                       </div>";
                }
                else
                {
                    echo "<div class='pageNumberContainer'> 
                            <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                <img src='assets/img/page.png' alt = ''>
                                <span class='pageNumber'>$currentPage</span>
                            </a> 
                       </div>";
                }


                $currentPage++;
                $pagesLeft--;
            }
            ?>

            <div class="pageNumberContainer">
                <img src="assets/img/pageEnd.png" alt="">
            </div>

        </div>



    </div>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
    <script type="text/javascript" src="assets/javascript/script.js"></script>
</body>
</html>
