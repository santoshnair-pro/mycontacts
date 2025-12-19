<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src') // Directory to include
    ->in(__DIR__ . '/tests') // Another directory to include
    ->in(__DIR__ . '/database') // Another directory to include
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->exclude([__DIR__ . '/database/migrations',__DIR__ . '/database/seeders']) // Directories to exclude
    ;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'binary_operator_spaces' => [
            'operators' => [
                '=' => 'align_single_space_minimal',
                '=>' => 'align_single_space_minimal',
            ],
        ],
        // Add other rules here
    ])
    ->setFinder($finder)
;
