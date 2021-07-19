<?php

namespace App\Http\Controllers;

use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Repositories\CategoryRepository;
use App\Repositories\UserCategoryRepository;
use App\Repositories\UserRepository;
use App\Services\CategoryService;
use App\Services\UserService;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $userService;

    public function __construct(CategoryService $categoryService,UserService $userService)
    {
        $this->categoryService = $categoryService;
        $this->userService = $userService;
    }

    /**
     * 사용자의 카테고리 모두 조회하는 메서드
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     * @throws NotFoundException
     */
    function getCategories(Request $request, string $userId): JsonResponse
    {
        $user = $this->userService->getInfo($userId);

        return ApiUtils::success($this->categoryService->getCategoriesByUser($user));
    }


    /**
     * 카테고리 생성 메서드
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function createCategory(Request $request, string $userId): JsonResponse
    {
        //request body($title) validate
        $title = $request['title'];
        if (empty($title)) {
            Log::error('잘못된 입력입니다.');
            throw new NotFoundException('잘못된 입력입니다.');
        }

        //카테고리 테이블에 카테고리가 존재하는지 확인
        $category = $this->categoryService->getCategoryByTitle($title);
        if (empty($category)) {
            //없다면 새로 생성
            $category = $this->categoryService->createCategory($title);
        }else{
            //있다면 내가 가진 카테고리인지 확인
            $isMyCategory = $this->categoryService->haveCategory($userId,$category->id);
            if(!empty($isMyCategory)){
                throw new BadRequestException('이미 존재하는 카테고리입니다.');
            }
        }

        //user와의 연관관계 설정
        if (empty($MyCategory) && $category->users()) {
            $user = $this->userService->getInfo($userId);
            $this->categoryService->connectWithUser($category,$user);
        }

        return ApiUtils::success($category);
    }
}
