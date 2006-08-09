<?php
class LinkHelper
{
	public function to( $template = null, $action = null )
	{
		$template = !empty( $template ) ? '/' . trim( $template, '/' ) . '/' : '/';
		$action = !empty( $action ) ? trim( $action, '/' ) . '/' : null;		
		return BASE_URI . $template . $action;
	}
	
	public function in( $file, $dir = null )
	{
		$file = '/' . trim( $file, '/' );
		$dir = !empty( $dir ) ? '/' . trim( $dir, '/' ) : null;
		return WEB_CONTENT_DIR . SELECTED_THEME_DIR . $dir . $file;
	}
	
	public function style( $file, $dir = 'styles' )
	{
		echo WEB_CONTENT_DIR . SELECTED_THEME_DIR . DS . 'styles' . DS . $file;
		//echo '<link rel="stylesheet" href="' . $dir . $file . '" type="text/css">' . "\n";
	}
	
	/**
	 *	link_end is depreciated with link_to, link_to functionality should be worked into this method
	*/
	public function pagination( $page = 1, $per_page, $total, $num_links, $link, $link_end = null )
	{
		$num_pages = ceil( $total / $per_page );

		$query = !empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : null;

		$html = ' <a href="' . $link . '1' . $link_end . $query . '">First</a> |';
		
		if( $page > 1 ) {
			$html .= ' <a href="' . $link . ($page - 1) . $link_end . $query . '">&lt Prev</a> |';
		} else {
			$html .= ' &lt Prev |';
		}

		$buffer = floor( ( $num_links - 1 ) / 2 );
		$start_link = ( ($page - $buffer) > 0 ) ? ($page - $buffer) : 1;
		$end_link = ( ($page + $buffer) < $num_pages ) ? ($page + $buffer) : $num_pages;

		if( $start_link == 1 ) {
			$end_link += ( $num_links - $end_link );
		}elseif( $end_link == $num_pages ) {
			$start_link -= ( $num_links - ($end_link - $start_link ) - 1 );
		}
		
		for( $i = $start_link; $i < $end_link+1; $i++) {
			if( $i <= $num_pages ) {
				if( $page == $i ) {
					$html .= ' ' . $i . ' |';
				} else {
					$html .= ' <a href="' . $link . $i . $link_end . $query . '">' . ($i) . '</a> |';
				}
			}
		}

		if( $page < $num_pages ) {
			$html .= ' <a href="' . $link . ($page + 1) . $link_end . $query . '">Next &gt</a> |';
		} else {
			$html .= ' Next &gt |';
		}
		
		$html .= ' <a href="' . $link . $num_pages . $link_end . $query . '">Last</a> ';
		
		$html .= '<select id="pagination-link" onchange="location.href = \''.$link.'\' + this.value + \'' . $link_end . $query.'\'">';
		$html .= '<option>Page:&nbsp;&nbsp;</option>';
		for( $i = 0; $i < $num_pages; $i++ ) {
			$html .= '<option value="' . ($i + 1) . '"';
			//if( $page == ($i+1) ) $html .= ' selected ';
			$html .= '>' . ($i + 1) . '</option>';
		}
		$html .= '</select>';
		
		echo $html;
	}
}
?>