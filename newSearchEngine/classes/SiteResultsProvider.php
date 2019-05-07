<?php 
class SiteResultsProvider {
    
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    //uses a prepared statement to get all titles, descriptions and keywords that are like the search term
    public function getNumResults($term) {
        $query = $this->con->prepare("SELECT COUNT(*) as total FROM sites WHERE title LIKE :term
                                     OR url LIKE :term OR keywords LIKE :term
                                     OR description LIKE :term");
        $searchTerm = "%".$term."%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();
        
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"]; 
    }
    
    public function getResultsHtml($page, $pageSize, $term) {
        
        //page is multiplied by the page size and and won't go over the max pages
        $fromLimit = ($page - 1) * $pageSize;
        
        //prepared statement that selects all from database that matches the search term. Also limits results per page
        $query = $this->con->prepare("SELECT * FROM sites WHERE title LIKE :term
                                     OR url LIKE :term OR keywords LIKE :term
                                     OR description LIKE :term
                                     ORDER BY clicks DESC 
                                     LIMIT :fromLimit, :pageSize");
        //term can have infor on either side of it to narrow searches 
        $searchTerm = "%".$term."%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();
        
        $resultsHtml = "<div class='siteResults'>";
        
        //fetch from database the different coloumns
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];
            
            //trims title and description to a specifc number of letters
            $title = $this->trimField($title, 55);
            $description = $this->trimField($description, 230);
            
            $resultsHtml .= "<div class='resultContainer'>
                                <h3 class='title'>
                                    <a class='result' href='$url' data-linkId='$id'>
                                        $title
                                    </a>
                                </h3>
                                <span class='url'>$url</span>
                                <span class='description'>$description</span>
                            </div>";
        }
        
        $resultsHtml .="</div>";
        
        return $resultsHtml;
    }
    
    private function trimField($string, $characterLimit) {
        //adds ... to the trimmed fields
        $dots = strlen($string) > $characterLimit ? "..." : "";
        return substr($string, 0, $characterLimit) . $dots;
    }
    
}


?>