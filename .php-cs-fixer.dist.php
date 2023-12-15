<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@array_syntax' => 'long',
        '@array_indentation' => true,
        '@phpdoc_to_param_type' => true
    ])
    ->setFinder($finder)
;
