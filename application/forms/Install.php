<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Form
 */
class Omeka_Form_Install extends Omeka_Form
{
    const DEFAULT_TAG_DELIMITER = ',';
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    const DEFAULT_SHOW_EMPTY_ELEMENTS = false;
    const DEFAULT_USER_NAME = 'Super User';

    public function init()
    {
        parent::init();

        $this->setMethod('post');


        $defaultLabelOptions = ['placement' => 'prepend', 'tag' => 'div', 'tagClass' => 'two columns alpha', 'requiredSuffix' => sprintf('<span class="required-label">%s</span>', __('required field'))];
        $defaultLabel = new Omeka_Form_Decorator_RawAffixLabel($defaultLabelOptions);

        $decorators = [
                        ['Description', ['tag' => 'p', 'class' => 'explanation', 'escape' => false]],
                        'ViewHelper',
                        ['Errors', ['class' => 'error']],
                        [['InputsTag' => 'HtmlTag'], ['tag' => 'div', 'class' => 'inputs five columns omega']],
                        'Label' => $defaultLabel,
                        [['FieldTag' => 'HtmlTag'], ['tag' => 'div', 'class' => 'field']]
                    ];

        $this->addElement('text', 'username', [
            'label' => __('Username'),
            'description' => __('must be 30 characters or fewer with no whitespace'),
            'validators' => [
                ['StringLength', false, [User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH]],
                ['validator' => 'Regex', 'breakChainOnFailure' => true, 'options' =>
                    [
                        'pattern' => '#^[a-zA-Z0-9.*@+!\-_%\#\^&$]*$#u',
                        'messages' => [
                            Zend_Validate_Regex::NOT_MATCH =>
                                __('Whitespace is not allowed. Only these special characters may be used: %s', ' + ! @ # $ % ^ & * . - _')
                        ]
                    ]
                ]
            ],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('password', 'password', [
            'label' => __('Password'),
            'description' => __('must be at least 6 characters'),
            'validators' => [
                ['validator' => 'NotEmpty', 'options' => [
                    'messages' => [
                        'isEmpty' => 'Password is required.'
                    ]
                ]],
                ['validator' => 'Confirmation', 'options' => [
                    'field' => 'password_confirm',
                    'messages' => [
                        'notMatch' => "Typed passwords do not match."]
                ]],
                ['validator' => 'StringLength', 'options' => [
                    'min' => User::PASSWORD_MIN_LENGTH,
                    'messages' => [
                        'stringLengthTooShort' => "Password must be at least %min% characters in length."]
                ]]
            ],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('password', 'password_confirm', [
            'label' => __('Re-type the Password'),
            'description' => __('Confirm your password.'),
            'required' => true,
            'validators' => [
                ['validator' => 'NotEmpty', 'options' => [
                    'messages' => [
                        'isEmpty' => 'Password confirmation is required.'
                    ]
                ]]
            ],
            'decorators' => $decorators,
        ]);

        $this->addElement('text', 'super_email', [
            'label' => __('Email'),
            'validators' => ['EmailAddress'],
            'decorators' => $decorators,
            'required' => true,
        ]);

        $this->addElement('text', 'site_title', [
            'label' => __('Site Title'),
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('textarea', 'description', [
            'label' => __('Site Description'),
            'decorators' => $decorators,
        ]);

        $this->addElement('text', 'administrator_email', [
            'label' => __('Administrator Email'),
            'validators' => ['EmailAddress'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('text', 'copyright', [
            'label' => __('Site Copyright Information'),
            'decorators' => $decorators,
        ]);

        $this->addElement('text', 'author', [
            'label' => __('Site Author Information'),
            'decorators' => $decorators,
        ]);

        $this->addElement('text', 'tag_delimiter', [
            'label' => __('Tag Delimiter'),
            'description' => __('Separate tags using this character or string.'),
            'value' => self::DEFAULT_TAG_DELIMITER,
            'decorators' => $decorators,
        ]);

        // Allow the tag delimiter to be a whitespace character(s) (except for
        // new lines). The NotEmpty validator (and therefore the required flag)
        // considers spaces to be empty. Because of this we must set the
        // allowEmpty flag to false so Zend_Form_Element::isValid() passes an
        // "empty" value to the validators, and then, using the Regex validator,
        // match the value to a string containing one or more characters.
        $this->getElement('tag_delimiter')->setAllowEmpty(false);
        $this->getElement('tag_delimiter')->addValidator('regex', false, ['/^.+$/']);

        $this->addElement('text', 'fullsize_constraint', [
            'label' => __('Fullsize Image Size'),
            'description' => __('Maximum fullsize image size constraint (in pixels)'),
            'value' => self::DEFAULT_FULLSIZE_CONSTRAINT,
            'validators' => ['Digits'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('text', 'thumbnail_constraint', [
            'label' => __('Thumbnail Size'),
            'description' => __('Maximum thumbnail size constraint (in pixels)'),
            'value' => self::DEFAULT_THUMBNAIL_CONSTRAINT,
            'validators' => ['Digits'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('text', 'square_thumbnail_constraint', [
            'label' => __('Square Thumbnail Size'),
            'description' => __('Maximum square thumbnail size constraint (in pixels)'),
            'value' => self::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT,
            'validators' => ['Digits'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('text', 'per_page_admin', [
            'label' => __('Items Per Page (admin)'),
            'description' => __('Limit the number of items displayed per page in the administrative interface.'),
            'value' => self::DEFAULT_PER_PAGE_ADMIN,
            'validators' => ['Digits'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('text', 'per_page_public', [
            'label' => __('Items Per Page (public)'),
            'description' => __('Limit the number of items displayed per page in the public interface.'),
            'value' => self::DEFAULT_PER_PAGE_PUBLIC,
            'validators' => ['Digits'],
            'decorators' => $decorators,
            'required' => true
        ]);

        $this->addElement('checkbox', 'show_empty_elements', [
            'label' => __('Show Empty Elements'),
            'class' => 'checkbox',
            'description' => __('Check box to show metadata elements with no text.'),
            'value' => self::DEFAULT_SHOW_EMPTY_ELEMENTS,
            'decorators' => $decorators,
        ]);

        $this->addElement('text', 'path_to_convert', [
            'label' => __('ImageMagick Directory Path'),
            'decorators' => $decorators,
        ]);

        $this->addElement('submit', 'install_submit', [
            'label' => __('Install'),
            'decorators' => ['Tooltip', 'ViewHelper']
        ]);

        $this->addDisplayGroup(
            ['username', 'password', 'password_confirm', 'super_email'],
            'superuser_account',
            ['legend' => __('Default Superuser Account')]
        );

        $this->addDisplayGroup(
            ['administrator_email', 'site_title', 'description',
                  'copyright', 'author', 'tag_delimiter', 'fullsize_constraint',
                  'thumbnail_constraint', 'square_thumbnail_constraint',
                  'per_page_admin', 'per_page_public', 'show_empty_elements',
                  'path_to_convert'],
            'site_settings',
            ['legend' => __('Site Settings')]
        );

        $this->addDisplayGroup(
            ['install_submit'],
            'submit'
        );
    }
}
