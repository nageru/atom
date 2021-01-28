<?php

$finder = PhpCsFixer\Finder::create()
  ->notPath('#/templates/#')
  ->in(__DIR__)
;

return PhpCsFixer\Config::create()
  ->setCacheFile(__DIR__.'/.php_cs.cache')
  ->setIndent("  ")
  // ->setLineEnding("\r\n")
  ->setRules([
    // @PSR2, @Symfony, @PhpCsFixer, @PHP73Migration
    '@PSR1' => true,
    'elseif' => true, // Same in: @PSR2, @Symfony, @PhpCsFixer
    'braces' => ['position_after_control_structures' => 'next'], // Different in: @PSR2, @Symfony, @PhpCsFixer
    'array_syntax' => ['syntax' => 'long'], // Different in: @Symfony, @PhpCsFixer
  ])
  ->setFinder($finder)
;
