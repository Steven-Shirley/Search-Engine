<?php
//include different files
include("config.php");
include("classes/DomDocumentParser.php");

//variables
$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

//function to insert data into database
function insertLink($url, $title, $description, $keywords) {
    global $con;
    
    //prepared statement so SQL injection can't occur
    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
							VALUES(:url, :title, :description, :keywords)");
    
    //binds the differnt paramaters
    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);
    
    return $query->execute();
}

function insertImage($url, $src, $alt, $title) {
    global $con;
    
    //prepared statement so SQL injection can't occur
    $query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
							VALUES(:siteUrl, :imageUrl, :alt, :title)");
    
    //binds the differnt paramaters
    $query->bindParam(":siteUrl", $url);
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);
    
    return $query->execute();
}

function linkExists($url) {
    global $con;
    
    //select all from database where = url
    $query = $con->prepare("SELECT * FROM sites WHERE url= :url");
    
    $query->bindParam(":url", $url);
    $query->execute();
    
    //return if row isnt = 0
    return $query->rowCount() != 0;
}

function createLink($src, $url) {
    
    //sets the scheme = to http
    $scheme = parse_url($url)["scheme"]; // http
    //host is the host website
    $host = parse_url($url)["host"]; // www.wikipedia.com
    
    //if else statement to fix links that dont have the correct scheme. example - might be missing 
    if(substr($src, 0, 2) == "//") {
        $src =  $scheme . ":" . $src;
    }
    else if(substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    else if(substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    else if(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    
    return $src;
}

function getDetails($url) {
    
    //makes the alreadyFoundImages variable useable in the this function
    global $alreadyFoundImages;
    
    //DomDocument parser has control
    $parser = new DomDocumentParser($url);
    
    //get titles
    $titleArray = $parser->getTitleTags();
    
    //if no title don't return the result
    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
        return;
    }
    //makes title a node value
    $title = $titleArray->item(0)->nodeValue;
    $title = str_replace("\n", "", $title);
    
    //return title
    if($title == "") {
        return;
    }
    
    $description = "";
    $keywords = "";
    
    //meta tags array
    $metasArray = $parser->getMetatags();
    
    //foreach meta tag
    foreach($metasArray as $meta) {
        //get description attribute and set it as content
        if($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }
        //get keywords attribute and set it as content
        if($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }
    // take a new line and print out the description
    $description = str_replace("\n", "", $description);
    //take a new line and print out the keywords
    $keywords = str_replace("\n", "", $keywords);
    
    //if the url arleady exists in the database print
    if(linkExists($url)) {
        echo "$url already exists<br>";
    } 
    // else input link into database
    else if(insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    } 
    //error message if it can't be input for whatever reason
    else {
        echo "ERROR: Failed to insert $url<br>";
    }
    //image array that acts the same as the above foreach 
    $imageArray = $parser->getImages();
    foreach ($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");
        
        if(!$title && !$alt) {
            continue;
        }
        
        $src = createLink($src, $url);
        
        //if not in array add image
        if(!in_array($src, $alreadyFoundImages)) {
            $alreadyFoundImages[] = $src;
            
            insertImage($url, $src, $alt, $title);
        }
        
    }
    
    
}

function followLinks($url) {
    
    global $alreadyCrawled;
    global $crawling;
    
    //DomDocumentParser has control
    $parser = new DomDocumentParser($url);
    
    //parser gets links
    $linkList = $parser->getLinks();
    
    //makes link a clickable url
    foreach($linkList as $link) {
        $href = $link->getAttribute("href");
        
        //if link has # dont include
        if(strpos($href, "#") !== false) {
            continue;
        }
        //if link is a javascript link dont include
        else if(substr($href, 0, 11) == "javascript:") {
            continue;
        }
        
        
        $href = createLink($href, $url);
        
        //update alreadyCrawled variable to add new links that have been crawled
        if(!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;
            
            getDetails($href);
        }
        
        
        
    }
    
    array_shift($crawling);
    
    foreach($crawling as $site) {
        followLinks($site);
    }
    
}
//starts crawling on that page
$startUrl = "http://www.gamespot.com";
followLinks($startUrl);
?>