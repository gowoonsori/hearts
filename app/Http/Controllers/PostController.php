<?php


namespace App\Http\Controllers;


use App\Dtos\PostDto;
use App\Exceptions\BadRequestException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnAuthorizeException;
use App\Repositories\UserCategoryRepository;
use App\Services\CategoryService;
use App\Services\PostService;
use App\Services\RequestService;
use App\Services\UserService;
use App\utils\ApiUtils;
use App\utils\ExceptionMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected PostService $postService;
    protected UserService $userService;
    protected RequestService $requestService;
    protected CategoryService $categoryService;

    public function __construct(PostService $postService,UserService $userService,RequestService $requestService,
                                CategoryService $categoryService)
    {
        $this->postService = $postService;
        $this->userService = $userService;
        $this->requestService = $requestService;
        $this->categoryService = $categoryService;
    }


    /**
     * /user/post/{postId}
     *  postId로 문구 조회
     * @param int $postId
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnAuthorizeException
     */
    function getPost(int $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //post 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException();

        //문구가 검색 불가 설정인데 자기 문구가 아닌 경우
        if(!$post->search && $post->user_id != $userId){
            throw new ForbiddenException();
        }

        return ApiUtils::success($post);
    }

    /**
     * /user/post
     * /user/post?lastId=
     * 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @queryParam $lastId ?int
     * @queryParam $limit ?int
     * @return JsonResponse
     * @throws InternalServerException
     * @throws UnAuthorizeException
     * @throws BadRequestException
     */
    function getPosts(Request $request) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //pagination 조회인지 확인
        $query = $this->requestService->getLastIdAndSize($request);

        if(empty($query)){
            //모두 조회
            $posts = $this->postService->getPostsByUserId($userId);
        }else{
            //pagination
            $posts = $this->postService->getPagingPostsByUserId($userId,$query['lastId'],$query['size']);
        }

        return ApiUtils::success($posts);
    }


    /**
     * /user/post/category/{categoryId}
     * /user/post/category/{categoryId}?lastId=
     * 특정 카테고리의 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param int $categoryId
     * @queryParam $lastId ?int
     * @queryParam $limit ?int
     * @return JsonResponse
     * @throws InternalServerException
     * @throws UnAuthorizeException|BadRequestException
     */
    function getMyPostsByCategory(Request $request,int $categoryId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //pagination 조회인지 확인
        $query = $this->requestService->getLastIdAndSize($request);

        //모두 조회
        if(empty($query)){
            $posts = $this->postService->getPostsByCategories($userId,$categoryId);
        }else{
            //pagination
            $posts = $this->postService->getPostsByCategoriesCursorPaging($userId,$categoryId,$query['lastId'],$query['size']);
        }

        return ApiUtils::success($posts);
    }


    /**
     * /user/post
     * 문구 등록
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws UnAuthorizeException|InternalServerException
     */
    function createPost(Request $request) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId, $request['tags']);
        $post = $postDto->getPost();

        //문구 등록
        $post = $this->postService->createPost($post);
        $post = $this->postService->getPostFullInfo($post->id);
        return ApiUtils::success($post,201);
    }

    /**
     * /user/post/{postId}/share
     * 문구 공유횟수 update
     * @param int $postId
     * @return JsonResponse
     * @throws InternalServerException
     * @throws NotFoundException
     */
    function updateShareCount(int $postId) : JsonResponse
    {
        //문구 조회
        $post = $this->postService->getPostById($postId);

        //공유 횟수 증가
        $success = $this->postService->updateShareCount($post);
        if(!$success) throw new InternalServerException();

        return ApiUtils::success('ok');
    }

    /**
     * /user/post/{postId}
     * 문구 수정
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnAuthorizeException
     */
    function updatePost(Request $request,int $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //수정할 문구가있는지 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException();

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId, $request['tags']);
        $postDto = $postDto->getPost();

        //수정할 카테고리가 존재하는지 검사
        if ( empty( $this->categoryService->haveCategoryByCategoryId($userId,$postDto->category_id) )) {
            throw new BadRequestException(ExceptionMessage::BADREQUEST_CATEGORY_NOTEXIST);
        }

        //문구 수정
        $this->postService->updatePost($post, $postDto);
        return ApiUtils::success($post);
    }

    /**
     * /user/post/{postId}
     * 문구 삭제
     * @param int $postId
     * @return JsonResponse
     * @throws ForbiddenException
     * @throws InternalServerException
     * @throws NotFoundException
     * @throws UnAuthorizeException
     */
    function deletePost(int $postId) : JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //삭제할 문구가있는지 조회
        $post = $this->postService->getPostById($postId);
        if($post->user_id != $userId) throw new ForbiddenException();

        //삭제
        $this->postService->deletePost($post);
        return ApiUtils::success(true);
    }
}
