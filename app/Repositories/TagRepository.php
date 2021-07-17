<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagRepository
{
    protected $tag;

    public function __construct()
    {
        $this->tag = new Tag;
    }

    /**
     * tag들 bulk insert 연산 수행후 삽입한 tag id return
     * @param array $tags
     * @return Collection
     */
    public function insert(array $tags): Collection
    {
        $tagList = [];
        foreach($tags as $tag){
           array_push($tagList,['title'=>$tag]);
        }

        DB::table('tags')->insertOrIgnore($tagList);
        return DB::table('tags')->whereIn('title',$tags)->get();
    }

}
