<?php

return [
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    'declare_strict_types' => true, // Because why not have that extra security?
    'yoda_style' => false, // Only a mentally ill patient would ask "5 is x", so why should we write code like that?
    'use_arrow_functions' => true, // Shorter and more readable whenever possible.
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last', // Because it's almost always the least important and expected outcome.
        'sort_algorithm' => 'none', // Because knowingly sorting based on importance or frequency is much better.
    ],
    'ordered_types' => [
        'null_adjustment' => 'always_last', // Because it's almost always the least important and expected outcome.
        'sort_algorithm' => 'none', // Because knowingly sorting based on importance or frequency is much better.
    ],
    'explicit_string_variable' => false, // Because it's a waste of time and space.
    'phpdoc_align' => [
        'align' => 'left', // Why would you want that many spaces?!
    ],
    'native_function_invocation' => false, // Because it's a waste of time and space.
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line', // Not 'new_line_for_chained_calls' because there is nothing uglier than a single semicolon on a line.
    ],
    'comment_to_phpdoc' => [
        'ignored_tags' => [
            'todo',
            'phpstan-ignore-line',
            'phpstan-ignore-next-line'
        ]
    ],
    'concat_space' => ['spacing' => 'one'], // People with bad eye sight like me can't see those dots when you don't use spaces!
    'single_line_empty_body' => false, // Changing it would make a bigger diff in git.
    'global_namespace_import' => true, // You should import your classes, full stop.
    'increment_style' => false, // Because $i++ is much more natural than ++$i and by the way sometimes you actually do want the behavior.
    'php_unit_strict' => false, // Nah, I'm good.
    'php_unit_test_case_static_method_calls' => false, // Just a personal preference.
];