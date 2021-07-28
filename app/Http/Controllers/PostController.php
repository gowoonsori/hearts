<?php


namespace App\Http\Controllers;


use App\Dtos\PostDto;
use App\Exceptions\BadRequestException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizeException;
use App\Services\PostService;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected PostService $postService;
    protected UserService $userService;

    public function __construct(PostService $postService,UserService $userService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
    }


    /**
     * postId로 문구 조회
     * @param Request $request
     * @param $postId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnauthorizeException
     */
    function getPost(Request $request, $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        //post 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException("잘못된 접근입니다.");

        //문구가 검색 불가 설정인데 자기 문구가 아닌 경우
        if(!$post->search && $post->user_id != $userId){
            throw new BadRequestException('조회할 수 없는 문구 입니다.');
        }

        return ApiUtils::success($post);
    }

    /**
     * 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @return JsonResponse
     * @throws InternalServerException
     * @throws UnauthorizeException
     */
    function getPosts(Request $request) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        return ApiUtils::success($this->postService->getPostsByUserId($userId));
    }

    /**
     * 특정 카테고리의 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     * @throws InternalServerException|UnauthorizeException
     */
    function getPostsByCategory(Request $request,int $categoryId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        return ApiUtils::success( $this->postService->getPostsByCategories($userId,$categoryId));
    }

    /**
     * 문구 등록
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws UnauthorizeException|InternalServerException
     */
    function createPost(Request $request) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId, $request['tags']);
        $post = $postDto->getPost();

        //문구 등록
        $post = $this->postService->createPost($post);
        $post = $this->postService->getPostFullInfo($post->id);
        return ApiUtils::success($post);
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

        $post = $this->postService->getPostFullInfo($post->id);
        return ApiUtils::success($post);
    }

    /**
     * 문구 수정
     * @param Request $request
     * @param $postId
     * @return JsonResponse
     * @throws UnauthorizeException
     * @throws ForbiddenException
     * @throws BadRequestException
     */
    function updatePost(Request $request, $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        //수정할 문구가있는지 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException("잘못된 접근입니다.");

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId, $request['tags']);
        $postDto = $postDto->getPost();

        //문구 수정
        $this->postService->updatePost($post, $postDto);
        return ApiUtils::success($post);
    }

    /**
     * 문구 삭제
     * @param Request $request
     * @param $postId
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnauthorizeException
     */
    function deletePost(Request $request, $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        //삭제할 문구가있는지 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException("잘못된 접근입니다.");

        //삭제
        $this->postService->deletePost($post);

        return ApiUtils::success(true);
    }
}
