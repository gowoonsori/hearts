<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $posts = null;
        if(!empty($keyword)) {
            $postIds = Post::search($keyword);

            //posts.index에 없다면 select쿼리 실행 x
            if(empty($postIds->keys()->all())) return ApiUtils::success(null);
            $posts = $postIds->get();

            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if(empty($posts->all())) return ApiUtils::success(null);
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
            $postIds = Post::search($keyword)->whereIn('tags', [$keyword]);

            //posts.index에 없다면 select쿼리 실행 x
            if(empty($postIds->keys()->all())) return ApiUtils::success(null);
            $posts = $postIds->get();

            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if(empty($posts->all())) return ApiUtils::success(null);
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
            $postIds = Post::search($keyword)->whereIn('content', [$keyword]);
            //posts.index에 없다면 select쿼리 실행 x
            if(empty($postIds->keys()->all())) return ApiUtils::success(null);
            $posts = $postIds->get();

            //검색index에는 있으나 db에는 없는 경우(sync가 깨진경우)
            if(empty($posts->all())) return ApiUtils::success(null);
        }

        return ApiUtils::success($posts);
    }
}
