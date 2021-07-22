<?php


namespace App\Http\Controllers;


use App\Dtos\PostDto;
use App\Exceptions\BadRequestException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizeException;
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

        //post 조회
        $post = $this->postService->getPostById($postId);

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

        //검색노출 여부 파악후 검색엔진 index에 등록
        if($post->search) $post->searchable();

        //tag 정보까지 지연로딩 후 반환
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

    /**
     * 문구 삭제
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws ForbiddenException
     */
    function deletePost(Request $request,int $userId) : JsonResponse
    {
        $postId = $request->query('postId');
        if (empty($postId)) {
            throw new BadRequestException('잘못된 요청입니다.');
        }

        //삭제할 문구가있는지 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException("잘못된 접근입니다.");

        //삭제
        $this->postService->deletePost($post);

        return ApiUtils::success(true);
    }
}
