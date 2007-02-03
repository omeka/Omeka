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
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Service_Rest
 */
require_once 'Zend/Service/Rest.php';

/**
 * Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * Zend_Service_Amazon_Accessories
 */
require_once 'Zend/Service/Amazon/Accessories.php';

/**
 * Zend_Service_Amazon_CustomerReview
 */
require_once 'Zend/Service/Amazon/CustomerReview.php';

/**
 * Zend_Service_Amazon_EditorialReview
 */
require_once 'Zend/Service/Amazon/EditorialReview.php';

/**
 * Zend_Service_Amazon_Image
 */
require_once 'Zend/Service/Amazon/Image.php';

/**
 * Zend_Service_Amazon_Item
 */
require_once 'Zend/Service/Amazon/Item.php';

/**
 * Zend_Service_Amazon_ListmaniaList
 */
require_once 'Zend/Service/Amazon/ListmaniaList.php';

/**
 * Zend_Service_Amazon_Offer
 */
require_once 'Zend/Service/Amazon/Offer.php';

/**
 * Zend_Service_Amazon_OfferSet
 */
require_once 'Zend/Service/Amazon/OfferSet.php';

/**
 * Zend_Service_Amazon_ResultSet
 */
require_once 'Zend/Service/Amazon/ResultSet.php';

/**
 * Zend_Service_Amazon_SimilarProduct
 */
require_once 'Zend/Service/Amazon/SimilarProduct.php';

/**
 * Zend_Filter
 */
