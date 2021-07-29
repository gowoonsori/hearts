<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TeamTNT\TNTSearch\TNTSearch;

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

            if(empty($postIds) && empty($categoryIds)){
                return ApiUtils::success(null);
            }else if(empty($postIds)){
                $posts = $category->get()->load('posts')->pluck('posts')->flatten();
            }else if(empty($categoryIds)){
                $posts = $post->get();
            }else{
                $posts = DB::table('posts')
                    ->whereIn('id',$postIds)
                    ->orWhereIn('category_id',$categoryIds)
                    ->get()
                ->transform(function ($item,$key){
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

            $posts = DB::table('posts')
                ->where('tags','like', '%' . $keyword . '%')
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
            if(empty($postIds->keys()->all())) return ApiUtils::success(null);
            $posts = $postIds->get()->whereIn('content',$keyword);

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
            if(empty($categoryIds->keys()->all())) return ApiUtils::success(null);
            $posts = $categoryIds->get()->load('posts');

            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if($posts?->isEmpty()) return ApiUtils::success(null);
        }
        return ApiUtils::success($posts->pluck('posts')->flatten());
    }


}
