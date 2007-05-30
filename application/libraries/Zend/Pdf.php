<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Pdf_Exception */
require_once 'Zend/Pdf/Exception.php';

/** Zend_Pdf_Page */
require_once 'Zend/Pdf/Page.php';

/** Zend_Pdf_Cmap */
require_once 'Zend/Pdf/Cmap.php';

/** Zend_Pdf_Font */
require_once 'Zend/Pdf/Font.php';

/** Zend_Pdf_Style */
require_once 'Zend/Pdf/Style.php';

/** Zend_Pdf_Parser */
require_once 'Zend/Pdf/Parser.php';

/** Zend_Pdf_Trailer */
require_once 'Zend/Pdf/Trailer.php';

/** Zend_Pdf_Trailer_Keeper */
require_once 'Zend/Pdf/Trailer/Keeper.php';

/** Zend_Pdf_Trailer_Generator */
require_once 'Zend/Pdf/Trailer/Generator.php';

/** Zend_Pdf_Color */
require_once 'Zend/Pdf/Color.php';

/** Zend_Pdf_Color_GrayScale */
require_once 'Zend/Pdf/Color/GrayScale.php';

/** Zend_Pdf_Color_RGB */
require_once 'Zend/Pdf/Color/RGB.php';

/** Zend_Pdf_Color_CMYK */
require_once 'Zend/Pdf/Color/CMYK.php';

/** Zend_Pdf_Color_HTML */
require_once 'Zend/Pdf/Color/HTML.php';

/** Zend_Pdf_Image */
require_once 'Zend/Pdf/Resource/Image.php';

/** Zend_Pdf_Image */
require_once 'Zend/Pdf/Resource/ImageFactory.php';

/** Zend_Pdf_Image_JPEG */
require_once 'Zend/Pdf/Resource/Image/JPEG.php';

/** Zend_Pdf_Image_TIFF */
require_once 'Zend/Pdf/Resource/Image/TIFF.php';

/** Zend_Pdf_Image_PNG */
require_once 'Zend/Pdf/Resource/Image/PNG.php';