require_once 'Zend/Filter.php';


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon
{
	public $appId;

	static protected $_sortVerbs = array('relevance', 'salesrank', 'price', '-price',
	                                     'most-recent', '-date', 'title', '-title',
	                                     'sale-flag', 'featured', 'review', 'artist',
	                                     'age-min', '-age-min');

	static protected $_searchSort = array(
      'Apparel' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => '-launch-date',
          'sale-flag'   => 'sale-flag'),
      'Baby' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'Beauty' => array(
          'featured'    => 'pmrank',
          'sales'       => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'most-recent' => '-launchdate',
          'sale-flag'   => 'sale-flag'),
      'Blended' => array(),
      'Books' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'review'      => 'reviewrank',
          'price'       => 'pricerank',
          '-price'      => 'inverse-pricerank',
          'most-recent' => 'daterank',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Classical' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank',
          'most-recent' => 'orig-rel-date'),
      'DigitalMusic' => array(
          'title'       => 'songtitlerank',
          'most-recent' => 'uploaddaterank'),
      'DVD' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          'most-recent' => '-video-release-date'),
      'Electronics' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'review'      => 'reviewrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'ForeignBooks' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'review'      => 'reviewrank',
          'price'       => 'pricerank',
          '-price'      => 'inverse-pricerank',
          'most-recent' => 'daterank',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'GourmetFood' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => 'launch-date',
          'sale-flag'   => 'sale-flag'),
      'HealthPersonalCare' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => 'launch-date',
          'sale-flag'   => 'sale-flag'),
      'HomeGarden' => array(),
      'Jewelry' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => 'launch-date'),
      'Kitchen' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Magazines' => array(
          'salesrank'   => 'subslot-salesrank',
          'review'      => 'reviewrank',
          'price'       => 'price',
          '-price'      => '-price',
          'most-recent' => 'daterank',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Merchants' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => '-launch-date',
          'sale-flag'   => 'sale-flag'),
      'Miscellaneous' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Music' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank',
          'artist'      => 'artistrank',
          'most-recent' => 'orig-rel-date'),
      'MusicalInstruments' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'most-recent' => '-launch-date',
          'sale-flag'   => 'sale-flag'),
      'MusicTracks' => array(
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'OfficeProducts' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'review'      => 'reviewrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'OutdoorLiving' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'PCHardware' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'PetSupplies' => array(
          'featured'    => '+pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Photo' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Restaurants' => array(
          'relevance'   => 'relevance',
          'title'       => 'titlerank'),
      'Software' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'SoftwareVideoGames' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank'),
      'SportingGoods' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'pricerank',
          '-price'      => 'inverseprice',
          'most-recent' => 'launch-date',
          'sale-flag'   => 'sale-flag'),
      'Tools' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'Toys' => array(
          'featured'    => 'pmrank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          '-age-min'    => '-age-min'),
      'VHS' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          'most-recent' => '-video-release-date'),
      'Video' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          'most-recent' => '-video-release-date'),
      'VideoGames' => array(
          'relevance'   => 'relevancerank',
          'salesrank'   => 'salesrank',
          'price'       => 'price',
          '-price'      => '-price',
          'title'       => 'titlerank',
          'most-recent' => '-video-release-date'),
      'Wireless' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'),
      'WirelessAccessories' => array(
          'featured'    => 'psrank',
          'salesrank'   => 'salesrank',
          'title'       => 'titlerank',
          '-title'      => '-titlerank'));

	protected static $_searchParams = array(
    	'Apparel' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'Baby' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'Title'),
      	'Beauty' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'Title'),
      	'Blended' => array(
			'Keywords'),
      	'Books' => array(
      		'Author',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Power',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'Classical' => array(
      		'Artist',
      		'BrowseNode',
      		'Composer',
      		'Condition',
      		'Conductor',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MusicLabel',
      		'Orchestra',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'DVD' => array(
      		'Actor',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'Director',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MPAARating',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'DigitalMusic' => array(
      		'Actor',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'Director',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MPAARating',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'Electronics' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'GourmetFood' => array(
      		'Actor',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'Director',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MPAARating',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'HealthPersonalCare' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'Jewelry' => array(
      		'Actor',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'Director',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MPAARating',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'Kitchen' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'Magazines' => array(
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Publisher',
      		'Sort',
      		'Title'),
      	'Merchants' => array(
      		'Keywords',
      		'Title',
      		'Power',
      		'BrowseNode',
      		'Artist',
      		'Author',
      		'Actor',
      		'Director',
      		'AudienceRating',
      		'Manufacturer',
      		'MusicLabel',
      		'Composer',
      		'Publisher',
      		'Brand',
      		'Conductor',
      		'Orchestra',
      		'TextStream',
      		'Cuisine',
      		'City',
      		'Neighborhood'),
      	'Miscellaneous' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'Title'),
      	'Music' => array(
      		'Artist',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MusicLabel',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'MusicTracks' => array(
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort'),
      	'MusicalInstruments' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'OfficeProducts' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'OutdoorLiving' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'PCHardware' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'PetSupplies' => array(
      		'Keywords',
      		'Title',
      		'BrowseNode',
      		'Author',
      		'Manufacturer',
      		'Brand',
      		'Count',
      		'ItemPage',
      		'Sort',
      		'MinimumPrice',
      		'MaximumPrice',
      		'MerchantId',
      		'Condition',
      		'DeliveryMethod'),
      	'Photo' => array(
	      	'Brand',
	      	'BrowseNode',
	      	'Condition',
	      	'DeliveryMethod',
	      	'ISPUPostalCode',
	      	'Manufacturer',
	      	'MaximumPrice',
	      	'MerchantId',
	      	'MinimumPrice',
	      	'ItemPage',
	      	'Keywords',
	      	'Sort',
	      	'TextStream',
	      	'Title'),
      	'Restaurants' => array(
    	  	'BrowseNode',
    	  	'City',
    	  	'Condition',
    	  	'Cuisine',
    	  	'ItemPage',
    	  	'Keywords',
    	  	'MaximumPrice',
    	  	'MerchantId',
    	  	'MinimumPrice',
    	  	'Neighborhood',
    	  	'Sort',
    	  	'Title'),
      	'Software' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'ItemPage',
      		'Keywords',
      		'Sort',
      		'Title'),
      	'SportingGoods' => array(
	      	'Brand',
	      	'BrowseNode',
	      	'Condition',
	      	'DeliveryMethod',
	      	'ISPUPostalCode',
	      	'ItemPage',
	      	'Keywords',
	      	'Manufacturer',
	      	'MaximumPrice',
	      	'MerchantId',
	      	'MinimumPrice',
	      	'Sort',
	      	'Title'),
    	'Tools' => array(
      		'Brand',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'Manufacturer',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'Title'),
      	'Toys' => array(
	      	'BrowseNode',
	      	'Condition',
	      	'DeliveryMethod',
	      	'ISPUPostalCode',
	      	'ItemPage',
	      	'Keywords',
	      	'MaximumPrice',
	      	'MerchantId',
	      	'MinimumPrice',
	      	'Sort',
	      	'TextStream',
	      	'Title'),
      	'VHS' => array(
    	  	'Actor',
    	  	'BrowseNode',
    	  	'Condition',
    	  	'DeliveryMethod',
    	  	'Director',
    	  	'ISPUPostalCode',
    	  	'ItemPage',
    	  	'Keywords',
    	  	'MaximumPrice',
    	  	'MerchantId',
    	  	'MinimumPrice',
    	  	'MPAARating',
    	  	'Publisher',
    	  	'Sort',
    	  	'Title'),
      	'Video' => array(
      		'Actor',
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'Director',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'MPAARating',
      		'Publisher',
      		'Sort',
      		'TextStream',
      		'Title'),
      	'VideoGames' => array(
	      	'Brand',
	      	'BrowseNode',
	      	'Condition',
	      	'DeliveryMethod',
	      	'ISPUPostalCode',
	      	'ItemPage',
	      	'Keywords',
	      	'Manufacturer',
	      	'MaximumPrice',
	      	'MerchantId',
	      	'MinimumPrice',
	      	'Sort',
	      	'TextStream',
	      	'Title'),
      	'Wireless' => array(
    	  	'BrowseNode',
    	  	'Condition',
    	  	'DeliveryMethod',
    	  	'ISPUPostalCode',
    	  	'ItemPage',
    	  	'Keywords',
    	  	'MaximumPrice',
    	  	'MerchantId',
    	  	'MinimumPrice',
    	  	'Sort',
    	  	'Title'),
      	'WirelessAccessories' => array(
      		'BrowseNode',
      		'Condition',
      		'DeliveryMethod',
      		'ISPUPostalCode',
      		'ItemPage',
      		'Keywords',
      		'MaximumPrice',
      		'MerchantId',
      		'MinimumPrice',
      		'Sort',
      		'Title'));

    protected $_baseUri = 'http://webservices.amazon.com';

    protected $_baseUriList = array('US' => 'http://webservices.amazon.com',
                                    'UK' => 'http://webservices.amazon.co.uk',
                                    'DE' => 'http://webservices.amazon.de',
                                    'JP' => 'http://webservices.amazon.co.jp',
                                    'FR' => 'http://webservices.amazon.fr',
                                    'CA' => 'http://webservices.amazon.ca');

    /**
     * Zend_Service_Rest Object
     *
     * @var Zend_Service_Rest
     */
    protected $_rest;

	/**
     * Constructs a new Amazon Web Services Client
     *
     * @param string $appId Developer's Amazon appid
     * @param string $countryCode Country code for Amazon service to connect to.
     * Defaults to US, can be US, UK, DE, JP, FR, CA
     * @throws Zend_Service_Exception
     * @return Zend_Service_Amazon
     */
    public function __construct($appId, $countryCode = 'US')
    {
        $this->appId = $appId;

        if (!isset($this->_baseUriList[$countryCode])) {
            throw new Zend_Service_Exception("Amazon Web Service: Unknown country code: $countryCode");
        }

        $this->_array = array();
        $this->_rest = new Zend_Service_Rest();
        $this->_rest->setUri($this->_baseUriList[$countryCode]);
    }


    /**
     * Search for Items
     *
     * @param array $options Options to use for the Search Query
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2005-10-05&p=ApiReference/ItemSearchOperation
     * @throws Zend_Service_Exception
     * @return Zend_Service_Amazon_ResultSet
     */
    public function itemSearch($options)
    {
        $defaultOptions = array('ResponseGroup' => 'Small');
        $options = $this->_prepareOptions('ItemSearch', $options, $defaultOptions);
        $this->_validateItemSearch($options);
        $response = $this->_rest->restGet('/onca/xml', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: '
        	                               . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);

        return new Zend_Service_Amazon_ResultSet($dom);
    }


    /**
     * Look up for a Single Item
     *
     * @param string $asin Amazon ASIN ID
     * @param array $options Query Options
     * @see http://www.amazon.com/gp/aws/sdk/main.html/102-9041115-9057709?s=AWSEcommerceService&v=2005-10-05&p=ApiReference/ItemLookupOperation
     * @throws Zend_Service_Exception
     * @return Zend_Service_Amazon_Item|Zend_Service_Amazon_ResultSet|null
     */
    public function itemLookup($asin, $options = null)
    {
        if (!$options) {
            $options = array();
        }

        $defaultOptions = array('IdType' => 'ASIN', 'ResponseGroup' => 'Small');
        $options['ItemId'] = $asin;
        $options = $this->_prepareOptions('ItemLookup', $options, $defaultOptions);
        $this->_validateItemLookup($options);
        $response = $this->_rest->restGet('/onca/xml', $options);

        if ($response->isError()) {
        	throw new Zend_Service_Exception('An error occurred sending request. Status code: '
        	                               . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::_checkErrors($dom);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
        $items = $xpath->query('//az:Items/az:Item');

        if ($items->length == 1) {
          return new Zend_Service_Amazon_Item($items->item(0));
        } elseif ($items->length > 1) {
        	return new Zend_Service_Amazon_ResultSet($items);
        }

        return null;
    }


    /** @todo docblock */
    protected function _validateItemSearch($options = array())
    {
        if (!is_array($options)) {
            throw new Zend_Service_Exception('Options must be specified as an array');
        }

        // Validate keys in the $options array
        $this->_compareOptions($options, array('Marketplace',
                                               'AssociateTag',
                                               'SearchIndex',
                                               'Keywords',
                                               'Title',
                                               'Power',
                                               'BrowseNode',
                                               'Author',
                                               'Actor',
                                               'Director',
                                               'AudienceRating',
                                               'Manufacturer',
                                               'MusicLabel',
                                               'Composer',
                                               'Publisher',
                                               'Brand',
                                               'Conductor',
                                               'Orchestra',
                                               'TextStream',
                                               'ItemPage',
                                               'Sort',
                                               'City',
                                               'Cuisine',
                                               'Neighborhood',
                                               'MinimumPrice',
                                               'MaximumPrice',
                                               'MerchantId',
                                               'Condition',
                                               'DeliveryMethod',
                                               'ResponseGroup',
                                               'Service',
                                               'SubscriptionId',
                                               'Operation'));

        // Validate SearchIndex (required)
        if (empty($options['SearchIndex'])) {
            throw new Zend_Service_Exception('Query requires a SearchIndex');
        } else {
            $this->_validateInArray('SearchIndex',
                                    $options['SearchIndex'],
                                    array_keys(self::$_searchSort));
        }


        // Validate ResponseGroup (required)
        if (empty($options['ResponseGroup'])) {
            throw new Zend_Service_Exception('Query requires a ResponseGroup');
        } else {
            $responseGroup = split(',', $options['ResponseGroup']);

            foreach($responseGroup as $r) {
                if (!in_array($r, array('Request', 'Small', 'Medium', 'Large'))) {
                    throw new Zend_Service_Exception('This wrapper only supports Request, Small, Medium and Large '
                                                   . 'ResponseGroups');
                }
            }
        }


        // Validate Sort (optional)
        if (isset($options['Sort'])) {
            $this->_validateInArray('Sort',
                                    $options['Sort'],
                                    array_values(self::$_searchSort[$options['SearchIndex']]));
        }


        // Validate City (optional)
        if (isset($options['City'])) {
            $this->_validateInArray('City',
                                    $options['City'],
                                    array('Boston', 'Chicago', 'New York', 'San Francisco',
                                          'Seattle', 'Washington, D.C.'));
        }

        if (isset($options['ItemPage'])) {
        	if (!Zend_Filter::isBetween($options['ItemPage'],0, 2500, true)) {
        	    throw new Zend_Service_Exception($options['ItemPage'] . ' is not valid for the "ItemPage" option.');
        	}
        }
    }


    /**
     * Validate options for an ItemLookup
     *
     * @param array $options Options array to be used for the query
     * @throws Zend_Service_Exception
     * @return void
     */
    protected function _validateItemLookup($options = array())
    {
        if (!is_array($options)) {
            throw new Zend_Service_Exception('Options must be specified as an array');
        }

        // Validate keys in the $options array
        $this->_compareOptions($options, array('ItemId',
                                               'IdType',
                                               'SearchIndex',
                                               'MerchantId',
                                               'Condition',
                                               'DeliveryMethod',
                                               'ISPUPostalCode',
                                               'OfferPage',
                                               'ReviewPage',
                                               'VariationPage',
                                               'ResponseGroup',
                                               'Service',
                                               'SubscriptionId',
                                               'Operation'));

        // Validate ResponseGroup (required)
        if (empty($options['ResponseGroup'])) {
            throw new Zend_Service_Exception('Query requires a ResponseGroup');
        } else {
            $responseGroup = split(',', $options['ResponseGroup']);

            foreach($responseGroup as $r) {
                if (!in_array($r, array('Request', 'Small', 'Medium', 'Large'))) {
                    throw new Zend_Service_Exception('This wrapper only supports Request, Small, Medium and Large '
                                                   . 'ResponseGroups');
                }
            }
        }


        // Validate Sort (optional)
        if (isset($options['Sort'])) {
            $this->_validateInArray('Sort',
                                    $options['Sort'],
                                    array_values(self::$_searchSort[$options['SearchIndex']]));
        }


        // Validate City (optional)
        if (isset($options['City'])) {
            $this->_validateInArray('City',
                                    $options['City'],
                                    array('Boston', 'Chicago', 'New York', 'San Francisco',
                                          'Seattle', 'Washington, D.C.'));
        }

        if (isset($options['ItemPage'])) {
        	if (!Zend_Filter::isBetween($options['ItemPage'],0, 2500, true)) {
        	    throw new Zend_Service_Exception($options['ItemPage'] . ' is not a valid value for the "ItemPage" option.');
        	}
        }
    }


    /**
     * Prepare options for request
     *
     * @param string $query Action to perform
     * @param array $options User supplied options
     * @param array $defaultOptions Default options
     * @return array
     */
    protected function _prepareOptions($query, $options, $defaultOptions)
    {
        $options['SubscriptionId'] = $this->appId;
        $options['Service']        = 'AWSECommerceService';
        $options['Operation']      = $query;

        // de-canonicalize out sort key
        if (isset($options['ResponseGroup'])) {
            $responseGroup = split(',', $options['ResponseGroup']);

            if (!in_array('Request', $responseGroup)) {
                $responseGroup[] = 'Request';
                $options['ResponseGroup'] = implode(',', $responseGroup);
            }
        }

        if (isset($options['Sort'])) {
            $options['Sort'] = self::$_searchSort[$options['SearchIndex']][$options['Sort']];
        }

        $options = array_merge($defaultOptions, $options);
        return $options;
    }


    /**
     * Check that all options are valid
     *
     * @param array $options User supplied options
     * @param array $validOptions Valid options
     * @throws Zend_Service_Exception
     */
    protected function _compareOptions($options, $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);

        if ($difference) {
            throw new Zend_Service_Exception('The following parameters are invalid: ' . implode(',', $difference));
        }
    }


    /**
     * Validate that an option is in a given array
     *
     * @param string $name Option Name
     * @param string $value Option value
     * @param array $array Array in which to check for the option
     * @throws Zend_Service_Exception
     */
    protected function _validateInArray($name, $value, $array)
    {
        if (!in_array($value, $array)) {
            throw new Zend_Service_Exception("You have entered an invalid value for $name");
        }
    }


    /**
     * Check result for errors
     *
     * @param DomDocument $dom
     * @throws Zend_Service_Exception
     */
    static protected function _checkErrors(DomDocument $dom)
    {
    	$xpath = new DOMXPath($dom);
        $xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');

		if ($xpath->query('//az:Error')->length >= 1) {
			$code = $xpath->query('//az:Error/az:Code/text()')->item(0)->data;
			$message = $xpath->query('//az:Error/az:Message/text()')->item(0)->data;

			throw new Zend_Service_Exception("$message ($code)");
		}
    }
}
