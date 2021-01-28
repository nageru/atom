<?php

$finder = PhpCsFixer\Finder::create()
  ->exclude('cache')  // vendor is already excluded
  ->notPath('#/templates/#')
  ->notPath('#/model/om/#')
  ->notPath('#/model/map/#')
  ->in(__DIR__);

return PhpCsFixer\Config::create()
  ->setCacheFile(__DIR__.'/.php_cs.cache')
  ->setIndent("  ")
  ->setRules([
    // '@PSR12' => true,  // or @PSR2, @PhpCsFixer, @Symfony, etc
    'elseif' => true,
    'function_declaration' => true,
    'no_spaces_after_function_name' => true,
    'blank_line_after_opening_tag' => true,  // not in @PSR2
    'no_closing_tag' => true,
    'indentation_type' => true,
    'line_ending' => true,
    'no_extra_blank_lines' => true,  // not in @PSR12, @PSR2; rule with more tokens below
    'no_spaces_inside_parenthesis' => true,
    'no_spaces_around_offset' => true,  // not in @PSR12, @PSR2
    'no_trailing_whitespace' => true,
    'no_whitespace_in_blank_line' => true,
    'single_blank_line_at_eof' => true,
    'multiline_whitespace_before_semicolons' => true,  // not in any
    'no_singleline_whitespace_before_semicolons' => true,  // not in @PSR12, @PSR2
    'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],  // not in @PSR12, @PSR2
    'no_useless_return' => true,  // only in @PhpCsFixer
    'return_assignment' => true,  // only in @PhpCsFixer
    'simplified_null_return' => true,  // not in any
    /*
    'braces' => ['position_after_control_structures' => 'next'],  // different in all
    'blank_line_before_statement' => [  // different in all
      'break', 'case', 'continue', 'declare', 'default', 'die', 'do', 'exit', 'for',
      'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once',
      'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from',
    ],
    'array_syntax' => ['syntax' => 'short'],  // different in @PSR12, @PSR2
    'method_chaining_indentation' => true,  // only in @PhpCsFixer
    'no_extra_blank_lines' => ['tokens' => [  // different in all (example from @PhpCsFixer)
      'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block',
      'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait'
    ]],
    */
  ])
  ->setFinder($finder);
