<?php


namespace App\Services;


use App\Models\Category;
use App\Models\User;
use App\Repositories\CategoryRepository;
use App\Repositories\UserCategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    private $categoryRepository;
    private $userCategoryRepository;

    public function __construct(CategoryRepository $categoryRepository, UserCategoryRepository $userCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->userCategoryRepository = $userCategoryRepository;
    }


    /**
     * @param User $user
     * @return Collection | string
     * */
    public function getCategoriesByUser(User $user)
    {
        $categories = $user->categories()->get();
        if(empty($categories->all())){
            return 'null';
        }
        return $categories;
    }

    /**
     * @param string $title
     * @return Category|null
     */
    public function getCategoryByTitle(string $title): ?Category
    {
       return $this->categoryRepository->findByTitle($title);
    }

    /**
     * @param string $title
     * @return User|bool|Model
     */
    public function createCategory(string $title){
        return $this->categoryRepository->insert($title);
    }

    /**
     * @param integer $userId
     * @param integer $categoryId
     * @return Model|Object|null
     */
    public function haveCategory(int $userId, int $categoryId){
        return $this->userCategoryRepository->haveCategory($userId,$categoryId);
    }

    /**
     * @param Category $category
     * @param User $user
     * @return void
     * */
    public function connectWithUser(Category $category,User $user){
        $category->users()->save($user);
    }
}
