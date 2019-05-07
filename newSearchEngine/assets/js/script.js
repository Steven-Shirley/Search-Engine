//creates a timer variable
var timer;

$(document).ready(function() {
	
	$(".result").on("click", function() {
		// creates id and url variable, also attaches an attribute to both
		var id = $(this).attr("data-linkId");
		var url = $(this).attr("href");
		console.log(id);
		
		//if id isn't found then return an alert
		if(!id) {
			alert("data-linkId attribute not found");
		}
		//uses the increaseLinkFunction in this function
		increaseLinkClicks(id, url);
		
		return false;
	});
	//creates a grid for imageResults
	var grid = $(".imageResults");
	
	grid.on("layoutComplete", function(){
		$(".gridItem img").css("visibility", "visible");
	});
	//uses the masonry gird layout
	grid.masonry({
		itemSelector: ".gridItem",
		columnWidth: 200,
		gutter: 5,
		transitionDuration: 0,
		isInitLayout: false
	});
	
	//sets up fancybox and adds clickable links to it
	$("[data-fancybox]").fancybox({
		
		caption : function( instance, item ) {
	        var caption = $(this).data('caption') || '';
	        var siteUrl = $(this).data('siteurl') || '';

	        if ( item.type === 'image' ) {
	            caption = (caption.length ? caption + '<br />' : '') 
	            + '<a href="' + item.src + '">View image</a><br>'
	            + '<a href="' + siteUrl + '">Visit page</a>';
	        }
	        
	        

	        return caption;
	    },
	    //controls the behaviour of fancybox after the event has happened
	    afterShow : function( instance, item ) {
	    	increaseImageClicks(item.src);
	    	
	    }
	    
	});
	
});

//sets a timer on masonry so that the images dont load everytime the user scrolls or resizes the page
function loadImage(src, className) {
	var image = $("<img>");
	
	image.on("load", function(){
		$("." + className + " a").append(image);
		
		clearTimeout(timer);
		
		timer= setTimeout(function(){
			$(".imageResults").masonry();
		}, 500);
		
		
	});
	//removes broken links and calls the ajax page
	
	image.on("error", function() {
		
		$("." + className).remove();
		
		$.post("ajax/setBroken.php", {src: scr});
		
	});
	
	image.attr("src", src);
}

function increaseLinkClicks(linkId, url) {
	//links to ajax file, if user clicks a link add 1 to clicks in the database and rank the site higher.
	$.post("ajax/updateLinkCount.php", {linkId: linkId})
	.done(function(result) {
		if(result != "") {
			alert(result);
			return;
		} 
		
		window.location.href = url;
	});
	
}

function increaseImageClicks(imageUrl) {
	//links to ajax file, if user clicks an image add 1 to clicks in the database and rank the image higher.
	$.post("ajax/updateImageCount.php", {imageUrl: imageUrl})
	.done(function(result) {
		if(result != "") {
			alert(result);
			return;
		} 
	
	});
	
}