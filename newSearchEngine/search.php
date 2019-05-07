<?php 
//includes different files
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/imageResultsProvider.php");

//gets terms, if term isn't set then return "you must enter a search term"
if(isset($_GET["term"])) {
    $term = $_GET["term"];
} else {
    exit("You must enter a search term");
}

//type and page are set as variables, it then gets site and page
$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
$page = isset($_GET["page"]) ? $_GET["page"] : 1;

    
?>
<!-- html document -->
<!DOCTYPE html>
<html>
<head>
	<!-- title is set on the page tab -->
	<title>Welcome to the search engine</title>
	
	<!-- links jquery fancybox plugin -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<!-- links CSS file -->
	<link rel="stylesheet" type="text/css" href="assets/CSS/style.css">
	
	<!-- script that adds jquery support to the webpage  -->
	<script
  		src="https://code.jquery.com/jquery-3.3.1.min.js"
  		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
 	 	crossorigin="anonymous">
	</script>
</head>
<body>
	
	<div class="wrapper">
		
		<div class="header">
	
			<div class="headerContent">
				
				<!-- logoContainer class that adds the logo -->
				<div class="logoContainer">
					<a href="index.php">
						<img src="assets/images/Soogle.png">
					</a>
			
				</div>
			
					<div class="searchContainer">
					
					<!-- get method used -->
					<form action="search.php" method="GET">
						
						<!-- sets the name and prints out the search term that the user requested -->
						<div class="searchBarContainer">
							<input type="hidden" name="type" value="<?php echo $type; ?>">
							<input class="searchBox" type="text" name="term" value="<?php echo $term;?>">
							<button class="searchButton">
								<img src="assets/images/Icons/searchPageIcon.png">
							</button>
					
						</div>
				
					</form>
			
			
					</div>
					
				
			</div>
			
			<div class="tabsContainer">
				<ul class="tabList">
					<!-- takes the type variable  and makes it active. will search for the term that the user entered 
					and return those results -->
					<li class="<?php echo $type == 'sites' ? 'active' : '' ?>" >
						<a href='<?php  echo "search.php?term=$term&type=sites"; ?>'>
							Sites
						</a>
					</li>
					
					<!-- takes the type variable (images) and makes it active. will search for the term that the user entered 
					and return those results -->
					<li class="<?php echo $type == 'images' ? 'active' : '' ?>" >
						<a href='<?php  echo "search.php?term=$term&type=images"; ?>'>
							Images
						</a>
					</li>
					
				</ul>
				
				</div>
		</div>		
		
		<div class= "mainResultsSection">
			
			<?php 
			//if sites tab is clicked link to SiteResultsProvider and set page size to 20 results
			if($type == "sites") {
			    $resultsProvider = new SiteResultsProvider($con);
			    $pageSize = 20;
			//else return imageResultsProvider and set page size to 30 results    
			} else {
			    $resultsProvider = new imageResultsProvider($con);
			    $pageSize = 30;
			}
			
			//number of results is qual to resultsProver, get number of results
			$numResults = $resultsProvider->getNumResults($term);
			
			//print this out
			echo "<p class='resultsCount'>$numResults results found</p>"; 
			
			//print out the page, page size and term
			echo $resultsProvider->getResultsHtml($page, $pageSize, $term);
			
			?>
		
		
		</div>
		
		<div class="pageinationContainer">
			<div class="pageButtons">
			
				<div class="pageNumberContainer">
					<img src="assets/images/pageStart.png">
				</div>
				
				<?php  
				//pagination system
				//shows 10 pages
				$pagesToShow = 10;
				//page number = ceil(round up to nearest integer) numResults/pageSize
				$numPages = ceil($numResults / $pageSize);
				//number of pages left = min
				$pagesLeft = min($pagesToShow, $numPages);
				
				//current page = page - floor(round down to nearest integer) pages to show/2
				$currentPage = $page - floor($pagesToShow / 2);
				
				//current page cant be less than 1
				if($currentPage < 1) {
				    $currentPage = 1;
				}
				
				if($currentPage + $pagesLeft > $numPages + 1) {
				    $currentPage = $numPages + 1 - $pagesLeft;
				}
				
				while($pagesLeft != 0 && $currentPage <= $numPages) {
				    
				    //use the pageSelected image for the users current page 
				    if($currentPage == $page) {
				        echo "<div class='pageNumberContainer'>
                                <img src='assets/images/pageSelected.png'>
                                <span class='pageNumber'>$currentPage</span>
                              </div>";
				    //else use the page image
				    } else {
				        echo "<div class='pageNumberContainer'>
                                <a href='search.php?term=$term&type=$type&page=$currentPage'>
                                <img src='assets/images/page.png'>
                                <span class='pageNumber'>$currentPage</span>
                                </a>
                              </div>";
				    }
				    
				    
				    //current page get looped until curent page cant go higher
				    $currentPage++;
				    //pages left gets deincremented until none are left
				    $pagesLeft--;
				    
				}
				
				?>
				
				
				<!-- use pageEnd image -->
				<div class="pageNumberContainer">
					<img src="assets/images/pageEnd.png">
				</div>
			
			</div>
		
		<!-- different scripts that have been added to search.php -->
		</div>
		<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
		<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
		<script type="text/javascript" src="assets/js/script.js"></script>
</body>
</html>