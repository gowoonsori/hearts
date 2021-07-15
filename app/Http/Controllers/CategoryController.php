<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\utils\ApiUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $userRepository;
    protected $categoryRepository;

    public function __construct(UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * 사용자의 카테고리 모두 조회하는 메서드
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     * @throws NotFoundException
     */
    function getCategory(Request $request, string $userId): JsonResponse
    {
        $user = $this->userRepository->findById($userId);
        if (empty($user)) {
            throw new NotFoundException("사용자를 찾을 수 없습니다.");
        }

        $categories = $user->categories()->get();
        //get()은 Collection을 반환 하는데 Collection은 기본적으로 items[]를 가지고있어
        //empty()를 사용해도 체킹되지 않는다. 따라서 Collection의 all()로 items[]를 반환받아
        //empty()로 체크
        if (empty($categories->all())) {
            return ApiUtils::success('null');
        }
        return ApiUtils::success($categories);
    }


    /**
     * @param Request $request
     * @param string $userId
     * @throws NotFoundException
     * @return JsonResponse
     */
    public function insertCategory(Request $request, string $userId): JsonResponse
    {
        //title validate
        $title = $request['title'];
        if(empty($title)){
            Log::error('잘못된 입력입니다.');
            throw new NotFoundException('잘못된 입력입니다.');
        }

        //카테고리 존재하는지 확인
        $user = $this->userRepository->findById($userId);
        $category = $user->categories()->whereTitle($title)->get();
        if(!empty($category->all())){
            Log::error("이미 존재하는 카테고리 입니다.");
            throw new NotFoundException("이미 존재하는 카테고리 입니다.");
        }

        $category = $this->categoryRepository->insert($title,$userId);
        return ApiUtils::success($user->categories()->save($category));
    }
}
