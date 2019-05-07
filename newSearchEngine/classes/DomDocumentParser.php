<?php
class DomDocumentParser {
    
    private $doc;
    
    public function __construct($url) {
        
        //gets data from website and let website know that it's a crawler
        $options = array(
            'http'=>array('method'=>"GET", 'header'=>"User-Agent: crawlBot/0.1\n")
        );
        $context = stream_context_create($options);
        
        $this->doc = new DomDocument();
        @$this->doc->loadHTML(file_get_contents($url, false, $context));
    }
    
    //gets links
    public function getlinks() {
        return $this->doc->getElementsByTagName("a");
    }
    //gets titles
    public function getTitleTags() {
        return $this->doc->getElementsByTagName("title");
    }
    //gets meta tags
    public function getMetaTags() {
        return $this->doc->getElementsByTagName("meta");
    }
    //gets images
    public function getImages() {
        return $this->doc->getElementsByTagName("img");
    }
    
}
?>