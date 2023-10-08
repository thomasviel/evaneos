<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests')
;

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        'linebreak_after_opening_tag' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_no_alias_tag' => false,
        'phpdoc_no_package' => false,
        'semicolon_after_instruction' => true,
        'phpdoc_summary' => false,
        'single_line_throw' => false,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/.php-cs-fixer.cache')
;
