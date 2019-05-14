<?php


class imagesResultsProvider
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function GetNumResults($term)
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
                                                            FROM images 
                                                            WHERE (title LIKE :term
                                                            OR alt LIKE  :term)
                                                            AND broken = 0");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    public function GetResultsHTML($page, $pageSize, $term){

        $fromLimit = ($page -1) * $pageSize;

        $query = $this->con->prepare("SELECT * 
                                            FROM images 
                                            WHERE (title LIKE :term
                                            OR alt LIKE  :term)
                                            AND broken = 0
                                            ORDER BY clicks DESC
                                            LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class = 'imageResults'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];

            if($title){
                $displayText = $title;
            }
            elseif($alt){
                $displayText = $alt;
            }
            else{
                $displayText = $imageUrl;
            }

            $resultsHTML .= "<div class = 'gridItem'>
                                <a href='$imageUrl'>
                                
                                    <img src='$imageUrl' alt=''>
                                    
                                    <span class='details'>$displayText</span>
                                    
                                </a>
                              </div>";

        }

        $resultsHTML.= "</div>";

        return $resultsHTML;
    }


}