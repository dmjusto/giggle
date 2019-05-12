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

        $fromLimit = ($page -1) * $pageSize;

        $query = $this->con->prepare("SELECT * 
                                            FROM sites WHERE title LIKE :term
                                             OR url LIKE :term
                                             OR keywords LIKE :term
                                             OR description LIKE :term
                                             ORDER BY clicks DESC
                                             LIMIT :fromLimit, :pageSize");

        $searchTerm = "%" . $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();

        $resultsHTML = "<div class = 'siteResults'>";

        while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->TrimField($title,55);
            $description = $this->TrimField($description, 230);

            $resultsHTML .= "<div class = 'resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url'>$title</a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                              </div>";

        }

        $resultsHTML.= "</div>";

        return $resultsHTML;
    }

    private function TrimField($string, $characterLimit){
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string,0,$characterLimit) . $dots;
    }
}