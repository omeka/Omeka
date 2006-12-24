<?php

class HtmlHelper
{
	public function thumbnail( $file, $properties = array(), $width = null, $height = null )
	{
		$path = WEB_THUMBNAIL_DIR . DS . $file;
		$abs_path = ABS_THUMBNAIL_DIR . DS . $file;
		if( file_exists( $abs_path ) ) {
			$html = '<img src="' . $path . '" ';
			foreach( $properties as $k => $v ) {
				$html .= $k . '="' . $v . '" ';
			}
			if( $width && !$height )
			{
				list($o_width, $o_height) = getimagesize( $abs_path );
				if( $o_width > $width )
				{
					$ratio = $width / $o_width;
					$height = $o_height * $ratio;
					$html .= 'width="' . $width . '" height="' . $height . '"';
				}
			}
			$html .= '/>' . "\n";
			echo $html;
		} else {
			echo '<img src="' . $path . '" alt="Thumbnail image missing." />' . "\n";
		}
	}
	
	public function tagCloud( array $tags, $largest, $link = null, $max = '4', $min = '1', $units = 'em' )
	{	
		foreach( $tags as $tag )
		{
			$size = round( ( ( $tag['tagCount'] / $largest ) * $max ), 3 );
			
			$size = ($size < $min) ? $min : $size;

			$html = '<span style="font-size:' . $size . $units . '">';

			if( $link )
			{
				$html .= '<a href="' . $link . '?tags=' . $tag['tag_name'] . '">';
			}

			$html .= $tag['tag_name'];

			if( $link )
			{
				$html .= '</a>';
			}

			$html .= '</span>' . "\n";

			echo $html;
		}
	}
}

?>