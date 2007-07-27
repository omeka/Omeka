<?php
// style-switcher.php
class AlternateStyles {
	var $styleSheet = array();	// @Array: collection of All Style Sheets
	var $altStyles = array();	// @Array: collection of Alternate Style Sheets
	var $prefStyleSheet = '';	// @String: The name (title) of the Preferred Style Sheet
	var $styleSheets = '';		// @String: All the style sheets output in their respective html formats
	// @constructor
	function AlternateStyles() {
		$this->prefStyleSheet = $this->cleanTitle($_GET['css']);
		if ( isset($_GET['cssJaxy']) && $_GET['cssJaxy'] == true ) {
			$this->setStyleCookie($this->prefStyleSheet);
			die();
		}
	}
	// @public
	function add($path,$media='',$title='',$alternate=false) {
		// first grab all global styles
		if ( !$title ) {
			$mediaRef = ($media != '' ? 'media="'.$media.'" ' : '');
			$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' />';
			// add it to our style sheet array
			array_push($this->styleSheet,$styleLink);
		}
		// otherwise we're adding the 'preferred' & 'alternates'
		else {
			$this->determinePreferred($path,$title,$media,$alternate);
		}
		// now grab our preferred
		$this->getPreferredStyles();
	}
	// @private
	function getPreferredStyles() {
		$this->styleSheets = '';
		$totalStyleSheets = count($this->styleSheet);
		for ( $i = 0; $i < $totalStyleSheets;$i++ ) {
			$this->styleSheets .= $this->styleSheet[$i]."\n";
		}
	}
	// @private
	function determinePreferred($path,$title,$media='',$alternate=false) {
		// still need that media thing no matter what
		$mediaRef = ($media != '' ? 'media="'.$media.'" ' : '');
		// if $_GET['css'] was set
		if ( $this->prefStyleSheet ) {
			$this->setStylecookie($this->prefStyleSheet);
			if ( $this->prefStyleSheet == $title ) {
				$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' title="'.$title.'" />';
			}
			else {
				$styleLink = '<link type="text/css" href="'.$path.'" rel=" alternate stylesheet" '.$mediaRef.' title="'.$title.'" />';
			}
		}
		// or we could have set a style sheet from before
		elseif ( $_COOKIE['PrefStyles'] ) {
			// odd bug with prototype, php, and cookies....don't ask
			$cookieCheck = $this->fixOurCookie($_COOKIE['PrefStyles']);
			if ( $cookieCheck == $title ) {
				$styleLink = '<link type="text/css" href="'.$path.'" rel="stylesheet" '.$mediaRef.' title="'.$title.'" />';
			}
			else {
				$styleLink = '<link type="text/css" href="'.$path.'" rel=" alternate stylesheet" '.$mediaRef.' title="'.$title.'" />';
			}
		}
		// probably just our first time here
		else  {
			$styleLink = '<link type="text/css" href="'.$path.'" rel="'.($alternate ? 'alternate ' : '' ).'stylesheet" '.$mediaRef.' title="'.$title.'" />';
		}
		array_push($this->styleSheet,$styleLink);
	}
	// @private
	function setStyleCookie($value) {
		setcookie("PrefStyles", $value, time()+(3600*24*365));  /* expires in 1 year */
	}
	// @private
	function cleanTitle($str) {
		return str_replace('_',' ',$str);
	}
	// @private
	function fixOurCookie($str) {
		$c = explode('?',$str);
		return $c[0];
	}
	// @public
	function drop() {
		// watchout! magic may occur
		echo $this->styleSheets;
	}
}
?>