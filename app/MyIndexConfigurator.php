<?php

namespace App;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class MyIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'posts_index';

    protected $settings = [
        'analysis' => [
            "tokenizer" => [
                'custom_nori' => [
                    'type' => 'nori_tokenizer',
                    'decompound_mode' => 'mixed',
                ]
            ],
            'analyzer' => [
                'default_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'custom_nori',
                ]
            ]
        ]
    ];
}
