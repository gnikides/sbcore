<?php namespace Core\Services\Elastic;

use ScoutElastic\IndexConfigurator as BaseConfigurator;
use ScoutElastic\Migratable;

class ScoutConfigurator extends BaseConfigurator
{
    use Migratable;

    protected $name;

    public function __construct()
    {
        $this->name = config('scout.elasticsearch.index');
    }

    protected $settings = [
        'analysis' => [
            'filter' => [
                'french_stop' => [
                  'type' => 'stop',
                  'stopwords' => '_french_'
                ],
                'french_elision' => [
                    'type' => 'elision',
                    'articles_case' => true,
                    'articles' => [
                        'l', 'm', 't', 'qu', 'n', 's',
                        'j', 'd', 'c', 'jusqu', 'quoiqu',
                        'lorsqu', 'puisqu'
                      ]
                ],
                //  commented because don't yet have words to exclude from stemming
                // 'french_keywords' => [
                //   'type' => 'keyword_marker',
                //   'keywords' => [put unstemmed words here]
                // ],
                'french_stemmer' => [
                  'type' => 'stemmer',
                  'language' => 'light_french'
                ],
                'english_stop' => [
                  'type' => 'stop',
                  'stopwords' => '_english_'
                ],
                'english_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'english'
                ],
                //  commented because don't yet have words to exclude from stemming
                // 'english_keywords' => [
                //   'type' => 'keyword_marker',
                //   'keywords' => [put unstemmed words here]
                // ],
                'english_possessive_stemmer' => [
                  'type' => 'stemmer',
                  'language' => 'possessive_english'
                ]
            ],
            'analyzer' => [
                //  keyword is for sorting & faceting
                'keyword_lowercase_analyzer' => [
                    'tokenizer' => 'keyword',
                    'filter' => ['lowercase']
                ],
                'french_standard_analyzer' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'french_elision',
                        'lowercase',
                        'french_stop',
                        //  uncomment if needed, see above
                        //  'french_keywords',
                        'french_stemmer'
                    ]
                ],
                'english_standard_analyzer' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'english_possessive_stemmer',
                        'lowercase',
                        'english_stop',
                        //  uncomment if needed, see above
                        //  'english_keywords',
                        'english_stemmer'
                    ]
                ]
            ]
        ]
    ];   
}
