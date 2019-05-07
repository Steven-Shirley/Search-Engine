<!-- this is the index page, it's an html document -->
<!DOCTYPE html>
<html>
<head>
<!-- title is displayed on a tab at the top of a page -->
	<title>Welcome to the search engine</title>
	
	<!-- sets the description, keywords, author and width of page (changes depending on what device is used) -->
	<meta name="description" content="Search for websites and images">
  	<meta name="keywords" content="search engine, websites, images">
  		<meta name="author" content="Steven Shirley">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Links CSS file  -->
	<link rel="stylesheet" type="text/css" href="assets/CSS/style.css">
</head>
<body>
	<!-- overall class for the index page  -->
	<div class="wrapper indexPage">
	<!-- Main section class -->
		<div class="mainSection">
	
			<div class="logoContainer">
		<!-- adds logo to the homepage -->
				<img src="assets/images/Soogle.png">
			</div>
		
			<div class="searchContainer">
			<!-- links to search page -->
			<form action="search.php" method="GET">
			
			<!-- creates a textbox -->
			<input class="searchBox" type="text" name="term" placeholder="Enter Search...">
			<!-- Creates a button -->
			<input class="searchButton" type="submit" value="Search">
			
			</form>
		
			</div>
	
		</div>
	</div>
	
</body>
</html>