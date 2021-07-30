<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * */
    public function search(Request $request): JsonResponse
    {
        //문구+태그로 검색
        $keyword = $request->query('keyword');
        $post = Post::search($keyword);
        $category = Category::search($keyword);

        $posts = null;
        if(!empty($keyword)) {
            $postIds = $post->keys()->toArray();
            $categoryIds = $category->keys()->toArray();

            //문구내용, 카테고리 두개의 인덱스에 안걸렸을때
            if(empty($postIds) && empty($categoryIds)){
                return ApiUtils::success(null);
            }else if(empty($postIds)){
                //카테고리만 걸렸을때
                $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                    ->join('categories as c','p.category_id','=','c.id')
                    ->join('users as u','u.id','=','p.user_id')
                    ->whereIn('p.category_id',$categoryIds)
                    ->Where('p.search',1)
                    ->get()
                    ->transform(function ($item){
                        $item->tags = json_decode($item->tags);
                        return $item;
                    });
                if($posts?->isEmpty())return ApiUtils::success(null);
            }else if(empty($categoryIds)){
                //문구만 걸렸을때
                $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                    ->join('categories as c','p.category_id','=','c.id')
                    ->join('users as u','u.id','=','p.user_id')
                    ->whereIn('p.id',$postIds)
                    ->get()
                    ->transform(function ($item){
                        $item->tags = json_decode($item->tags);
                        return $item;
                    });
            }else{
                //둘다 포함되어있을때
                $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                    ->join('categories as c','p.category_id','=','c.id')
                    ->join('users as u','u.id','=','p.user_id')
                    ->whereIn('p.id',$postIds)
                    ->orWhereIn('p.category_id',$categoryIds)
                    ->Where('p.search',1)
                    ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
            }
        }

       return ApiUtils::success($posts);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * */
    public function tagSearch(Request $request): JsonResponse
    {
        //태그로 검색
        $keyword = $request->query('keyword');

        $posts = null;
        if(!empty($keyword)) {
            $postIds = Post::search($keyword);

            //posts.index에 없다면 select쿼리 실행 x
            if($postIds->keys()->isEmpty()) return ApiUtils::success(null);
            $postIds = $postIds->keys()->toArray();

            $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->whereIn('p.id',$postIds)
                ->where('p.tags','like', '%' . $keyword . '%')
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
        }

        return ApiUtils::success($posts);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * */
    public function contentSearch(Request $request): JsonResponse
    {
        //문구로 검색
        $keyword = $request->query('keyword');

        $posts = null;
        if(!empty($keyword)) {
            $postIds = Post::search($keyword);
            //posts.index에 없다면 select쿼리 실행 x
            if($postIds->keys()?->isEmpty()) return ApiUtils::success(null);
            $postIds = $postIds->keys()->toArray();

            $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->whereIn('p.id',$postIds)
                ->where('p.content','like', '%' . $keyword . '%')
                ->get()
                ->transform(function ($item){
                    $item->tags = json_decode($item->tags);
                    return $item;
                });

            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if($posts?->isEmpty()) return ApiUtils::success(null);
        }

        return ApiUtils::success($posts);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * */
    public function CategorySearch(Request $request): JsonResponse
    {
        //문구로 검색
        $keyword = $request->query('keyword');

        $posts = null;
        if(!empty($keyword)) {
            $categoryIds = Category::search($keyword);

            //posts.index에 없다면 select쿼리 실행 x
            if($categoryIds->keys()->isEmpty()) return ApiUtils::success(null);
            $categoryIds = $categoryIds->keys()->toArray();

            $posts = DB::table('posts','p')->select(DB::raw('p.*, u.name as owner, c.title as category'))
                ->join('categories as c','p.category_id','=','c.id')
                ->join('users as u','u.id','=','p.user_id')
                ->whereIn('p.category_id',$categoryIds)
                ->where('p.search',1)
                ->get()
                ->transform(function ($item) {
                    $item->tags = json_decode($item->tags);
                    return $item;
                });
            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if($posts?->isEmpty()) return ApiUtils::success(null);
        }
        return ApiUtils::success($posts);
    }


}