/**
 * General entity which describes PDF document.
 * It implements document abstraction with a document level operations.
 *
 * Class is used to create new PDF document or load existing document.
 * See details in a class constructor description
 *
 * Class agregates document level properties and entities (pages, bookmarks,
 * document level actions, attachments, form object, etc)
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf
{
  /**** Class Constants ****/
  
    /**
     * Version number of generated PDF documents.
     */
    const PDF_VERSION = 1.4;
    
    /**
     * PDF file header.
     */
    const PDF_HEADER  = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
     
  
  
    /**
     * Pages collection
     *
     * @todo implement it as a class, which supports ArrayAccess and Iterator interfaces,
     *       to provide incremental parsing and pages tree updating.
     *       That will give good performance and memory (PDF size) benefits.
     *
     * @var array   - array of Zend_Pdf_Page object
     */
    public $pages = array();

    /**
     * Document properties
     *
     * @var array
     */
    private $_properties = array();

    /**
     * Document level javascript
     *
     * @var string
     */
    private $_javaScript = null;

    /**
     * Document named actions
     * "GoTo..." actions, used to refer document parts
     * from outside PDF
     *
     * @var array   - array of Zend_Pdf_Action objects
     */
    private $_namedActions = array();


    /**
     * Pdf trailer (last or just created)
     *
     * @var Zend_Pdf_Trailer
     */
    private $_trailer = null;


    /**
     * PDF objects factory.
     *
     * @var Zend_Pdf_ElementFactory
     */
    private $_objFactory = null;


    /**
     * Create new PDF document from a $source string
     *
     * @param string $source
     * @param integer $revision
     * @return Zend_Pdf
     */
    static public function parse(&$source = null, $revision = null)
    {
        return new Zend_Pdf($source, $revision);
    }

    /**
     * Load PDF document from a file
     *
     * @param string $source
     * @param integer $revision
     * @return Zend_Pdf
     */
    static public function load($source = null, $revision = null)
    {
        return new Zend_Pdf($source, $revision, true);
    }

    /**
     * Render PDF document and save it.
     *
     * If $updateOnly is true, then it only appends new section to the end of file.
     *
     * @param string $filename
     * @param boolean $updateOnly
     * @throws Zend_Pdf_Exception
     */
    public function save($filename, $updateOnly = false)
    {
        if (($file = @fopen($filename, $updateOnly ? 'ab':'wb')) === false ) {
            throw new Zend_Pdf_Exception( "Can not open '$filename' file for writing." );
        }

        $pdfData = $this->render($updateOnly);

        while ( strlen($pdfData) > 0 && ($byteCount = fwrite($file, $pdfData)) != false ) {
            $pdfData = substr($pdfData, $byteCount);
        }

        fclose($file);
    }

    /**
     * Creates or loads PDF document.
     *
     * If $source is null, then it creates a new document.
     *
     * If $source is a string and $load is false, then it loads document
     * from a binary string.
     *
     * If $source is a string and $load is true, then it loads document
     * from a file.

     * $revision used to roll back document to specified version
     * (0 - currtent version, 1 - previous version, 2 - ...)
     *
     * @param string  $source - PDF file to load
     * @param integer $revision
     * @throws Zend_Pdf_Exception
     * @return Zend_Pdf
     */
    public function __construct(&$source = null, $revision = null, $load = false)
    {
        $this->_objFactory = new Zend_Pdf_ElementFactory(1);

        if ($source !== null) {
            $parser  = new Zend_Pdf_Parser($source, $this->_objFactory, $load);
            $this->_trailer = $parser->getTrailer();
            if ($revision !== null) {
                $this->rollback($revision);
            } else {
                $this->_loadPages($this->_trailer->Root->Pages);
            }
        } else {
            $trailerDictionary = new Zend_Pdf_Element_Dictionary();

            /**
             * Document id
             */
            $docId = md5(uniqid(rand(), true));   // 32 byte (128 bit) identifier
            $docIdLow  = substr($docId,  0, 16);  // first 16 bytes
            $docIdHigh = substr($docId, 16, 16);  // second 16 bytes

            $trailerDictionary->ID = new Zend_Pdf_Element_Array();
            $trailerDictionary->ID->items[] = new Zend_Pdf_Element_String_Binary($docIdLow);
            $trailerDictionary->ID->items[] = new Zend_Pdf_Element_String_Binary($docIdHigh);

            $trailerDictionary->Size = new Zend_Pdf_Element_Numeric(0);

            $this->_trailer    = new Zend_Pdf_Trailer_Generator($trailerDictionary);

            /**
             * Document catalog indirect object.
             */
            $docCatalog = $this->_objFactory->newObject(new Zend_Pdf_Element_Dictionary());
            $docCatalog->Type    = new Zend_Pdf_Element_Name('Catalog');
            $docCatalog->Version = new Zend_Pdf_Element_Name(Zend_Pdf::PDF_VERSION);
            $this->_trailer->Root = $docCatalog;

            /**
             * Pages container
             */
            $docPages = $this->_objFactory->newObject(new Zend_Pdf_Element_Dictionary());
            $docPages->Type  = new Zend_Pdf_Element_Name('Pages');
            $docPages->Kids  = new Zend_Pdf_Element_Array();
            $docPages->Count = new Zend_Pdf_Element_Numeric(0);
            $docCatalog->Pages = $docPages;
        }
    }

    /**
     * Retrive number of revisions.
     *
     * @return integer
     */
    public function revisions()
    {
        $revisions = 1;
        $currentTrailer = $this->_trailer;

        while ($currentTrailer->getPrev() !== null && $currentTrailer->getPrev()->Root !== null ) {
            $revisions++;
            $currentTrailer = $currentTrailer->getPrev();
        }

        return $revisions++;
    }

    /**
     * Rollback document $steps number of revisions.
     * This method must be invoked before any changes, applied to the document.
     * Otherwise behavior is undefined.
     *
     * @param integer $steps
     */
    public function rollback($steps)
    {
        for ($count = 0; $count < $steps; $count++) {
            if ($this->_trailer->getPrev() !== null && $this->_trailer->getPrev()->Root !== null) {
                $this->_trailer = $this->_trailer->getPrev();
            } else {
                break;
            }
        }
        $this->_objFactory->setObjectCount($this->_trailer->Size->value);

        // Mark content as modified to force new trailer generation at render time
        $this->_trailer->Root->touch();

        $this->pages = array();
        $this->_loadPages($this->_trailer->Root->Pages);
    }

    /**
     * Load pages recursively
     *
     * @param Zend_Pdf_Element_Reference $pages
     * @param array|null $attributes
     */
    private function _loadPages(Zend_Pdf_Element_Reference $pages, $attributes = null)
    {
        static $inheritable = array('Resources', 'MediaBox', 'CropBox', 'Rotate');

        if ($pages->getType() != Zend_Pdf_Element::TYPE_DICTIONARY) {
            throw new Zend_Pdf_Exception('Wrong argument');
        }

        if ($attributes === null) {
            $attributes = array();
        }
        foreach ($pages->getKeys() as $property) {
            if (in_array($property, $inheritable)) {
                $attributes[$property] = $pages->$property;
                $pages->$property = null;
            }
        }


        foreach ($pages->Kids->items as $child) {
            if ($child->Type->value == 'Pages') {
                $this->_loadPages($child, $attributes);
            } else if ($child->Type->value == 'Page') {
                foreach ($inheritable as $property) {
                    if ($child->$property === null && array_key_exists($property, $attributes)) {
                        /**
                         * Important note.
                         * If any attribute or dependant object is an indirect object, then it's still
                         * shared between pages.
                         */
                        if ($attributes[$property] instanceof Zend_Pdf_Element_Object) {
                            $child->$property = $attributes[$property];
                        } else {
                            $child->$property = $this->_objFactory->newObject($attributes[$property]);
                        }
                    }
                }
                $this->pages[] = new Zend_Pdf_Page($child, $this->_objFactory);
            }
        }
    }


    /**
     * Orginize pages to tha pages tree structure.
     *
     * @todo atomatically attach page to the document, if it's not done yet.
     * @todo check, that page is attached to the current document
     *
     * @todo Dump pages as a balanced tree instead of a plain set.
     */
    private function _dumpPages()
    {
        $pagesContainer = $this->_trailer->Root->Pages;
        $pagesContainer->touch();
        $pagesContainer->Kids->items->clear();

        foreach ($this->pages as $page ) {
            $page->render($this->_objFactory);

            $pageDictionary = $page->getPageDictionary();
            $pageDictionary->touch();
            $pageDictionary->Parent = $pagesContainer;

            $pagesContainer->Kids->items[] = $pageDictionary;
        }

        $pagesContainer->Count->touch();
        $pagesContainer->Count->value = count($this->pages);
    }


    /**
     * Create page object, attached to the PDF document.
     * Method signatures:
     *
     * 1. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new Zend_Pdf_Page(string $pagesize);
     * ---------------------------------------------------------
     *
     * 2. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new Zend_Pdf_Page(numeric $width, numeric $height);
     * ---------------------------------------------------------
     *
     * @param mixed $param1
     * @param mixed $param2
     * @return Zend_Pdf_Page
     */
    public function newPage($param1, $param2 = null)
    {
        if ($param2 === null) {
            return new Zend_Pdf_Page($param1, $this->_objFactory);
        } else {
            return new Zend_Pdf_Page($param1, $param2, $this->_objFactory);
        }
    }

    /**
     * Return return the an associative array with PDF meta information, values may
     * be string, boolean or float.
     * Returned array could be used directly to access, add, modify or remove
     * document properties.
     *
     * Standard document properties: Title (must be set for PDF/X documents), Author,
     * Subject, Keywords (comma separated list), Creator (the name of the application,
     * that created document, if it was converted from other format), Trapped (must be
     * true, false or null, can not be null for PDF/X documents)
     *
     * @return array
     */
    public function properties()
    {
        return $this->_properties;
    }


    /**
     * Return the document-level JavaScript
     * or null if there is no JavaScript for this document
     *
     * @return string
     */
    public function getJavaScript()
    {
        return $this->_javaScript;
    }


    /**
     * Return an associative array containing all the named actions in the PDF.
     * Named actions (it's always "GoTo" actions) can be used to reference from outside
     * the PDF, ex: 'http://www.something.com/mydocument.pdf#MyAction'
     *
     * @return array
     */
    public function getNamedActions()
    {
        return $this->_namedActions;
    }


    /**
     * Render the completed PDF to a string.
     * If $newSegmentOnly is true, then only appended part of PDF is returned.
     *
     * @param boolean $newSegmentOnly
     * @return string
     */
    public function render($newSegmentOnly = false)
    {
        $this->_dumpPages();

        // Check, that PDF file was modified
        // File is always modified by _dumpPages() now, but future implementations may eliminate this.
        if (!$this->_objFactory->isModified()) {
            if ($newSegmentOnly) {
                return '';
            } else {
                return $this->_trailer->getPDFString();
            }
        }

        // offset (from a start of PDF file) of new PDF file segment
        $segmentOffset = $this->_trailer->getPDFLength();
        // new PDF file segment itself
        $pdfSegment = '';
        // Last Object number in a list of free objects
        $lastFreeObject = $this->_trailer->getLastFreeObject();

        // Array of cross-reference table subsections
        $xrefTable = array();
        // Object numbers of first objects in each subsection
        $xrefSectionStartNums = array();

        // Last cross-reference table subsection
        $xrefSection = array();
        // Dummy initialization of the first element (specail case - header of linked list of free objects).
        $xrefSection[] = 0;
        $xrefSectionStartNums[] = 0;
        // Object number of last processed PDF object.
        // Used to manage cross-reference subsections.
        // Initialized by zero (specail case - header of linked list of free objects).
        $lastObjNum = 0;

        // Iterate objects to create new reference table
        foreach ($this->_objFactory->listModifiedObjects() as $updateInfo) {
            $objNum = $updateInfo->getObjNum();

            if ($objNum - $lastObjNum != 1) {
                // Save cross-reference table subsection and start new one
                $xrefTable[] = $xrefSection;
                $xrefSection = array();
                $xrefSectionStartNums[] = $objNum;
            }

            if ($updateInfo->isFree()) {
                // Free object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d f \n", $lastFreeObject, $updateInfo->getGenNum());
                $lastFreeObject = $objNum;
            } else {
                // In-use object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d n \n", $segmentOffset + strlen($pdfSegment), $updateInfo->getGenNum());
                $pdfSegment .= $updateInfo->getObjectDump();
            }
            $lastObjNum = $objNum;
        }
        // Save last cross-reference table subsection
        $xrefTable[] = $xrefSection;

        // Modify first entry (specail case - header of linked list of free objects).
        $xrefTable[0][0] = sprintf("%010d 65535 f \n", $lastFreeObject);

        $xrefTableStr = "xref\n";
        foreach ($xrefTable as $sectId => $xrefSection) {
            $xrefTableStr .= sprintf("%d %d \n", $xrefSectionStartNums[$sectId], count($xrefSection));
            foreach ($xrefSection as $xrefTableEntry) {
                $xrefTableStr .= $xrefTableEntry;
            }
        }

        $xrefStartOffset = $segmentOffset + strlen($pdfSegment);
        $this->_trailer->Size->value = $this->_objFactory->getObjectCount();

        $pdfSegment .= $xrefTableStr
                    .  $this->_trailer->toString()
                    . "startxref\n" . $xrefStartOffset . "\n"
                    . "%%EOF\n";

        if ($newSegmentOnly) {
            return $pdfSegment;
        } else {
            return $this->_trailer->getPDFString() . $pdfSegment;
        }
    }


    /**
     * Set the document-level JavaScript
     *
     * @param string $javascript
     */
    public function setJavaScript($javascript)
    {
        $this->_javaScript = $javascript;
    }
    
    
    /**
     * Convert date to PDF format (it's close to ASN.1 (Abstract Syntax Notation
     * One) defined in ISO/IEC 8824).
     *
     * @todo This really isn't the best location for this method. It should
     *   probably actually exist as Zend_Pdf_Element_Date or something like that.
     *
     * @todo Address the following E_STRICT issue:
     *   PHP Strict Standards:  date(): It is not safe to rely on the system's
     *   timezone settings. Please use the date.timezone setting, the TZ
     *   environment variable or the date_default_timezone_set() function. In
     *   case you used any of those methods and you are still getting this
     *   warning, you most likely misspelled the timezone identifier.
     *
     * @param integer $timestamp (optional) If omitted, uses the current time.
     * @return string
     */
    public static function pdfDate($timestamp = null)
    {
        if (is_null($timestamp)) {
            $date = date('\D\:YmdHisO');
        } else {
            $date = date('\D\:YmdHisO', $timestamp);
        }
        return substr_replace($date, '\'', -2, 0) . '\'';
    }
     
}
