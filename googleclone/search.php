 <?php
    if(isset($_GET["term"])) {
      $term = $_GET["term"];
    }
    else {
        exit("Please enter a search term");
    }

    if(isset($_GET["type"])) {
      $type = $_GET["type"];
    }
    else {
        $type = "sites";
    }


  ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>

    <meta charset="utf-8">
    <title>Giggle Search</title>


    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>
  <body>

      <article class="header">
          <section class="headerContent">
            <section class="logoContainer">
              <a href="index.php">
              <img src="assets/img/giggleLogo.png" >
              </a>
            </section>

            <section class="searchContainer">
              <form class="" action="search.php" method="get">
                <section class="searchBarContainer">
                  <input type="text" name="term" class="searchBox"value="">
                  <button type="button" class="searchButton"name="button">
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

  </body>
</html>
