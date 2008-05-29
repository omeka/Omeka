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
        $page          = $this->_sanitizePage($page);
        $perPageCount  = $this->_sanitizePerPageCount($perPageCount);
        $totalCount    = $this->_sanitizeTotalCount($totalCount);
        $pageName      = $this->_sanitizePageName($options['pageName']);
        $url           = $this->_sanitizeUrl($options['url']);
        $queryArray    = $this->_sanitizeQueryArray($options['queryArray']);
        $pagesType     = $this->_sanitizePagesType($options['pagesType']);
        $displayFormat = $this->_sanitizeDisplayFormat($options['displayFormat']);
        $classes       = $this->_sanitizeClasses($options['classes']);
        $texts         = $this->_sanitizeTexts($options['texts']);
        
        // Set the total number of pages.
        $totalPages = ceil($totalCount / $perPageCount);
        
        // Begin building the pagination string.
        $pagination = '
<ul class="'.$classes['pagination'].'">';
        
        if (in_array($displayFormat, array(1,2,3,4))) {
            // The "first" link.
            if (1 != $page) {
                $pagination .= '
    <li class="'.$classes['first'].'"><a href="'.$this->_buildUrl(1, $pageName, $url, $queryArray).'">'.$texts['first'].'</a></li>';
            } else {
                if (in_array($displayFormat, array(1,3))) $pagination .= '
    <li class="'.$classes['firstGhost'].'">'.$texts['first'].'</li>';
            }
            // The "previous" link.
            if (1 < $page) {
                $pagination .= '
    <li class="'.$classes['previous'].'"><a href="'.$this->_buildUrl($page - 1, $pageName, $url, $queryArray).'">'.$texts['previous'].'</a></li>';
            } else {
                if (in_array($displayFormat, array(1,3))) $pagination .= '
    <li class="'.$classes['previousGhost'].'">'.$texts['previous'].'</li>';
            }
        }
        
        // Use the "show" pagination type.
        if (array_key_exists('show', $pagesType) && in_array($displayFormat, array(1,2,5))) {
            
            $show    = $pagesType['show'];
            $prePad  = floor($show/2);
            $postPad = ceil($show/2);
            
            // Show all pages and no ellipses if the show number is greater than 
            // or equal to the total number of pages.
            if ($show >= $totalPages) {
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($page == $i) {
                        $pagination .= '
    <li class="'.$classes['currentPage'].'">'.$i.'</li>';
                    } else {
                        $pagination .= '
    <li class="'.$classes['pages'].'"><a href="'.$this->_buildUrl($i, $pageName, $url, $queryArray).'">'.$i.'</a></li>';
                    }
                }
            // Oterwise, perform calculations to show the specified number of 
            // pages in the pagination.
            } else {
                if (1 < $page - $prePad) {
                    $pagination .= '
    <li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
                }
                if (1 > $page - $prePad) {
                    for ($i = 1; $i <= $show; $i++) {
                        if ($page == $i) {
                            $pagination .= '
    <li class="'.$classes['currentPage'].'">'.$i.'</li>';
                        } else {
                            $pagination .= '
    <li class="'.$classes['pages'].'"><a href="'.$this->_buildUrl($i, $pageName, $url, $queryArray).'">'.$i.'</a></li>';
                        }
                    }
                }
                if (1 <= $page - $prePad && $totalPages >= $page + $postPad - 1) {
                    for ($i = $page - $prePad; $i <= $page + $postPad - 1; $i++) {
                        if ($page == $i) {
                            $pagination .= '
    <li class="'.$classes['currentPage'].'">'.$i.'</li>';
                        } else {
                            $pagination .= '
    <li class="'.$classes['pages'].'"><a href="'.$this->_buildUrl($i, $pageName, $url, $queryArray).'">'.$i.'</a></li>';
                        }
                    }
                }
                if ($totalPages < $page + $postPad - 1) {
                    for ($i = $totalPages - $show + 1; $i <= $totalPages; $i++) {
                        if ($page == $i) {
                            $pagination .= '
    <li class="'.$classes['currentPage'].'">'.$i.'</li>';
                        } else {
                            $pagination .= '
    <li class="'.$classes['pages'].'"><a href="'.$this->_buildUrl($i, $pageName, $url, $queryArray).'">'.$i.'</a></li>';
                        }
                    }
                }
                if ($totalPages > $page + $postPad - 1) {
                    $pagination .= '
    <li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
                }
            }
            
        // Use the "pad" pagination type.
        } elseif (array_key_exists('pad', $pagesType) && in_array($displayFormat, array(1,2,5))) {
        
            $pad = $pagesType['pad'];
            
            if (1 < $page - $pad) {
                $pagination .= '
    <li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
            }
            for ($i = $page - $pad; $i <= $page + $pad; $i++) {
                if ($page == $i) {
                    $pagination .= '
    <li class="'.$classes['currentPage'].'">'.$i.'</li>';
                } elseif (0 < $i && $totalPages >= $i) {
                    $pagination .= '
    <li class="'.$classes['pages'].'"><a href="'.$this->_buildUrl($i, $pageName, $url, $queryArray).'">'.$i.'</a></li>';
                }
            }
            if ($page < $totalPages - $pad) {
                $pagination .= '<li class="'.$classes['ellipsis'].'">'.$texts['ellipsis'].'</li>';
            }
        }
        
        if (in_array($displayFormat, array(1,2,3,4))) {
            // The "next" link.
            if ($totalCount > $page * $perPageCount) {
                $pagination .= '
    <li class="'.$classes['next'].'"><a href="'.$this->_buildUrl($page + 1, $pageName, $url, $queryArray).'">'.$texts['next'].'</a></li>';
            } else {
                if (in_array($displayFormat, array(1,3))) $pagination .= '
    <li class="'.$classes['nextGhost'].'">'.$texts['next'].'</li>';
            }
            // The "last" link.
            if ($totalPages != $page) {
                $pagination .= '
    <li class="'.$classes['last'].'"><a href="'.$this->_buildUrl($totalPages, $pageName, $url, $queryArray).'">'.$texts['last'].'</a></li>';
            } else {
                if (in_array($displayFormat, array(1,3))) $pagination .= '
    <li class="'.$classes['lastGhost'].'">'.$texts['last'].'</li>';
            }
        }
        
        $pagination .= '
</ul>';
        
        return $pagination;
    }
    
    protected function _buildUrl($subsequentPage, $pageName, $url, $queryArray)
    {
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