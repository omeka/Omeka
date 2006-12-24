function addQuickZoomLinks() {
	quickzoom = document.getElementById("quickzoom");
	quickzoom.innerHTML =  "<?php include('quickzoom.php'); ?>";	
}

//addLoadListener(addQuickZoomLinks);