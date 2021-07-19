<?php


namespace App\Services;


use App\Repositories\TagRepository;
use Illuminate\Support\Collection;

class TagService
{
    private $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param array $tags
     * @return Collection
     */
    public function createTag(array $tags): Collection
    {
        $tagList = [];
        foreach($tags as $tag){
            array_push($tagList,['title'=>$tag]);
        }
        return $this->tagRepository->insert($tagList);
    }
}
