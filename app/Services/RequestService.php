<?php


namespace App\Services;


use App\Exceptions\BadRequestException;
use App\utils\ExceptionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class RequestService
{
    /**
     * DB의 cursor 기반 페이지네이션을 위해 request 에서 쿼리 parameter 분리하는 메서드
     * @param Request $request
     * @return array|null
     * @throws BadRequestException
     */
    public function getLastIdAndSize(Request $request): ?array
    {
        //cursor validate
        $lastId = $request->query('lastId');
        if (!isset($lastId)) return [];
        if (!is_numeric($lastId)) throw new BadRequestException();
        else{
            $lastId = intval($lastId);
            if ($lastId == 0) $lastId = PHP_INT_MAX;
        }

        //limit 존재하지 않으면 기본값 5로 설정
        $size = $request->query('size');
        if (!isset($size)) $size = 5;
        else if (!is_numeric($size)) throw new BadRequestException();
        else {
            $size = intval($size);
            if ($size > 100) throw new BadRequestException(ExceptionMessage::BADREQUEST_OVER_MAX_SIZE);
        }

        return array(
            'lastId' => $lastId,
            'size' => $size
        );
    }

    /**
     * Elastic search 의 검색 keyword 와 scroll 기반 pagination 인지 판별하기 위한 메서드
     * @param Request $request
     * @return array
     * @throws BadRequestException
     */
    #[ArrayShape(['keyword' => "array|string", 'scrollId' => "null|string", 'size' => "integer"])]
    public function getKeywordAndScrollIdAndSize(Request $request): array
    {
        //검색위한 keyword 쿼리스트링 검사
        $keyword = $request->query('keyword');
        if(!isset($keyword)) throw new BadRequestException();

        //pagination 위한 쿼리스트링 판별
        $scrollId = $request->query('scrollId');
        $size = $request->query('size');
        if(!isset($size)) $size = 5;
        else if (!is_numeric($size)) throw new BadRequestException();
        else {
            $size = intval($size);
            if ($size > 100) throw new BadRequestException(ExceptionMessage::BADREQUEST_OVER_MAX_SIZE);
        }

        return array(
            'keyword' => $keyword,
            'scrollId' => $scrollId,
            'size' => $size
        );
    }
}
