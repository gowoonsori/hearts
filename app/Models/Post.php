<?php

namespace App\Models;

use App\MyIndexConfigurator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ScoutElastic\Searchable;

class Post extends Model
{
    use HasFactory,Searchable;

    public const CONTENT_FIELD = 'content';
    public const TAG_FIELD = 'tags.tag';
    public const CATEGORY_FIELD = 'category';
    public const SCROLL_TIME = '10m';

    public $timestamps = false;

    protected $fillable = [
        'content',
        'total_like',
        'share_cnt',
        'search',
        'user_id',
        'category_id',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'search' => 'boolean',
    ];

    protected $hidden = ['pivot'];

    protected $indexConfigurator = MyIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'content' => [
                'type' => 'text',
                'analyzer' => 'nori',
                'search_analyzer' => 'nori',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            'tags' => [
                'properties' => [
                    'tag' =>[
                        'type' => 'text',
                        'analyzer' => 'nori',
                        'search_analyzer' => 'nori',
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                            ],
                        ]
                    ]
                ]
            ],
            'category' => [
                'type' => 'text',
                'analyzer' => 'nori',
                'search_analyzer' => 'nori',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ]
            ],
            'owner' => [
                'type' => 'text',
                'analyzer' => 'nori',
                'search_analyzer' => 'nori',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ]
            ],
            'total_like' => [
                'type' => 'integer',
                'index' => false,
            ],
            'share_cnt' => [
                'type' => 'integer',
                'index' => false,
            ],
            'search' => [
                'type' => 'boolean',
                'index' => false,
            ],
            'user_id' => [
                'type' => 'integer',
                'index' => false,
            ],
            'category_id' => [
                'type' => 'integer',
                'index' => false,
            ],
        ]
    ];

    public function searchableAs(): string
    {
        return 'posts_index';
    }

    public function toSearchableArray (): array {
        $array =  $this->load(['category','user'])->toArray();
        $result = [
            'id' => $array['id'],
            'content' => $array['content'],
            'category' => $array['category']['title'],
            'total_like' => $array['total_like'],
            'share_cnt' => $array['share_cnt'],
            'search' => $array['search'],
            'user_id' => $array['user_id'],
            'category_id' => $array['category_id'],
            'owner' => $array['user']['name'],
        ];
        return array_merge($result, ['tags' =>$array['tags']]);
    }

    public function shouldBeSearchable()
    {
        return $this->search;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany( User::class,'likes');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
