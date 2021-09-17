<?php


namespace Database\Factories;


use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PostFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $tagCount = $this->faker->numberBetween(0,5);
        $tags = [];
        Log::info($tagCount);
        for($i=0 ; $i < $tagCount; $i++){
            Log::info($tags);
            array_push($tags,[
                'tag'=> $this->faker->company(),
                'color' => $this->faker->numberBetween(0,360)]);
        }

        return [
            'content' => $this->faker->realText(80),
            'total_like' => 0,
            'share_cnt' => $this->faker->numberBetween(0, 100000),
            'search' => true,
            'tags' => $tags,
            'user_id' => User::all()->pluck('id')->random(),
            'category_id'=> Category::all()->pluck('id')->random(),
        ];
    }
}
