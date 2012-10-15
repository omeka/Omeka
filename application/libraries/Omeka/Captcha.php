<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Factory for creating a captcha for use when soliciting public input.
 * 
 * @package Omeka\Captcha
 */
class Omeka_Captcha {

    const PUBLIC_KEY_OPTION = 'recaptcha_public_key';
    const PRIVATE_KEY_OPTION = 'recaptcha_private_key';
    
    /**
     * Get a captcha object implementing Zend's captcha API.
     *
     * @internal Currently returns a Zend_Captcha_ReCaptcha object.
     *
     * @return Zend_Captcha_Adapter|null
     */
    static function getCaptcha()
    {
        $publicKey = get_option(self::PUBLIC_KEY_OPTION);
        $privateKey = get_option(self::PRIVATE_KEY_OPTION);

        if (empty($publicKey) || empty($privateKey)) {
           return null;
        }

        $ssl = false;
        if ($request = Zend_Controller_Front::getInstance()->getRequest()) {
            $ssl = $request->isSecure();
        }

        $captcha = new Zend_Captcha_ReCaptcha(array(
            'pubKey' => $publicKey,
            'privKey' => $privateKey,
            'ssl' => $ssl));

        return $captcha;
    }

    /**
     * Return whether the captcha is configured.
     * If this returns true, getCaptcha will not return null.
     *
     * @return boolean
     */
    static function isConfigured()
    {
        $publicKey = get_option(self::PUBLIC_KEY_OPTION);
        $privateKey = get_option(self::PRIVATE_KEY_OPTION);

        return !(empty($publicKey) || empty($privateKey));
    }
}
