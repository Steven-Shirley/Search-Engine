<?php
class imageResultsProvider {
    
    private $con;
    
    public function __construct($con) {
        $this->con = $con;
    }
    
    //uses a prepared statement to get all titles and alts that are like the search term and and aren't broken
    public function getNumResults($term) {
        $query = $this->con->prepare("SELECT COUNT(*) as total FROM images WHERE (title LIKE :term
                                     OR alt LIKE :term) AND broken = 0");
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
        $query = $this->con->prepare("SELECT * FROM images WHERE (title LIKE :term
                                     OR alt LIKE :term) AND broken = 0
                                     ORDER BY clicks DESC
                                     LIMIT :fromLimit, :pageSize");
        $searchTerm = "%".$term."%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();
        
        $resultsHtml = "<div class='imageResults'>";
        
        $count= 0;
        
        //fetch from database the different coloumns
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];
            
            //if theres a title display, else if alt display, else display image URL
            if($title) {
                $displayText = $title;
            } else if($alt) {
                $displayText = $alt;
            } else {
                $displayText = $imageUrl;
            }
            
            //sets up fancybox and uses javaScript to load image
            $resultsHtml .= "<div class='gridItem image$count'>
                                <a href='$imageUrl' data-fancybox data-caption='$displayText'
                                    data-siteurl='$siteUrl'>
                                    
                                    <script>
                                        $(document).ready(function() {
                                            loadImage(\"$imageUrl\",\"image$count\");
                                        });
                                    </script>
                                    
                                    <span class='details'>$displayText</span>
                                </a>
                            </div>";
        }
        
        $resultsHtml .="</div>";
        
        return $resultsHtml;
    }
    
    
}


?>