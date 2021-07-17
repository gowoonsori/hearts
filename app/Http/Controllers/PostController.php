<?php


namespace App\Http\Controllers;


use App\Dtos\PostDto;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\PostRepository;
use App\Repositories\PostTagRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class PostController extends Controller
{
    protected $userRepository;
    protected $postRepository;
    protected $tagRepository;
    protected $postTagRepository;


    public function __construct(UserRepository $userRepository, PostRepository $postRepository,
                                TagRepository $tagRepository,PostTagRepository $postTagRepository)
    {
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->postTagRepository = $postTagRepository;
    }


    /**
     * postId로 문구 조회
     * @param Request $request
     * @param integer $userId
     * @param integer $postId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws NotFoundException
     */
    function getPost(Request $request, int $userId, int $postId) : JsonResponse
    {
        $post = $this->postRepository->findById($postId);
        // id가 존재하지 않는 경우
        if(empty($post)){
            throw new NotFoundException('존재하지 않은 문구입니다.');
        }

        //문구가 검색 불가 설정인데 자기 문구가 아닌 경우
        if(!$post->search && $post->user_id != $userId){
            throw new BadRequestException('');
        }

        return ApiUtils::success($post);
    }

    /**
     * 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     */
    function getPosts(Request $request, int $userId) : JsonResponse
    {
        $post = $this->postRepository->findAll($userId);
        if(empty($post)) $post = null;
        return ApiUtils::success($post);
    }

    /**
     * 사용자(자신)의 모든 문구 조회
     * @param Request $request
     * @param integer $userId
     * @return JsonResponse
     */
    function getPostsByCategory(Request $request, int $userId,int $categoryId) : JsonResponse
    {
        $post = $this->postRepository->findByCategories($userId,$categoryId);
        if(empty($post)) $post = null;
        return ApiUtils::success($post);
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
        $user = $this->userRepository->findById($userId);
        if(empty($user)){
            throw new NotFoundException('존재하지 않은 사용자입니다.');
        }

        //request body 유효성 검사
        $postDto = new PostDto($request['content'],$request['search'],$request['category_id'],$userId);
        $post = $postDto->getPost();
        //문구 등록
        $user->post()->save($post);


        //tag null이 아니라면 tag 등록
        $tagsRequest = $request['tags'];
        if(!empty($tagsRequest)){
            $tags = $this->tagRepository->insert($tagsRequest);   //tags 삽입
            $this->postTagRepository->insert($post,$tags);
        }

        //tag정보까지 지연로딩 후 반환
        return ApiUtils::success($post->load('tags'));
    }



}
