<?php


namespace App\Services;


use App\Exceptions\InternalServerException;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\UserCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    private UserCategoryRepository $userCategoryRepository;

    public function __construct(CategoryRepository $categoryRepository, UserCategoryRepository $userCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->userCategoryRepository = $userCategoryRepository;
    }


    /**
     * User 정보로 카테고리 모두 가져오는 메서드
     * @param User $user
     * @return Collection
     * */
    public function getCategoriesByUser(User $user): Collection
    {
        return $user->categories()->get();
    }

    /**
     * 카테고리 이름으로 카테고리 조회 메서드
     * @param string $title
     * @return object | null
     * @throws InternalServerException
     */
    public function getCategoryByTitle(string $title): ?object
    {
        return $this->categoryRepository->findByTitle($title);
    }


    /**
     * 카테고리 생성 메서드
     * @param string $title
     * @return object | null
     * @throws InternalServerException
     */
    public function createCategory(string $title): ?object
    {
        return $this->categoryRepository->insert($title);
    }

    /**
     * User 가 특정 카테고리를 가지고 있는지 확인하는 메서드
     * @param integer $userId
     * @param integer $categoryId
     * @return object | bool
     * @throws InternalServerException
     */
    public function haveCategoryByCategoryId(int $userId, int $categoryId): ?object
    {
        return $this->userCategoryRepository->findByUserIdAndCategoryId($userId,$categoryId);
    }

    /**
     * 카테고리와 사용자간의 연관관계 수정 메서드
     * @param int $userId
     * @param integer $categoryId
     * @param int $updateId
     * @return void
     * @throws InternalServerException
     */
    public function updateCategoryConnect(int $userId,int $categoryId, int $updateId) : void
    {
        $this->userCategoryRepository->update($userId, $categoryId, $updateId);
    }

    /**
     * 카테고리와 사용자간의 연관관계 맺는 메서드
     * @param integer $categoryId
     * @param integer $userId
     * @return void
     * @throws InternalServerException
     */
    public function attachWithUser(int $categoryId, int $userId) : void
    {
        $this->userCategoryRepository->insert($categoryId,$userId);
    }

    /**
     * 카테고리와 사용자간의 연관관계 삭제 메서드
     * @param int $id
     * @return void
     * @throws InternalServerException
     */
    public function detachWithUser(int $id) : void
    {
        $this->userCategoryRepository->deleteById($id);
    }
}
