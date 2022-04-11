<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@Symfony' => true,
            '@PhpCsFixer' => true,
            'array_syntax' => ['syntax' => 'short'],
            'declare_strict_types' => true,
            'php_unit_strict' => true,
            'strict_comparison' => true,
            'strict_param' => true,
            'void_return' => true,
            'phpdoc_to_comment' => false,
        ]
    )
    ->setFinder($finder);