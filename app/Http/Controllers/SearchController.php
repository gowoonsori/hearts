<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\Services\PostService;
use App\Services\RequestService;
use App\utils\ApiUtils;
use App\utils\ExceptionMessage;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    private PostService $postService;
    private RequestService $requestService;

    public function __construct(PostService $postService,RequestService $requestService)
    {
        $this->postService = $postService;
        $this->requestService = $requestService;
    }

    /**
     * /search?keyword=
     * /search?keyword=?lastId=
     * 문구 내용, 카테고리, 태그 통합 검색
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException|InternalServerException
     */
    public function search(Request $request): JsonResponse
    {
        $queryString = $this->requestService->getKeywordAndScrollIdAndSize($request);

        if(!isset($queryString['scrollId'])){
            //모두 조회
            $posts = $this->postService->getPostsMatchMultiField($queryString['keyword']);
        }else if($queryString['scrollId'] === "0"){
            //scroll 생성
            $posts = $this->postService->createScrollIdMatchMultiField($queryString['keyword'],$queryString['size']);
        }else{
            //scroll 조회
            $posts = $this->postService->getPostsByScrollId($queryString['scrollId']);
        }

        //문구정보만 추출
        $posts = $this->postService->getPostsInfoFromElasticRawData($posts);
        return ApiUtils::success($posts);
    }

    /**
     * /search/tag?keyword=
     * /search/tag?keyword=?lastId=
     * 태그로 검색
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException|InternalServerException
     */
    public function tagSearch(Request $request): JsonResponse
    {
        //태그 필드로 검색
        return ApiUtils::success($this->searchBySingleField($request,Post::TAG_FIELD) );
    }

    /**
     * /search/post?keyword=
     * /search/post?keyword=?lastId=
     * 문구 내용으로 검색
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException|InternalServerException
     */
    public function contentSearch(Request $request): JsonResponse
    {
        //content 필드로 검색
        return ApiUtils::success( $this->searchBySingleField($request,Post::CONTENT_FIELD));
    }

    /**
     * /search/category?keyword=
     * /search/category?keyword=?lastId=
     * 카테고리로 문구 조회
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException|InternalServerException
     */
    public function CategorySearch(Request $request): JsonResponse
    {
        //카테고리 필드로 검색
        return ApiUtils::success($this->searchBySingleField($request,Post::CATEGORY_FIELD));
    }

    /**
     * 키워드로 단일 필드 검색후 정보만 반환해주는 메서드
     * @throws InternalServerException
     * @throws BadRequestException
     */
    private function searchBySingleField(Request $request, string $searchField): array
    {
        $queryString = $this->requestService->getKeywordAndScrollIdAndSize($request);

        //정확히 일치하는 문서조회 인지 쿼리 스트링 판별
        $exact = $request->query('exact');
        if($exact === 'true') {
            $searchMethod = 'filter';
            $keywordType = '.keyword';
        }else{
            $searchMethod = 'must';
            $keywordType = '';
        }

        if(!isset($queryString['scrollId'])){
            //모두 조회
            $posts = $this->postService->getPostsMatchSingleField($searchField,$queryString['keyword'],$searchMethod,$keywordType);
        }else if($queryString['scrollId'] === "0"){
            //scroll 생성
            $posts = $this->postService->createScrollIdMatchSingleField($searchField,$queryString['keyword'],$searchMethod,$keywordType,$queryString['size']);
        }else{
            //scroll 조회
            $posts = $this->postService->getPostsByScrollId($queryString['scrollId']);
        }

        //문구정보만 추출
        return $this->postService->getPostsInfoFromElasticRawData($posts);
    }

}
