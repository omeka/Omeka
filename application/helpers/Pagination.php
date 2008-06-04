<?php
/*
Copyright 2008 Fab Apps

Contact:
Fab Apps
info@fabapps.com
http://fabapps.com/software/pagination.php

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Use this style to make the pagination horizontal: .pagination li {display:inline;}
class Omeka_View_Helper_Pagination
{
    /**
     * @param    int         $page          Current page number
     * @param    int         $perPageCount  Number of results per page
     * @param    int         $totalCount    Total number of results
     * @param    array       $options       OPTIONAL Associative array containing 
     *       any or all available class options, for example:
     *       array('pageName'     => [string|bool], 
     *             'url'          => [string], 
     *             'queryArray'   => [array], 
     *             'pagesType'    => [array], 
     *             'displayFormat'=> [int], 
     *             'classes'      => [array], 
     *             'texts'        => [array])
     *       'pageName'      OPTIONAL Name of the page identifier; 
     *           set this to false when the page name is not in the query string; 
     *           defaults to 'page'.
     *       'url'           OPTIONAL URL of subsequent page; 
     *           defaults to $_SERVER['PHP_SELF'].
     *       'queryArray'    OPTIONAL Associative array containing 
     *           passed form data; most of the time this is $_GET; used only when a 
     *           query string is necessary to pass variables to subsequent pages; 
     *           defaults to $_GET.
     *       'pagesType'     OPTIONAL Associative array containing 
     *           pages type as key and number as value; defaults to array('show'=>10).
     *               Option 1: array('show'=>[int]): this number of pages will display 
     *               at all times; 
     *               Option 2: array('pad'=>[int]): this number of pages will pad to 
     *               each side of the current page;
     *       'displayFormat' OPTIONAL Number that signifies the 
     *           display format of the pagination, following this component framework: 
     *           first/previous/pages/next/last; defaults to 1.
     *               1: display all components, even unlinked ones; 
     *               2: display only active components, i.e. only when f/p/n/l are needed; 
     *               3: display all components except pages; 
     *               4: display only active components except pages; 
     *               5: display only pages, i.e. do not display f/p/n/l;
     *       'classes'       OPTIOANL Associative array containing 
     *           class names; may contain the following keys: pagination/first/
     *           firstGhost/previous/previousGhost/ellipsis/currentPage/pages/next/
     *           nextGhost/last/lastGhost; see _sanitizeClasses() for defaults.
     *       'texts'         OPTIONAL Associative array containing 
     *           link texts; may contain the following keys: first/previous/ellipsis/
     *           next/last; see _sanitizeTexts() for defaults.
     * @return    The entire pagination string in XHTML.
     */
    public function pagination(
        $page, 
        $perPageCount, 
        $totalCount, 
        $options = array(
            'pageName'      => null, 
            'url'           => null, 
            'queryArray'    => null, 
            'pagesType'     => null, 
            'displayFormat' => null, 
            'classes'       => null,         
            'texts'         => null
        )
    )
    {
        // Sanitize passed variables.
        $this->page          = $this->_sanitizePage($page);
        $this->perPageCount  = $this->_sanitizePerPageCount($perPageCount);
        $this->totalCount    = $this->_sanitizeTotalCount($totalCount);
        $this->pageName      = $this->_sanitizePageName($options['pageName']);
        $this->url           = $this->_sanitizeUrl($options['url']);
        $this->queryArray    = $this->_sanitizeQueryArray($options['queryArray']);
        $this->pagesType     = $this->_sanitizePagesType($options['pagesType']);
        $this->displayFormat = $this->_sanitizeDisplayFormat($options['displayFormat']);
        $this->classes       = $this->_sanitizeClasses($options['classes']);
        $this->texts         = $this->_sanitizeTexts($options['texts']);
        
        // Set the total number of pages.
        $this->totalPages = ceil($totalCount / $perPageCount);
        
        // Don't output anything (even <ul>) if we have less results than pages
        if ( ((float) $totalCount / $perPageCount) < 1) {
            return;
        }
        
        // Begin building the pagination string.
        $pagination = "\n" . '<ul class="'.$classes['pagination'].'">';
        
        if (in_array($this->displayFormat, array(1,2,3,4))) {
            $pagination .= $this->_createFirstLink();
            $pagination .= $this->_createPreviousLink();
        }
        
        // Use the "show" pagination type.
        if (array_key_exists('show', $this->pagesType) && in_array($this->displayFormat, array(1,2,5))) {
            $pagination .= $this->_createShowPagination();

        // Use the "pad" pagination type.
        } elseif (array_key_exists('pad', $this->pagesType) && in_array($this->displayFormat, array(1,2,5))) {
            $pagination .= $this->_createPadPagination();
        }
        
        if (in_array($this->displayFormat, array(1,2,3,4))) {
            $pagination .= $this->_createNextLink();
            $pagination .= $this->_createLastLink();            
        }
        
        $pagination .= "\n</ul>";
        
        return $pagination;
    }
    
    protected function _buildUrl($subsequentPage, $pageName=null, $url=null, $queryArray=array())
    {
        //Use the properties of this object if none are passed
        if (!$pageName) {
            $pageName = $this->pageName;
        }
        
        if(!$url) {
            $url = $this->url;
        }
        
        if(!$queryArray) {
            $queryArray = $this->queryArray;
        }
        
        // Remove the page element from the $queryArray, since it will be added 
        // below.
        if (array_key_exists($pageName, $queryArray)) unset($queryArray[$pageName]);
        
        // If $pageName is given, assume the page identifier was passed in 
        // $queryArray.
        if (strlen($pageName)) {
            $queryString = count($queryArray) ? '&amp;'.http_build_query($queryArray, '', '&amp;') : '';
            return $url.'?'.$pageName.'='.$subsequentPage.$queryString;
        
        // If $pageName is not given, assume the page identifier was passed in 
        // $url. For example: baseUrl/controller/action/page/1.
        } else {
            $queryString = count($queryArray) ? '/?'.http_build_query($queryArray, '', '&amp;') : '';
            return $url.$subsequentPage.$queryString;
        }
    }
    
    /**
     * Create a list item link for each of the pagination links.
     * 
     * @param string
     * @param integer
     * @param boolean
     * @param string
     * @param string
     * @return string
     **/
    protected function _createLink($page, $text, $useLink, $listClass, $alternateClass)
    {
        if ($useLink) {
            $href = $this->_buildUrl($page);
            $html = "\n" . '<li class="' . $listClass . '">';
            $html .= '<a href="' . $href . '">' . $text . '</a>';
        } else {
            $html = "\n" . '<li class="' . $alternateClass . '">';
            $html .= $text;
        }
        
        $html .= '</li>';
        return $html;
    }
    
    protected function _createFirstLink() 
    {
        extract(get_object_vars($this));
        
        $makeLink = (1 != $page);
        
        //If we're on the first page and using display format 2 or 4, don't do it
        if (!$makeLink and in_array($displayFormat, array(2, 4))) {
            return;
        }
        
        return $this->_createLink(1, $texts['first'], $makeLink, 
            $classes['first'], $classes['firstGhost']);  
    }
    
    protected function _createPreviousLink()
    {
        extract(get_object_vars($this));
        
        // Make hyperlink whenever we are past the first page
        $makeLink = ($page > 1);
        
        // Don't show un-linked text if using display format 2 or 4
        if (!$makeLink and in_array($displayFormat, array(2, 4))) {
            return;
        }
        
        return $this->_createLink($page - 1, $texts['previous'], $makeLink,
            $classes['previous'], $classes['previousGhost']);     
    }
    
    protected function _createNextLink()
    {
        extract(get_object_vars($this));
        
        // Only make "next" a hyperlink if the total result count is greater 
        // than what has been shown so far.
        $makeLink = $totalCount > $page * $perPageCount;
        
        // Don't show un-linked text if using display format 2 or 4
        if(!$makeLink and in_array($displayFormat, array(2,4))) {
            return;
        }
        
        return $this->_createLink($page + 1, $texts['next'], $makeLink, 
            $classes['next'], $classes['nextGhost']);
    }
    
    /**
     * @internal There is still some duplication in all of these methods
     * but less than before.
     * @return string
     **/
    protected function _createLastLink()
    {
        extract(get_object_vars($this));
        
        $makeLink = ($totalPages != $page);
        
        // Don't show un-linked text if using display format 2 or 4
        if(!$makeLink and in_array($displayFormat, array(2,4))) {
            return;
        }
        
        return $this->_createLink($totalPages, $texts['last'], $makeLink, 
            $classes['last'], $classes['lastGhost']);
    }
    
    /**
     * Create the set of page links.  All pages should be hyperlinked unless
     * except the current page.  All current pages should have the 'currentPage'
     * class setting as its class, whereas the pages that aren't current
     * should have the 'pages' class setting as their class.
     * 
     * @param integer
     * @param integer
     * @return string
     **/
    protected function _createPageLinks($start, $finish)
    {
        $html = '';
        for ($i= $start; $i <= $finish; $i++) { 
            $html .= $this->_createLink($i, $i, ($this->page != $i), 
                $this->classes['currentPage'], $this->classes['pages']);
        }
        return $html;
    }
    
    protected function _createShowPagination()
    {
        extract(get_object_vars($this));
        
        $show    = $pagesType['show'];
        $prePad  = floor($show/2);
        $postPad = ceil($show/2);

        // Show all pages and no ellipses if the show number is greater than 
        // or equal to the total number of pages.
        if ($show >= $totalPages) {
            $pagination .= $this->_createPageLinks(1, $totalPages);
        // Oterwise, perform calculations to show the specified number of 
        // pages in the pagination.
        } else {
            if (1 < $page - $prePad) {
                $pagination .= '
<li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
            }
            if (1 > $page - $prePad) {
                $pagination .= $this->_createPageLinks(1, $show);
            }
            if (1 <= $page - $prePad && $totalPages >= $page + $postPad - 1) {
                $start = $page - $prePad;
                $finish = $page + $postPad - 1;
                $pagination .= $this->_createPageLinks($start, $finish);
            }
            if ($totalPages < $page + $postPad - 1) {
                $pagination .= $this->_createPageLinks($totalPages - $show + 1, 
                    $totalPages);
            }
            if ($totalPages > $page + $postPad - 1) {
                $pagination .= '
<li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
            }
        }
        
        return $pagination;
    }
    
    protected function _createPadPagination()
    {
        extract(get_object_vars($this));

        $pad = $pagesType['pad'];

        if (1 < $page - $pad) {
            $pagination .= '<li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
        }
                
        // We won't link to pages that are below 1 (doesn't exist)
        $start = ($page - $pad) > 0 ? $page - $pad : 1;
        
        // We won't link to pages that are above the total # of pages
        $finish = ($page + $pad) < $totalPages ? $page + $pad : $totalPages;
        
        $pagination .= $this->_createPageLinks($start, $finish);

        if ($page < $totalPages - $pad) {
            $pagination .= '<li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
        }
                
        return $pagination;        
    }
    
    protected function _sanitizePage($page)
    {
        return is_numeric($page) ? round(abs($page)): 1;
    }
    
    protected function _sanitizePerPageCount($perPageCount)
    {
        return is_numeric($perPageCount) ? round(abs($perPageCount)): 10;
    }
    
    protected function _sanitizeTotalCount($totalCount)
    {
        return is_numeric($totalCount) ? round(abs($totalCount)): 0;
    }
    
    protected function _sanitizePageName($pageName)
    {
        return is_string($pageName) ? $pageName : ($pageName === false ? null : 'page');
    }
    
    protected function _sanitizeUrl($url)
    {
        return is_string($url) ? $url : $_SERVER['PHP_SELF'];
    }
    
    protected function _sanitizeQueryArray($queryArray)
    {
        return is_array($queryArray) ? $queryArray : $_GET;
    }
    
    protected function _sanitizePagesType($pagesType)
    {
        $pagesType = is_array($pagesType) ? $pagesType : array('show'=>10);
        
        if (array_key_exists('show', $pagesType)) {
            $typeNumber = is_numeric($pagesType['show']) ? round(abs($pagesType['show'])) : 10;
            $pagesType = array('show'=>$typeNumber);
        } elseif (array_key_exists('pad', $pagesType)) {
            $typeNumber = is_numeric($pagesType['pad']) ? round(abs($pagesType['pad'])) : 5;
            $pagesType = array('pad'=>$typeNumber);
        } else {
            $pagesType = array('show'=>10);
        }
        
        return $pagesType;
    }
    
    protected function _sanitizeDisplayFormat($displayFormat)
    {
        $displayFormat = is_numeric($displayFormat) ? $displayFormat: 1;
        
        $displayFormatOptions = array(
            1 => 'display all', 
            2 => 'display active', 
            3 => 'display all no pages', 
            4 => 'display active no pages', 
            5 => 'display pages only'
        );
        
        return array_key_exists($displayFormat, $displayFormatOptions) ? $displayFormat : 1;
    }
    
    protected function _sanitizeClasses($classes)
    {
        $classes = is_array($classes) ? $classes : array();
        
        $cls = array();
        $cls['pagination']    = array_key_exists('pagination', $classes) ? $classes['pagination'] : 'pagination';
        $cls['first']         = array_key_exists('first', $classes) ? $classes['first'] : 'paginationFirst';
        $cls['firstGhost']    = array_key_exists('firstGhost', $classes) ? $classes['firstGhost'] : 'paginationFirstGhost';
        $cls['previous']      = array_key_exists('previous', $classes) ? $classes['previous'] : 'paginationPrevious';
        $cls['previousGhost'] = array_key_exists('previousGhost', $classes) ? $classes['previousGhost'] : 'paginationPreviousGhost';
        $cls['ellipsis']      = array_key_exists('ellipsis', $classes) ? $classes['ellipsis'] : 'paginationEllipsis';
        $cls['currentPage']   = array_key_exists('currentPage', $classes) ? $classes['currentPage'] : 'paginationCurrentPage';
        $cls['pages']         = array_key_exists('pages', $classes) ? $classes['pages'] : 'paginationPages';
        $cls['next']          = array_key_exists('next', $classes) ? $classes['next'] : 'paginationNext';
        $cls['nextGhost']     = array_key_exists('nextGhost', $classes) ? $classes['nextGhost'] : 'paginationNextGhost';
        $cls['last']          = array_key_exists('last', $classes) ? $classes['last'] : 'paginationLast';
        $cls['lastGhost']     = array_key_exists('lastGhost', $classes) ? $classes['lastGhost'] : 'paginationLastGhost';
        
        return $cls;
    }
    
    protected function _sanitizeTexts($texts)
    {
        $texts = is_array($texts) ? $texts : array();
        
        $txt = array();
        $txt['first']    = array_key_exists('first', $texts) ? $texts['first'] : '&#171;&#171;first';
        $txt['previous'] = array_key_exists('previous', $texts) ? $texts['previous'] : '&#171;previous';
        $txt['ellipsis'] = array_key_exists('ellipsis', $texts) ? $texts['ellipsis'] : '&#8230;';
        $txt['next']     = array_key_exists('next', $texts) ? $texts['next'] : 'next&#187;';
        $txt['last']     = array_key_exists('last', $texts) ? $texts['last'] : 'last&#187;&#187;';
        
        return $txt;
    }
    
}
?>