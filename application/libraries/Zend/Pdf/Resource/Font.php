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
 * @package    Zend_Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_Resource */
require_once 'Zend/Pdf/Resource.php';

/** Zend_Pdf_Exception */
require_once 'Zend/Pdf/Exception.php';

/** Zend_Pdf_Cmap */
require_once 'Zend/Pdf/Cmap.php';


/**
 * Abstract class which manages PDF fonts.
 *
 * Defines the public interface and creates shared storage for concrete
 * subclasses which are responsible for generating the font's information
 * dictionaries, mapping characters to glyphs, and providing both overall font
 * and glyph-specific metric data.
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link Zend_Pdf_Font::fontWithName} and {@link Zend_Pdf_Font::fontWithPath}.
 *
 * @package    Zend_Pdf
 * @subpackage Fonts
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Resource_Font extends Zend_Pdf_Resource
{
  /**** Instance Variables ****/


    /**
     * Object representing the font's cmap (character to glyph map).
     * @var Zend_Pdf_Cmap
     */
    public $cmap = null;

    /**
     * The type of font. Use TYPE_ constants defined in {@link Zend_Pdf_Font}.
     * @var integer
     */
    protected $_fontType = Zend_Pdf_Font::TYPE_UNKNOWN;

    /**
     * Array containing descriptive names for the font. See {@link fontName()}.
     * @var array
     */
    protected $_fontNames = array();

    /**
     * Flag indicating whether or not this font is bold.
     * @var boolean
     */
    protected $_isBold = false;

    /**
     * Flag indicating whether or not this font is italic.
     * @var boolean
     */
    protected $_isItalic = false;

    /**
     * Flag indicating whether or not this font is monospaced.
     * @var boolean
     */
    protected $_isMonospace = false;

    /**
     * The position below the text baseline of the underline (in glyph units).
     * @var integer
     */
    protected $_underlinePosition = 0;

    /**
     * The thickness of the underline (in glyph units).
     * @var integer
     */
    protected $_underlineThickness = 0;

    /**
     * The position above the text baseline of the strikethrough (in glyph units).
     * @var integer
     */
    protected $_strikePosition = 0;

    /**
     * The thickness of the strikethrough (in glyph units).
     * @var integer
     */
    protected $_strikeThickness = 0;

    /**
     * Number of glyph units per em. See {@link getUnitsPerEm()}.
     * @var integer
     */
    protected $_unitsPerEm = 0;

    /**
     * Typographical ascent. See {@link getAscent()}.
     * @var integer
     */
    protected $_ascent = 0;

    /**
     * Typographical descent. See {@link getDescent()}.
     * @var integer
     */
    protected $_descent = 0;

    /**
     * Typographical line gap. See {@link getLineGap()}.
     * @var integer
     */
    protected $_lineGap = 0;

    /**
     * Array containing the widths of each of the glyphs contained in the font.
     * @var array
     */
    protected $_glyphWidths = null;

    /**
     * The highest integer index in the glyph widths array.
     * @var integer
     */
    protected $_glyphMaxIndex = 0;

    /**
     * Font embedding options. See discussion in {@link __construct()}.
     * @var integer
     */
    private $_embeddingOptions = 0;



  /**** Public Interface ****/


  /* Object Lifecycle */

    /**
     * Object constructor.
     *
     * The $embeddingOptions parameter allows you to set certain flags related
     * to font embedding. You may combine options by OR-ing them together. See
     * the EMBED_ constants defined in {@link Zend_Pdf_Font} for the list of
     * available options and their descriptions.
     *
     * @param integer $embeddingOptions (optional) Options for font embedding.
     *   Only used for certain font types.
     */
    public function __construct($embeddingOptions = 0)
    {
        parent::__construct(new Zend_Pdf_Element_Dictionary());
        $this->_resource->Type = new Zend_Pdf_Element_Name('Font');
        $this->_embeddingOptions = $embeddingOptions;
    }


  /* Object Magic Methods */

    /**
     * Returns the full name of the font in the encoding method of the current
     * locale. Transliterates any characters that cannot be naturally
     * represented in that character set.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFontName(Zend_Pdf_Font::NAME_FULL, '', '//TRANSLIT');
    }


  /* Accessors */

    /**
     * Returns the type of font.
     *
     * @return integer One of the TYPE_ constants defined in
     *   {@link Zend_Pdf_Font}.
     */
    public function getFontType()
    {
        return $this->_fontType;
    }

    /**
     * Returns the specified descriptive name for the font.
     *
     * The font name type is usually one of the following:
     * <ul>
     *  <li>{@link Zend_Pdf_Font::NAME_FULL}
     *  <li>{@link Zend_Pdf_Font::NAME_FAMILY}
     *  <li>{@link Zend_Pdf_Font::NAME_PREFERRED_FAMILY}
     *  <li>{@link Zend_Pdf_Font::NAME_STYLE}
     *  <li>{@link Zend_Pdf_Font::NAME_PREFERRED_STYLE}
     *  <li>{@link Zend_Pdf_Font::NAME_DESCRIPTION}
     *  <li>{@link Zend_Pdf_Font::NAME_SAMPLETEXT}
     *  <li>{@link Zend_Pdf_Font::NAME_ID}
     *  <li>{@link Zend_Pdf_Font::NAME_VERSION}
     *  <li>{@link Zend_Pdf_Font::NAME_POSTSCRIPT}
     *  <li>{@link Zend_Pdf_Font::NAME_CID_NAME}
     *  <li>{@link Zend_Pdf_Font::NAME_DESIGNER}
     *  <li>{@link Zend_Pdf_Font::NAME_DESIGNER_URL}
     *  <li>{@link Zend_Pdf_Font::NAME_MANUFACTURER}
     *  <li>{@link Zend_Pdf_Font::NAME_VENDOR_URL}
     *  <li>{@link Zend_Pdf_Font::NAME_COPYRIGHT}
     *  <li>{@link Zend_Pdf_Font::NAME_TRADEMARK}
     *  <li>{@link Zend_Pdf_Font::NAME_LICENSE}
     *  <li>{@link Zend_Pdf_Font::NAME_LICENSE_URL}
     * </ul>
     *
     * Note that not all names are available for all fonts. In addition, some
     * fonts may contain additional names, whose indicies are in the range
     * 256 to 32767 inclusive, which are used for certain font layout features.
     *
     * If the preferred language translation is not available, uses the first
     * available translation for the name, which is usually English.
     *
     * If the requested name does not exist, returns null.
     *
     * All names are stored internally as Unicode strings, using UTF-16BE
     * encoding. You may optionally supply a different resulting character set.
     *
     * @param integer $nameType Type of name requested.
     * @param mixed $language Preferred language (string) or array of languages
     *   in preferred order. Use the ISO 639 standard 2-letter language codes.
     * @param string $characterSet (optional) Desired resulting character set.
     *   You may use any character set supported by {@link iconv()};
     * @return string
     */
    public function getFontName($nameType, $language, $characterSet = null)
    {
        if (! isset($this->_fontNames[$nameType])) {
            return null;
        }
        $name = null;
        if (is_array($language)) {
            foreach ($language as $code) {
                if (isset($this->_fontNames[$nameType][$code])) {
                    $name = $this->_fontNames[$nameType][$code];
                    break;
                }
            }
        } else {
            if (isset($this->_fontNames[$nameType][$language])) {
                $name = $this->_fontNames[$nameType][$language];
            }
        }
        /* If the preferred language could not be found, use whatever is first.
         */
        if (is_null($name)) {
            $name = reset($this->_fontNames[$nameType]);
        }
        /* Convert the character set if requested.
         */
        if ((! is_null($characterSet)) && ($characterSet != 'UTF-16BE')) {
            $name = iconv('UTF-16BE', $characterSet, $name);
        }
        return $name;
    }

    /**
     * Returns the suggested position below the text baseline of the underline
     * in glyph units.
     *
     * This value is usually negative.
     *
     * @return integer
     */
    public function getUnderlinePosition()
    {
        return $this->_underlinePosition;
    }

    /**
     * Returns the suggested line thickness of the underline in glyph units.
     *
     * @return integer
     */
    public function getUnderlineThickness()
    {
        return $this->_underlineThickness;
    }

    /**
     * Returns the suggested position above the text baseline of the
     * strikethrough in glyph units.
     *
     * @return integer
     */
    public function getStrikePosition()
    {
        return $this->_strikePosition;
    }

    /**
     * Returns the suggested line thickness of the strikethrough in glyph units.
     *
     * @return integer
     */
    public function getStrikeThickness()
    {
        return $this->_strikeThickness;
    }

    /**
     * Returns the number of glyph units per em.
     *
     * Used to convert glyph space to user space. Frequently used in conjunction
     * with {@link widthsForGlyphs()} to calculate the with of a run of text.
     *
     * @return integer
     */
    public function getUnitsPerEm()
    {
        return $this->_unitsPerEm;
    }

    /**
     * Returns the typographic ascent in font glyph units.
     *
     * The typographic ascent is the distance from the font's baseline to the
     * top of the text frame. It is frequently used to locate the initial
     * baseline for a paragraph of text inside a given rectangle.
     *
     * @return integer
     */
    public function getAscent()
    {
        return $this->_ascent;
    }

    /**
     * Returns the typographic descent in font glyph units.
     *
     * The typographic descent is the distance below the font's baseline to the
     * bottom of the text frame. It is always negative.
     *
     * @return integer
     */
    public function getDescent()
    {
        return $this->_descent;
    }

    /**
     * Returns the typographic line gap in font glyph units.
     *
     * The typographic line gap is the distance between the bottom of the text
     * frame of one line to the top of the text frame of the next. It is
     * typically combined with the typographical ascent and descent to determine
     * the font's total line height (or leading).
     *
     * @return integer
     */
    public function getLineGap()
    {
        return $this->_lineGap;
    }

    /**
     * Returns the suggested line height (or leading) in font glyph units.
     *
     * This value is determined by adding together the values of the typographic
     * ascent, descent, and line gap. This value yields the suggested line
     * spacing as determined by the font developer.
     *
     * It should be noted that this is only a guideline; layout engines will
     * frequently modify this value to achieve special effects such as double-
     * spacing.
     *
     * @return integer
     */
    public function getLineHeight()
    {
        return $this->_ascent - $this->_descent + $this->_lineGap;
    }


  /* Information and Conversion Methods */

    /**
     * Returns a number between 0 and 1 inclusive that indicates the percentage
     * of characters in the string which are covered by glyphs in this font.
     *
     * Since no one font will contain glyphs for the entire Unicode character
     * range, this method can be used to help locate a suitable font when the
     * actual contents of the string are not known.
     *
     * Note that some fonts lie about the characters they support. Additionally,
     * fonts don't usually contain glyphs for control characters such as tabs
     * and line breaks, so it is rare that you will get back a full 1.0 score.
     * The resulting value should be considered informational only.
     *
     * @param string $string
     * @param string $charEncoding (optional) Character encoding of source text.
     *   If omitted, uses 'current locale'.
     * @return float
     */
    public function getCoveredPercentage($string, $charEncoding = '')
    {
        /* Convert the string to UTF-16BE encoding so we can match the string's
         * character codes to those found in the cmap.
         */
        if ($charEncoding != 'UTF-16BE') {
            $string = iconv($charEncoding, 'UTF-16BE', $string);
        }

        $charCount = iconv_strlen($string, 'UTF-16BE');
        if ($charCount == 0) {
            return 0;
        }

        /* Fetch the covered character code list from the font's cmap.
         */
        $coveredCharacters = $this->cmap->getCoveredCharacters();

        /* Calculate the score by doing a lookup for each character.
         */
        $score = 0;
        $maxIndex = strlen($string);
        for ($i = 0; $i < $maxIndex; $i++) {
            /**
             * @todo Properly handle characters encoded as surrogate pairs.
             */
            $charCode = (ord($string[$i]) << 8) | ord($string[++$i]);
            /* This could probably be optimized a bit with a binary search...
             */
            if (in_array($charCode, $coveredCharacters)) {
                $score++;
            }
        }
        return $score / $charCount;
    }

    /**
     * Returns the widths of the glyphs.
     *
     * The widths are expressed in the font's glyph space. You are responsible
     * for converting to user space as necessary. See {$link unitsPerEm()}.
     *
     * Throws an exception if the glyph number is out of range.
     *
     * See also {@link widthForGlyph()}.
     *
     * @param array &$glyphNumbers Array of glyph numbers.
     * @return array Array of glyph widths (integers).
     * @throws Zend_Pdf_Exception
     */
    public function widthsForGlyphs(&$glyphNumbers)
    {
        $widths = array();
        foreach ($glyphNumbers as $key => $glyphNumber) {
            if (($glyphNumber < 0) || ($glyphNumber > $this->_glyphMaxIndex)) {
                throw new Zend_Pdf_Exception("Glyph number is out of range: $glyphNumber",
                                             Zend_Pdf_Exception::GLYPH_OUT_OF_RANGE);
            }
            $widths[$key] = $this->_glyphWidths[$glyphNumber];
        }
        return $widths;
    }

    /**
     * Returns the width of the glyph.
     *
     * Like {@link widthsForGlyphs()} but used for one glyph at a time.
     *
     * @param integer $glyphNumber
     * @return integer
     * @throws Zend_Pdf_Exception
     */
    public function widthForGlyph($glyphNumber)
    {
        if (($glyphNumber < 0) || ($glyphNumber > $this->_glyphMaxIndex)) {
            throw new Zend_Pdf_Exception("Glyph number is out of range: $glyphNumber",
                                             Zend_Pdf_Exception::GLYPH_OUT_OF_RANGE);
        }
        return $this->_glyphWidths[$glyphNumber];
    }

    /**
     * Convert string from local encoding to Windows ANSI encoding.
     *
     * NOTE: This method may disappear in a future revision of the framework
     * once the font subsetting and Unicode support code is complete. At that
     * point, there may be multiple ways of encoding strings depending on
     * intended usage. You should treat this method as framework internal
     * use only.
     *
     * @param string $string
     * @param string $charEncoding Character encoding of source text.
     * @return string
     */
    public function encodeString($string, $charEncoding)
    {
        /* The $charEncoding paramater will go away once the remainder of the
         * layout code is completed. At that point, all strings will be handled
         * internally as UTF-16BE-encoded...
         */
        return iconv($charEncoding, 'CP1252//IGNORE', $string);
    }

    /**
     * Convert string from Windows ANSI encoding to local encoding.
     *
     * NOTE: This method may disappear in a future revision of the framework
     * once the font subsetting and Unicode support code is complete. At that
     * point, there may be multiple ways of encoding strings depending on
     * intended usage. You should treat this method as framework internal
     * use only.
     *
     * @param string $string
     * @param string $charEncoding Character encoding of resulting text.
     * @return string
     */
    public function decodeString($string, $charEncoding)
    {
        return iconv('CP1252', $charEncoding, $string);
    }



  /**** Internal Methods ****/


    /**
     * If the font's glyph space is not 1000 units per em, converts the value.
     *
     * @param integer $value
     * @return integer
     */
    protected function _toEmSpace($value)
    {
        if ($this->_unitsPerEm == 1000) {
            return $value;
        }
        return ceil(($value / $this->_unitsPerEm) * 1000);    // always round up
    }

    /**
     * Returns true if the embedding option has been set for this font.
     *
     * The embedding options are stored as a bitfield. Multiple options may be
     * set at the same time.
     *
     * This is only used by certain subclasses.
     *
     * @param integer $embeddingOption
     * @return boolean
     */
    protected function _isEmbeddingOptionSet($embeddingOption)
    {
        $isSet = (($this->_embeddingOptions & $embeddingOption) == $embeddingOption);
        return $isSet;
    }

}

