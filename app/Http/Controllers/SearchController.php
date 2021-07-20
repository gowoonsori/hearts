<?php


namespace App\Http\Controllers;


use App\Models\Post;
use App\utils\ApiUtils;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request){
        $keyword = $request->query('keyword');
        $posts = Post::search($keyword)->get();
       return ApiUtils::success($posts->load('tags'));
    }
}
