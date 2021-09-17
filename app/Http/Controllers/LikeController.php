<?php


namespace App\Http\Controllers;


use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnAuthorizeException;
use App\Services\PostService;
use App\Services\RequestService;
use App\Services\UserService;
use App\utils\ApiUtils;
use App\utils\ExceptionMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    protected UserService $userService;
    protected PostService $postService;
    protected RequestService $requestService;

    public function __construct(UserService $userService,PostService $postService,RequestService $requestService)
    {
        $this->userService = $userService;
        $this->postService = $postService;
        $this->requestService = $requestService;
    }

    /**
     * /user/post/{postId}/like
     * /user/post/{postId}/like?lastId=
     * 좋아요 한 문구 조회
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException
     * @throws InternalServerException
     * @throws UnAuthorizeException
     */
    public function getLikePosts(Request $request): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnAuthorizeException();

        //pagination 조회인지 확인
        $query = $this->requestService->getLastIdAndSize($request);

        //모두 조회
        if(empty($query)){
            $posts = $this->userService->getLikePosts($user);
        }else{
            //pagination
            $posts = $this->userService->getLikePostsCursorPaging($user,$query['lastId'],$query['size']);
        }

        return ApiUtils::success($posts);
    }


    /**
     * /user/post/{postId}/like
     * 문구 좋아요 하기
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException
     * @throws UnAuthorizeException
     */
    public function likePost(int $postId): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnAuthorizeException();

        //validate
        if(gettype($postId) != 'integer') throw new BadRequestException();

        //좋아요 상태 확인
        $post = $this->postService->getPostById($postId);
        $isLike = $this->postService->isLikePost($user,$post);
        if($isLike){
            throw new BadRequestException(ExceptionMessage::BADREQUEST_ALREADY_LIKE);
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException();
        }

        //좋아요 수행
        $this->postService->updateLike($post, $user);
        return ApiUtils::success('ok');
    }

    /**
     * /user/post/{postId}/like
     * 문구 좋아요 취소하기
     * @throws NotFoundException
     * @throws InternalServerException
     * @throws BadRequestException|UnAuthorizeException
     */
    public function unlikePost(int $postId): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnAuthorizeException();

        //validate
        if(gettype($postId) != 'integer') throw new BadRequestException();

        //문구 get
        $post = $this->postService->getPostById($postId);

        //좋아요 상태인지 체크
        $isLike = $this->postService->isLikePost($user,$post);
        if(!$isLike){
            throw new BadRequestException(ExceptionMessage::BADREQUEST_NOTYET_LIKE);
        }
        if(!$post->search && $user->id != $post->user_id){
            throw new BadRequestException();
        }

        //좋아요 취소
        $this->postService->deleteLike( $user, $post );
        return ApiUtils::success('ok');
    }
}
