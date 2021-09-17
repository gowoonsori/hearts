<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\UnAuthorizeException;
use App\Services\CategoryService;
use App\Services\PostService;
use App\Services\UserService;
use App\utils\ApiUtils;
use App\utils\ExceptionMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;
    protected UserService $userService;
    protected PostService $postService;

    public function __construct(CategoryService $categoryService,UserService $userService,PostService $postService)
    {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->postService = $postService;
    }

    /**
     * /user/category
     * 사용자의 카테고리 모두 조회
     * @return JsonResponse
     * @throws UnAuthorizeException
     */
    function getCategories(): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user)) throw new UnAuthorizeException();

        return ApiUtils::success($this->categoryService->getCategoriesByUser($user));
    }


    /**
     * /user/category
     * 카테고리 생성
     * @param Request $request
     * @return JsonResponse
     * @throws BadRequestException|UnAuthorizeException|InternalServerException
     */
    public function createCategory(Request $request): JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //request body($title) validate
        $title = $request['title'];
        if (empty($title) || preg_match_all("/[^a-zA-Z0-9ㄱ-ㅎㅏ-ㅣ가-힣 ]/",$title) != 0 ||
            mb_strlen($title) > 20){
            throw new BadRequestException();
        }

        //카테고리 테이블에 카테고리가 존재하는지 확인
        $category = $this->categoryService->getCategoryByTitle($title);
        if (empty($category)) {
            //없다면 새로 생성
            $category = $this->categoryService->createCategory($title);
        }else{
            //있다면 내가 가진 카테고리인지 확인
            $isMyCategory = $this->categoryService->haveCategoryByCategoryId($userId,$category->id);
            if($isMyCategory){
                throw new BadRequestException(ExceptionMessage::BADREQUEST_CATEGORY_DUPLICATE);
            }
        }

        //user 와의 연관관계 설정
        if (empty($isMyCategory) && $category->users()) {
            $this->categoryService->attachWithUser($category->id,$userId);
        }

        return ApiUtils::success($category,201);
    }

    /**
     * /user/category/{categoryId}
     * 카테고리 수정
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws UnAuthorizeException|InternalServerException
     */
    public function updateCategory(Request $request,int $categoryId): JsonResponse
    {
        //User get
        $user = Auth::user();
        if(empty($user))throw new UnAuthorizeException();

        //기본 카테고리는 삭제 불가 | validate
        $title = $request['title'];
        if ( empty($title) || $categoryId == 1 || gettype($categoryId) != 'integer'
            || preg_match_all("/[^a-zA-Z0-9ㄱ-ㅎㅏ-ㅣ가-힣 _-]/",$title) != 0) {
            throw new BadRequestException();
        }

        //수정할 카테고리가 존재하는지 확인.
        $categories = $this->categoryService->getCategoriesByUser($user)->pluck('id','title')->all();
        if(array_search($categoryId,array_values($categories)) === false){
            throw new BadRequestException(ExceptionMessage::BADREQUEST_CATEGORY_NOTEXIST);
        }else if(array_key_exists($title,$categories)) throw new BadRequestException(ExceptionMessage::BADREQUEST_CATEGORY_DUPLICATE);


        //수정하고 싶은 이름의 카테고리가 존재하는지 확인
        $category = $this->categoryService->getCategoryByTitle($title);
        //없다면 새로 생성
        if(empty($category)){
            $category = $this->categoryService->createCategory($title);
        }

        //검색엔진의 데이터 bulk update
        $postIds = $this->postService->getPostIdsByCategories($user->id,$categoryId);
        $postIds = $postIds->pluck('id')->all();
        $this->postService->bulkUpdateCategoryInElastic($postIds,$category->id, $title);

        //db post 데이터 bulk update
        $this->postService->bulkUpdateCategory($postIds,$category->id);

        //연결관계 수정
        $this->categoryService->updateCategoryConnect($user->id,$categoryId,$category->id);

        return ApiUtils::success($category);
    }

    /**
     * /user/category/{categoryId}
     * 카테고리 삭제
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     * @throws BadRequestException
     * @throws UnAuthorizeException|InternalServerException
     */
    public function deleteCategory(Request $request,int $categoryId): JsonResponse
    {
        //User get
        $userId = Auth::id();
        if(empty($userId))throw new UnAuthorizeException();

        //기본 카테고리는 삭제 불가
        if ($categoryId == 1 || gettype($categoryId) != 'integer') {
            throw new BadRequestException();
        }

        //수정할 카테고리가 존재하는지 확인
        $category = $this->categoryService->haveCategoryByCategoryId($userId,$categoryId);
        if (!$category) {
            //없다면 예외 발생
            throw new BadRequestException(ExceptionMessage::BADREQUEST_CATEGORY_NOTEXIST);
        }

        //연결관계 끊기
        $this->categoryService->detachWithUser($category->id);

        //검색엔진의 데이터 bulk update
        $postIds = $this->postService->getPostIdsByCategories($userId,$categoryId);
        $postIds = $postIds->pluck('id')->all();
        $this->postService->bulkUpdateCategoryInElastic($postIds);

        //해당 카테고리에 속하는 문구들 기본카테고리로 변경
        $this->postService->changeCategoryDefault($userId,$categoryId);

        return ApiUtils::success(true);
    }
}
