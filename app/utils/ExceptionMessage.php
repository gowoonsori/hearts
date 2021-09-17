<?php


namespace App\utils;


final class ExceptionMessage
{
    public const BADREQUEST =  '잘못된 요청입니다.';
    public const BADREQUEST_CATEGORY_DUPLICATE =  '이미 존재하는 카테고리입니다.';
    public const BADREQUEST_CATEGORY_NOTEXIST =  '카테고리가 존재하지 않습니다.';
    public const BADREQUEST_ALREADY_LIKE =  '이미 좋아요한 글 입니다.';
    public const BADREQUEST_NOTYET_LIKE =  '좋아요 하지 않은 글입니다.';
    public const BADREQUEST_EXPIRED_SCROLLID =  '유효하지 않은 scroll id 입니다.';
    public const BADREQUEST_OVER_MAX_SIZE =  '요청하신 size가 최대 size 100을 넘었습니다.';

    public const UNAUTHORIZE =  '인증에 실패하였습니다.';
    public const FORBIDDEN =  '접근권한이 존재하지 않습니다.';
    public const NOTFOUND =   '찾을 수 없습니다.';

    public const INTERNAL =  '오류가 발생하였습니다.';
    public const INTERNAL_USER_GET =  '사용자 조회중 오류가 발생했습니다.';
    public const INTERNAL_USER_INSERT =  '사용자 생성중 오류가 발생했습니다.';
    public const INTERNAL_CATEGORY_GET =  '카테고리 조회중 오류가 발생했습니다.';
    public const INTERNAL_CATEGORY_INSERT =  '카테고리 생성중 오류가 발생했습니다.';
    public const INTERNAL_CATEGORY_UPDATE =  '카테고리 수정중 오류가 발생했습니다.';
    public const INTERNAL_CATEGORY_DELETE =  '카테고리 삭제중 오류가 발생했습니다.';
    public const INTERNAL_POST_GET =  '문구 조회중 오류가 발생했습니다.';
    public const INTERNAL_POST_INSERT =  '문구 생성중 오류가 발생했습니다.';
    public const INTERNAL_POST_UPDATE =  '문구 수정중 오류가 발생했습니다.';
}
