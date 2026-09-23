<?php

/**
 * Validator for the ServerUrl setting.
 *
 * @package Omeka\Validate
 */
class Omeka_Validate_ServerUrl extends Zend_Validate_Abstract
{
    const MSG_INVALID = 'msgInvalid';

    protected $_messageTemplates = [
        self::MSG_INVALID => "Invalid server URL",
    ];

    public function isValid($value)
    {
        $this->_setValue($value);

        $valid = true;
        $parts = parse_url($value);

        // Scheme is mandatory and must be http/https
        $scheme = $parts['scheme'] ?? '';
        if ($scheme !== 'http' && $scheme !== 'https') {
            $valid = false;
        }
        // Host is mandatory
        if (!isset($parts['host'])) {
            $valid = false;
        }
        // Path is meant to be omitted but we'll tolerate a simple slash
        // (it will be ignored when using the setting)
        if (isset($parts['path']) && $parts['path'] !== '/') {
            $valid = false;
        }
        // Any other parts but port are not allowed (port is optional)
        if (isset($parts['user'], $parts['pass'], $parts['query'], $parts['fragment'])) {
            $valid = false;
        }

        if (!$valid) {
            $this->_error(self::MSG_INVALID);
        }

        return $valid;
    }
}
