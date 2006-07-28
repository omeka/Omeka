<?php
class CommonHelper
{
	public function javascripts()
	{
		foreach( func_get_args() as $arg ) {
			echo '<script type="text/javascript" src="' . WEB_CONTENT_DIR . '/common/javascripts/' . $arg . '"></script>' . "\n";
		}
	}
}
?>