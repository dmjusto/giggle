<?php


class SitesResultsProvider
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function GetNumResults($term)
    {
        $query = $this->con->prepare("SELECT COUNT(*) as total 
                                                            FROM sites WHERE title LIKE :term
                                                            OR url LIKE :term
                                                            OR keywords LIKE :term
                                                            OR description LIKE :term");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];
    }

    public function GetResultsHTML($page, $pageSize, $term){

        $query = $this->con->prepare("SELECT * 
                                            FROM sites WHERE title LIKE :term
                                             OR url LIKE :term
                                             OR keywords LIKE :term
                                             OR description LIKE :term
                                             ORDER BY clicks DESC");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $resultsHTML = "<div class = 'siteResults'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $title = $row["title"];
            $resultsHTML.= "$title <br>";
        }

        $resultsHTML.= "</div>";

        return $resultsHTML;
    }
}