<?php


namespace App\Http\Controllers;


use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizeException;
use App\Services\PostService;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected UserService $userService;
    protected PostService $postService;

    public function __construct(UserService $userService,PostService $postService)
    {
        $this->userService = $userService;
        $this->postService = $postService;
    }

    /**
     * 좋아요 한 문구 조회
     * @param Request $request
     * @return JsonResponse
     * @throws UnauthorizeException|InternalServerException
     */
    public function getLikePosts(Request $request): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        $posts = $this->userService->getLikePosts($user);
        return ApiUtils::success($posts);
    }


    /**
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException
     * @throws UnauthorizeException
     */
    public function likePost(Request $request, int $postId): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnauthorizeException('인증되지 않은 사용자입니다.');


        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if($isLike){
            throw new BadRequestException('이미 좋아요한 글 입니다.');
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException('잘못된 요청입니다.');
        }
        $post = $this->postService->updateLike($post, $user);
        $post = $this->postService->getPostFullInfo($post->id);
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
        if(empty($user)) throw new UnauthorizeException('인증되지 않은 사용자입니다.');

        //문구 get
        $post = $this->postService->getPostById($postId);

        //좋아요 상태인지 체크
        $isLike = $this->postService->isLikePost($user,$post);
        if(!$isLike){
            throw new BadRequestException('좋아요 하지 않은 글입니다.');
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException('잘못된 요청입니다.');
        }

        //좋아요 취소
        $post = $this->postService->deleteLike( $user, $post );
        $post = $this->postService->getPostFullInfo($post->id);
        return ApiUtils::success($post);
    }

}
