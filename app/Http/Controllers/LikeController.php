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
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException
     */
    public function like(Request $request, int $userId, int $postId): JsonResponse
    {
        $user = $this->userService->getInfo($userId);
        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if($isLike){
            throw new BadRequestException('이미 좋아요한 글 입니다.');
        }
        if(!$post->search && $userId != $post->user_id){
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
    public function unlike(Request $request, int $userId, int $postId): JsonResponse
    {
        $user = $this->userService->getInfo($userId);
        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if(!$isLike){
            throw new BadRequestException('좋아요 하지 않은 글입니다.');
        }
        if(!$post->search && $userId != $post->user_id){
            throw new BadRequestException('잘못된 요청입니다.');
        }
        $post = $this->postService->deleteLike($post, $user);
        return ApiUtils::success($post);
    }

}
