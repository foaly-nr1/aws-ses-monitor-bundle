<?php

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->files()
    ->exclude('build')
    ->exclude('vendor')
    ->in(__DIR__)
    ->name('*.php');

$header = <<<EOF
This file is part of the AWS SES Monitor Bundle.

@author Adamo Aerendir Crespi <hello@aerendir.me>
EOF;
Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->setUsingCache(false)
    ->fixers([
        // CONTRIB
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        //'echo_to_print',
        //'ereg_to_preg'
        'header_comment',
        //'logical_not_operators_with_spaces',
        //'logical_not_operators_with_successor_space',
        //'long_array_syntax',
        'multiline_spaces_before_semicolon',
        'newline_after_open_tag',
        'no_blanklines_before_namespace',
        'ordered_use',
        //'php4_constructor',
        'phpdoc_order',
        //'phpdoc_var_to_type',
        'php_unit_construct',
        //'phpunit_strict',
        'short_array_syntax',
        'short_echo_tag',
        'strict',
        //'strict_param',

        // PSR0
        'psr0',

        // PSR1
        'encoding',
        'short_tag',

        // PSR2
        'braces',
        //'elseif',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'parenthesis',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',

        // SYMFONY
        'array_element_no_space_before_comma',
        'array_element_white_space_after_comma',
        'blankline_after_open_tag',
        //'concat_without_spaces',
        'double_arrow_multiline_whitespaces', // Check again
        'duplicate_semicolon',
        'empty_return',
        'extra_empty_lines',
        'function_typehint_space',
        'include',
        'join',
        'list_commas',
        //'multiline_array_trailing_comma',
        'namespace_not_leading_whitespace',
        'new_with_braces',
        'no_blank_lines_after_class_opening',
        'no_empty_lines_after_phpdocs',
        'object_operator',
        'operators_spaces',
        'phpdoc_indent',
        'phpdoc_inline_tag',
        'phpdoc_no_access',
        'phpdoc_no_empty_return',
        'phpdoc_no_package',
        'phpdoc_params',
        'phpdoc_scalar',
        'phpdoc_separation',
        'phpdoc_short_description',
        //'phpdoc_to_comment',
        'phpdoc_trim',
        'phpdoc_types',
        'phpdoc_type_to_var',
        //'phpdoc_var_without_name',
        'pre_increment',
        'print_to_echo',
        //'remove_leading_slash_use',
        'remove_lines_between_uses',
        'return',
        'self_accessor',
        'short_bool_cast',
        //'single_array_no_trailing_comma', check again
        'single_blank_line_before_namespace',
        'single_quote',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'trim_array_spaces',
        'unary_operators_spaces',
        'unneeded_control_parantheses',
        'unused_use',
        'whitespacy_lines'
    ])
    ->finder($finder);
