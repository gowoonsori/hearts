<?php


namespace App\Http\Controllers;


use App\Dtos\PostDto;
use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Services\PostService;
use App\Services\TagService;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $postService;
    protected $userService;
    protected $tagService;

    public function __construct(PostService $postService,UserService $userService,TagService $tagService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
        $this->tagService = $tagService;
    }


    /**
     * postId로 문구 조회
     * @param Request $request
     * @param integer $userId
     * queryString postId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InternalServerException
     */
    function getPost(Request $request, int $userId) : JsonResponse
    {
        //validate
        $postId = $request->query('postId');


        $post = $this->postService->getPostById($postId);
        // id가 존재하지 않는 경우


        //문구가 검색 불가 설정인데 자기 문구가 아닌 경우
        if(!$post->search && $post->user_id != $userId){
            throw new BadRequestException('조회할 수 없는 문구 입니다.');
        }

        return ApiUtils::success($post);
    }

    /**
     * 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     * @throws NotFoundException|InternalServerException
     */
    function getPosts(Request $request, int $userId) : JsonResponse
    {
        $user = $this->userService->getInfo($userId);
        return ApiUtils::success($this->postService->getPostsByUserId($userId));
    }

    /**
     * 특정 카테고리의 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param integer $userId
     * @param int $categoryId
     * @return JsonResponse
     * @throws InternalServerException
     */
    function getPostsByCategory(Request $request, int $userId,int $categoryId) : JsonResponse
    {
        return ApiUtils::success( $this->postService->getPostsByCategories($userId,$categoryId));
    }

    /**
     * 문구 등록
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     * @throws NotFoundException
     * @throws BadRequestException
     */
    function createPost(Request $request, int $userId) : JsonResponse
    {
        $user = $this->userService->getInfo($userId);

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId);
        $post = $postDto->getPost();

        //문구 등록
        $this->userService->createPost($user, $post);

        //tag null이 아니라면 tag 등록과 연결
        $tagsRequest = $request['tags'];
        if(!empty($tagsRequest)){
            $tags = $this->tagService->createTag($tagsRequest);
            $this->postService->connectWithTags($post, $tags);
        }

        //tag정보까지 지연로딩 후 반환
        return ApiUtils::success($post->load('tags'));
    }

    /**
     * 문구 공유횟수 update
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     * @throws InternalServerException
     * @throws NotFoundException
     */
    function updateShareCount(Request $request,int $postId) : JsonResponse
    {
        $post = $this->postService->getPostById($postId);
        $success = $this->postService->updateShareCount($post);
        if(!$success) throw new InternalServerException('update도중 오류가 발생했습니다.');

        return ApiUtils::success($post);
    }
}
