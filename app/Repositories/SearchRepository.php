<?php


namespace App\Repositories;


use App\Exceptions\InternalServerException;
use App\Models\Post;
use App\utils\ExceptionMessage;
use CurlHandle;
use Illuminate\Support\Facades\Log;

class SearchRepository
{
    private CurlHandle $client;

    public function __construct()
    {
        $this->client = curl_init();
    }

    /**
     * content, category, tag 다중필드로 검색하여 scrollId생성하는 메서드
     * @throws InternalServerException
     */
    public function getScrollPostsMatchMultiFieldByKeywordAndSize($keyword, $size)
    {
        try {
            $query = [
                'size' => $size,
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
                        ]]
                ]
            ];
            $query = json_encode($query,JSON_UNESCAPED_UNICODE);

            //request
            $res = self::requestElasticSearch('http://localhost:9200/posts_index/_search?scroll=' . Post::SCROLL_TIME,
                'POST', $query);
            Log::info('scroll_Id create : ' . $res);
            return json_decode($res,JSON_UNESCAPED_UNICODE);

        }catch (\Exception $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * content, category, tag 다중필드로 검색하여 scrollId 생성하는 메서드
     * @throws InternalServerException
     */
    public function bulkUpdate($postIds, $categoryId, $categoryTitle)
    {
        try {
            //update 할 doc 쿼리문들생성
            $querySyntax = array_map(function ($id) use($categoryId, $categoryTitle){
                return '{ "update" : {  "_id" : "' . $id . '" } }
{ "doc" :{ "category_id" : '. $categoryId . ', "category" : "' . $categoryTitle . '"} }
';
            }, $postIds);

            //쿼리문들 하나의 json 형태로 합침
            $query = '';
            foreach ($querySyntax as $syntax) {
                $query = $query . $syntax;
            }
            $query = json_encode($query,JSON_UNESCAPED_UNICODE);

            //request
            $res = self::requestElasticSearch('http://localhost:9200/posts_index/_bulk', 'POST', $query);
            Log::info('Bulk update : ' . $res);

            return json_decode($res,JSON_UNESCAPED_UNICODE);

        }catch (\Exception $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_UPDATE);
        }
    }

    /**
     * Scroll time 과 scrollId를 가지고 다음 문서 조회하는 메서드
     * @throws InternalServerException
     */
    public function getPostsByScrollIdAndScrollTime($scrollTime, $scrollId)
    {
        try {
            $query = [
                'scroll' => $scrollTime,
                'scroll_id' => $scrollId
            ];
            //Json 형태로 변경
            $query = json_encode($query, JSON_UNESCAPED_UNICODE);

            //request
            $res = self::requestElasticSearch('http://localhost:9200/_search/scroll', 'POST', $query);
            Log::info('get document by scroll_Id : ' . $res);
            return json_decode($res,JSON_UNESCAPED_UNICODE);

        }catch (\Exception $e){
            throw new InternalServerException(ExceptionMessage::INTERNAL_POST_GET);
        }
    }

    /**
     * 내부 elasticsearch 서버에 직접 query 를 요청하는 메서드
     * @param string $url
     * @param string $method
     * @param string $query
     * @return string|bool
     */
    public function requestElasticSearch(string $url,string $method,string $query) : string|bool
    {
        curl_setopt_array($this->client, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        return curl_exec($this->client);
    }
}
