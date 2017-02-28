<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRules(array(
        '@PSR2' => true,
        'array_syntax' => array('syntax' => 'long'),
        'binary_operator_spaces' => true,
        'cast_spaces' => true,
        'include' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_trailing_whitespace_in_comment' => false,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_scalar' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'self_accessor' => true,
        'whitespace_after_comma_in_array' => true,
    ))
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('application')
            ->exclude('libraries/Pheanstalk')
            ->exclude('libraries/Zend')
            ->exclude('libraries/getid3')
            ->exclude('libraries/htmlpurifier')
    );
