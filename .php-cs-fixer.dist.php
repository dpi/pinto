<?php

$finder = PhpCsFixer\Finder::create()
  ->in([
      'src',
      'tests',
  ])
  ->exclude([
      'cache',
      'vendor',
  ]);

return (new PhpCsFixer\Config('Pinto'))
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'global_namespace_import' => false,
        'fully_qualified_strict_types' => true,
        'declare_strict_types' => true,
        'phpdoc_summary' => true,
        'phpdoc_align' => false,
        'phpdoc_no_useless_inheritdoc' => true,
        'strict_param' => true,
        'ordered_imports' => true,
        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],
        'increment_style' => false,
    ]);
