<?php


namespace App\Services;


use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\UserCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

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
     * @return Collection | null
     * */
    public function getCategoriesByUser(User $user): ?Collection
    {
        $categories = $user->categories()->get();
        if(empty($categories->all())){
            return null;
        }
        return $categories;
    }

    /**
     * 카테고리 이름으로 카테고리 조회 메서드
     * @param string $title
     * @return Category|null
     */
    public function getCategoryByTitle(string $title): ?Category
    {
       return $this->categoryRepository->findByTitle($title);
    }


    /**
     * 카테고리 생성 메서드
     * @param string $title
     * @return User|bool|Model
     */
    public function createCategory(string $title): Model|User|bool
    {
        return $this->categoryRepository->insert($title);
    }

    /**
     * User 가 특정 카테고리를 가지고 있는지 확인하는 메서드
     * @param integer $userId
     * @param integer $categoryId
     */
    public function haveCategory(int $userId, int $categoryId)
    {
        $category =  $this->userCategoryRepository->findByUserIdAndCategoryId($userId,$categoryId);
        if(empty($category))return false;
        return $category;
    }

    /**
     * 카테고리와 사용자간의 연관관계 수정 메서드
     * @param integer $id
     * @param integer $categoryId
     * @return void
     * */
    public function updateCategoryConnect(int $id, int $categoryId){
        $this->userCategoryRepository->update($id, $categoryId);
    }

    /**
     * 카테고리와 사용자간의 연관관계 맺는 메서드
     * @param integer $categoryId
     * @param integer $userId
     * @return void
     * */
    public function attachWithUser(int $categoryId, int $userId){
        $this->userCategoryRepository->insert($categoryId,$userId);
    }

    /**
     * 카테고리와 사용자간의 연관관계 삭제 메서드
     * @param int $id
     * @return void
     */
    public function detachWithUser(int $id){
        $this->userCategoryRepository->deleteById($id);
    }
}
