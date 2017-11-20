ZF1 - Google ReCaptcha v2 project
===================

Zend Framework 1 ships with ReCacaptcha v1. This project aims to transfer the full functionality of Google ReCaptcha v2 to forms and services.


## Install

Download package, copy files to library / vendor dir. Modify Your application.ini. Project assumes that the autoloader will be able to read defined namespaces so all references to require_once has been removed.

```
; +-----------------------------+
; | Include path and autoloader |
; +-----------------------------+
includePaths.library = BASE_PATH "/library"
autoloaderNamespaces[] = "Ghost"
```

## Usage

To use ReCaptcha V2 element first you need to define additional paths (inside form / by extending Zend_Form):

```php
$form->addElementPrefixPath(
    'Ghost', 
    'Ghost'
);
            
$form->addElementPrefixPath(
    'Ghost_Form_Decorator', 
    'Ghost/Form/Decorator',
    self::DECORATOR // 'decorator'
);
```

To get ReCaptcha v2 working you only need to change the name of the component:

```php
$this->addElement('captcha', 'captcha', array(
    'label' => 'Please solve Captcha puzzle:',
    'required' => true,
    'captcha' => array(
        'captcha' => 'ReCaptcha2',
        'hl' => 'en', // browser's language by deafult, this line is not required
        'theme' => 'light', // see options below
        'pubkey' => 'Your public key goes here',
        'privkey' => 'Your private key goes here'
    )
));
```

To include paths directly, you can use: 

```php
$captcha = new Zend_Form_Element_Captcha('captcha', array(
    'label' => 'Please solve Captcha puzzle:',
    'prefixPath' => array(
        array('prefix' => 'Ghost', 'path' => 'Ghost'),
        Zend_Form::DECORATOR => array('prefix' => 'Ghost_Form_Decorator', 'path' => 'Ghost/Form/Decorator'),
    ),
    'captcha' => array(
        'captcha' => 'ReCaptcha2',
        'pubkey' => 'Your public key goes here',
        'privkey' => 'Your private key goes here'
        'theme' => 'light', // see options below
    )
));
```

## Options

Ghost_Service_ReCaptcha2 constructor allows two different type of options: params and attributes. Both refers to https://developers.google.com/recaptcha/docs/display configuration options.
Parameters are published inside 'script' tag, while the attributes referes to 'div.g-recaptcha element'. By default they are defined as:

```php
/**
 * Parameters for the script object
 *
 * @var array
 */
protected $_params = array(
    'onload' => null,
    'render' => 'onload',
    'hl'     => null
);
    
/**
 * Attributes for div element
 *
 * @var array
 */
protected $_attributes = array(
    'class'            => 'g-recaptcha',
    'theme'            => 'light',
    'type'             => 'image',
    'tabindex'         => 0,
    'callback'         => null,
    'expired-callback' => null
);
```
All attributes are rendered with htmlentities() function.

Zend_Form will render ReCaptcha2 element as concatenation of script and div elements. If you prefer to define JS separately (for example with RequireJS), you can embed elements on the page using two additional methods:
Ghost_Service_ReCaptcha2::getHtmlHead() - <script> only
Ghost_Service_ReCaptcha2::getHtmlBody() - <div class="g-recaptcha"></div> only

## External links

* http://framework.zend.com/
* https://www.google.com/recaptcha/intro/index.html