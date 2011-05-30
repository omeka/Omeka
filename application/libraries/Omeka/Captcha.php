<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Factory for creating a captcha for use when soliciting public input.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
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

        $captcha = new Zend_Captcha_ReCaptcha(array(
            'pubKey' => $publicKey,
            'privKey' => $privateKey));

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
