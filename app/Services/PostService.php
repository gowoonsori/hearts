<?php


namespace App\Services;


use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerException;
use App\Exceptions\NotFoundException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\SearchRepository;
use App\utils\ExceptionMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PostService
{
    private PostRepository $postRepository;
    private SearchRepository $searchRepository;

    public function __construct(PostRepository $postRepository,SearchRepository $searchRepository)
    {
        $this->postRepository = $postRepository;
        $this->searchRepository = $searchRepository;
    }

    /**
     * 문구 생성 메서드
     * @param $post
     * @return object | null
     * @throws InternalServerException
     */
    public function createPost($post): ?object
    {
        return $this->postRepository->insert($post);
    }


    /**
     * 문구 id로 문구 조회 메서드
     * @param integer $postId
     * @return object | null
     * @throws InternalServerException
     * @throws NotFoundException
     */
    public function getPostById(int $postId): ?object
    {
        $post = $this->postRepository->findById($postId);
        if (empty($post)) {
            throw new NotFoundException('존재하지 않은 문구입니다.');
        }
        return $post;
    }

    /**
     * 문구내용(사용자 이름과 카테고리) 조회 메서드
     * @param int $postId
     * @return object | null
     * @throws InternalServerException
     */
    public function getPostFullInfo(int $postId): ?object
    {
        return $this->postRepository->findByIdWithUserWithCategory($postId);
    }

    /**
     * 유저 id로 모든 문구 조회
     * @param integer $userId
     * @return Collection|null
     * @throws InternalServerException
     */
    public function getPostsByUserId(int $userId): ?Collection
    {
        return $this->postRepository->findAllWithUserWithCategory($userId);
    }

    /**
     * cursor 기반 pagination 문구 조회
     * @param integer $userId
     * @param int $lastId
     * @param int $size
     * @return Collection|null
     * @throws InternalServerException
     */
    public function getPagingPostsByUserId(int $userId, int $lastId, int $size = 5): ?Collection
    {
        return $this->postRepository->findAllByLastIdAndLimitWithUserWithCategory($userId, $lastId, $size);
    }

    /**
     * 사용자의 특정 카테고리의 모든 문구 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return array|Collection|null
     * @throws InternalServerException
     */
    public function getPostsByCategories(int $userId, int $categoryId): array|Collection|null
    {
        return $this->postRepository->findByCategoryAndUserId($userId, $categoryId);
    }

    /**
     * 사용자의 특정 카테고리의 모든 문구 id 조회
     * @param integer $userId
     * @param integer $categoryId
     * @return Collection
     * @throws InternalServerException
     */
    public function getPostIdsByCategories(int $userId, int $categoryId): Collection
    {
        return $this->postRepository->findIdByCategoryAndUserId($userId, $categoryId);
    }

    /**
     * 사용자의 특정 카테고리의 모든 문구 cursor 기반 pagination 조회
     * @param integer $userId
     * @param integer $categoryId
     * @param int $lastId
     * @param int $limit
     * @return array|Collection|null
     * @throws InternalServerException
     */
    public function getPostsByCategoriesCursorPaging(int $userId, int $categoryId, int $lastId, int $limit): array|Collection|null
    {
        return $this->postRepository->findByCategoryAndUserIdAndLastIdAndLimit($userId, $categoryId, $lastId, $limit);
    }

    /**
     * 문구 좋아요 메서드
     * @param $postIds
     * @param $categoryId
     * @return int
     * @throws InternalServerException
     */
    public function bulkUpdateCategory($postIds, $categoryId): int
    {
        return $this->postRepository->updateCategoryByPostIds($postIds,$categoryId);
    }

    /**
     * 문구 좋아요 메서드
     * @param Post $post
     * @param User $user
     * @return Post
     */
    public function updateLike(Post $post, User $user): Post
    {
        $post->likes()->attach($user);
        $post->total_like += 1;
        $post->save();
        return $post;
    }

    /**
     * 문구 좋아요 취소 메서드
     * @param $post
     * @param $user
     * @return Post
     */
    public function deleteLike($user, $post): Post
    {
        $post->likes()->detach($user);
        $post->total_like -= 1;
        $post->save();
        return $post;
    }

    /**
     * 특정 문구를 좋아요 상태인지 판별하는 메서드
     * @param $user
     * @param $post
     * @return bool
     */
    public function isLikePost($user, $post): bool
    {
        $posts = $user->likes()->get();
        if (!empty($posts->all())) {
            $contain = $posts->contains($post);
            if ($contain) return true;
        }
        return false;
    }


    /**
     * 문구의 공유수 증가시키는 메서드
     * @param Post $post
     * @return bool
     * */
    public function updateShareCount(Post $post): bool
    {
        $post->share_cnt += 1;
        return $post->save();
    }

    /**
     * 문구 내용 수정 메서드
     * @param $post
     * @param $postDto
     * @return mixed
     */
    public function updatePost($post, $postDto): mixed
    {
        $post->content = $postDto->content;
        $post->search = $postDto->search;
        $post->category_id = $postDto->category_id;
        $post->tags = $postDto->tags;

        return $post->save();
    }


    /**
     * 문구 삭제 메서드
     * @param $post
     * @return void
     * */
    public function deletePost($post): void
    {
        $post->delete();
    }

    /**
     * $categoryId 카테고리의 문구들 기본카테고리로 수정
     * @param $userId
     * @param $categoryId
     * @return void
     */
    public function changeCategoryDefault($userId, $categoryId): void
    {
        Post::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->update(['category_id' => 1]);
    }

    /**
     * 검색엔진에서 특정 한개 필드로 문구들 score 순 검색
     * @param $field
     * @param $keyword
     * @param $searchMethod
     * @param $keywordType
     * @return array
     */
    public function getPostsMatchSingleField($field, $keyword, $searchMethod,$keywordType): array
    {
        Log::info($field . $keyword . $searchMethod . $keywordType);
        return Post::searchRaw([
            'query' => [
                'bool' => [
                    $searchMethod => [
                        'match' => [
                            $field . $keywordType =>  $keyword,
                        ]
                    ]
                ]
            ],
            'highlight' => [
                "fields" => [
                    $field => [
                        'pre_tags' => ['<em>'],
                        'post_tags' => ['</em>'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * 검색엔진에서 특정 한개 필드로 문구들 scroll pagination 생성
     * @param $field
     * @param $keyword
     * @param $searchMethod
     * @param $keywordType
     * @param $size
     * @return mixed
     */
    public function createScrollIdMatchSingleField($field, $keyword, $searchMethod,$keywordType, $size): mixed
    {
        //쿼리문들 하나의 json 형태로 합침
        $query = [
            'size' => $size,
            'query' => [
                'bool' => [
                    $searchMethod => [
                        'match' => [
                            $field . $keywordType =>  $keyword,
                        ]
                    ]
                ]
            ],
            'highlight' => [
                "fields" => [
                    $field => [
                        'pre_tags' => ['<em>'],
                        'post_tags' => ['</em>'],
                    ]
                ]
            ]
        ];
        $query = json_encode($query,JSON_UNESCAPED_UNICODE);

        //request
        $res = $this->searchRepository->requestElasticSearch('http://localhost:9200/posts_index/_search?scroll=' . Post::SCROLL_TIME,
            'POST',$query);
        Log::info('Scroll create : ' .$res);

        return json_decode($res,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 검색엔진에서 카테고리,문구,태그로 문구들 검색
     * @param $keyword
     * @return array
     */
    public function getPostsMatchMultiField($keyword): array
    {
        return Post::searchRaw([
            'query' => [
                'bool' => [
                    'must' => [
                        'multi_match' => [
                            'fields' => ['content', 'tags.tag', 'category'],
                            'query' => $keyword
                        ]
                    ]
                ]
            ],
            'highlight' => [
                "fields" => [
                    'content' => [
                        'pre_tags' => ['<em>'],
                        'post_tags' => ['</em>'],
                    ],
                    'tags.tag' => [
                        'pre_tags' => ['<em>'],
                        'post_tags' => ['</em>'],
                    ],
                    'category' => [
                        'pre_tags' => ['<em>'],
                        'post_tags' => ['</em>'],
                    ]
                ]
            ]
        ]);
    }

    /**
     * 검색엔진에서 category, content, tag 필드로 문구들 scroll pagination 생성
     * @param $keyword
     * @param $size
     * @return mixed
     * @throws InternalServerException
     */
    public function createScrollIdMatchMultiField($keyword, $size): mixed
    {
        return $this->searchRepository->getScrollPostsMatchMultiFieldByKeywordAndSize($keyword,$size);
    }

    /**
     * scrollId를 가지고 데이터 조회
     * @param $scrollId
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerException
     */
    public function getPostsByScrollId($scrollId): mixed
    {
        $res = $this->searchRepository->getPostsByScrollIdAndScrollTime(Post::SCROLL_TIME, $scrollId);

        //만료된 scrollId 라면 404 응답 => Exception 처리
        if(array_key_exists('status', $res) && $res['status'] == 404 ){
            throw new BadRequestException(ExceptionMessage::BADREQUEST_EXPIRED_SCROLLID);
        }

        return $res;
    }

    /**
     * postId 들의 카테고리들을 기본카테고리로 bulk update
     * @param array $postIds
     * @param int $categoryId
     * @param string $categoryTitle
     * @return mixed
     * @throws InternalServerException
     */
    public function bulkUpdateCategoryInElastic(array $postIds, int $categoryId=1, string $categoryTitle='기본 카테고리'): mixed
    {
        //update 할 postId가 없다면 바로 반환
        if (empty($postIds)) return true;

        return $this->searchRepository->bulkUpdate($postIds,$categoryId,$categoryTitle);
    }

    /**
     * 검색엔진에서 조회한 문구 raw data 를 문구 정보만 추출
     * @param $rawData
     * @return array
     * @throws InternalServerException
     */
    public function getPostsInfoFromElasticRawData($rawData): array
    {
        if (!$rawData) throw new InternalServerException('검색중 오류가 발생했습니다.');

        //scrollId가 존재한다면 scrollId 같이 실어 응답
        $scrollId = false;
        if(array_key_exists('_scroll_id', $rawData)){
            $scrollId = $rawData['_scroll_id'];
        }

        //데이터 비어있는지 확인하고 비어있다면 바로 반환
        $rawData = $rawData['hits']['hits'];
        if (empty($rawData)) return ['data' => []];

        //검색 시간과 score 등 부가정보 제외한 데이터만 반환
        $response = array_map(function ($post) {
            if(array_key_exists('highlight',$post)) return ['source' => $post['_source'], 'highlight' => $post['highlight']];
            return ['source' => $post['_source']];
        }, $rawData);

        //scrollId가 포함되어있다면 scrollId 포함하도록 응답 포맷 변경
        if(!empty($scrollId)) return ['scrollId' => $scrollId, "data" => $response];
        return ['data' => $response];
    }
}
