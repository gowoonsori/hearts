<?php


namespace App\Http\Controllers;


use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Services\PostService;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected $userService;
    protected $postService;

    public function __construct(UserService $userService,PostService $postService)
    {
        $this->userService = $userService;
        $this->postService = $postService;
    }

    /**
     * 좋아요 한 문구 조회
     * @param Request $request
     * @return JsonResponse
     */
    public function getLikePosts(Request $request): JsonResponse
    {
        //User get
        $user = Auth::user();
        $posts = $this->userService->getLikePosts($user);
        return ApiUtils::success($posts);
    }


    /**
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException
     */
    public function likePost(Request $request, int $postId): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new BadRequestException('잘못된 요청입니다.');


        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if($isLike){
            throw new BadRequestException('이미 좋아요한 글 입니다.');
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException('잘못된 요청입니다.');
        }
        $post = $this->postService->updateLike($post, $user);
        return ApiUtils::success($post);
    }

    /**
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException
     */
    public function unlikePost(Request $request, int $postId): JsonResponse
    {
        //User get
        $user = Auth::user();
        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if(!$isLike){
            throw new BadRequestException('좋아요 하지 않은 글입니다.');
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException('잘못된 요청입니다.');
        }
        $post = $this->postService->deleteLike($post, $user);
        return ApiUtils::success($post);
    }

}
