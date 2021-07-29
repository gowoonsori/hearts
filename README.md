# Hearts (문구 공유 사이트 서버)

### - 사용자 기능
카테고리 생성, 문구 공유, 생성/수정/삭제, 좋아요와 같은 대부분의 기능들은 서비스에 가입한 유저만 사용이 가능하다.

### - 익명사용자 기능
등록된 문구를 특정 키워드로 검색하는 기능을 제공하며 json 형태로 응답

<br>

## Api 명세
### 문구 검색 기능

#### 1. 통합 검색
요청
```json
GET /search?keyword=    HTTP/1.1
Host: localhost/api
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
            "id": 77,
            "content": "샘플 문구",
            "total_like": 0,
            "share_cnt": 0,
            "search": 1,
            "tags": [
                "비행기",
                "사무"
            ],
            "created_at": "2021-07-23 15:49:46",
            "updated_at": "2021-07-23 15:49:46",
            "user_id": 1,
            "category_id": 97
        }
    ]
}
```

<br>

#### 2. 문구내용으로 검색
요청
```json
GET /search/post?keyword=    HTTP/1.1
Host: localhost/api
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
            "id": 77,
            "content": "샘플 문구",
            "total_like": 0,
            "share_cnt": 0,
            "search": 1,
            "tags": [
                "비행기",
                "사무"
            ],
            "created_at": "2021-07-23 15:49:46",
            "updated_at": "2021-07-23 15:49:46",
            "user_id": 1,
            "category_id": 97
        }
    ]
}
```
<br>

#### 3. 태그로 검색
요청
```json
GET /search/tag?keyword=    HTTP/1.1
Host: localhost/api
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": null
}
```

<br>



이 아래 기능들 부터는 쿠키내에 사용자 세션을 가지고 있어야 요청가능하다.
### 카테고리 기능

#### 1. 카테고리 생성
요청
```json
POST /user/category HTTP/1.1
Host: localhost:8000
Content-Type: application/json
    
{
"title" : "샘플 카테고리"
}
```

응답
```json
{
    "success": true,
    "response": {
        "title": "샘플 카테고리",
        "id": 95
    }
}
```

<br>

#### 2. 카테고리 조회
요청

```json
GET /user/category HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
            "id": 95,
            "title": "샘플 카테고리"
        }
    ]
}
```
<br>

#### 3. 카테고리 수정
요청

```json
PATCH /user/category?categoryId=95 HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
"title" : "수정한 카테고리"
}
```

응답
```json
{
    "success": true,
    "response": {
        "title": "수정한 카테고리",
        "id": 96
    }
}
```

<br>

#### 4. 카테고리 삭제
요청

```json
DELETE /user/category/{categoryId} HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": true
}
```

<br>

### 문구 기능
#### 1. 문구 등록
요청
```json
POST /user/post HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
  "content": "샘플 문구",
  "search"  : true,
  "category_id" : 97,
  "tags":[
    "비행기","사무"
  ]
}
```

응답
```json
{
    "success": true,
    "response": {
        "content": "샘플 문구",
        "total_like": 0,
        "share_cnt": 0,
        "search": true,
        "user_id": 1,
        "category_id": 97,
        "tags": [
            "비행기",
            "사무"
        ],
        "id": 77
    }
}
```

<br>

#### 2. 문구 단일 조회
요청
```json
GET /user/post?postId=77 HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": {
        "content": "샘플 문구",
        "total_like": 0,
        "share_cnt": 0,
        "search": true,
        "user_id": 1,
        "category_id": 97,
        "tags": [
            "비행기",
            "사무"
        ],
        "id": 77
    }
}
```

<br>

#### 3. 문구 모두 조회
요청
```json
GET /user/post/all HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
        "content": "샘플 문구",
        "total_like": 0,
        "share_cnt": 0,
        "search": true,
        "user_id": 1,
        "category_id": 97,
        "tags": [
            "비행기",
            "사무"
        ],
        "id": 77
    }]
}
```

<br>

#### 4. 문구 수정
요청
```json
PATCH /user/post?postId= HTTP/1.1
Host: localhost:8000
Content-Type: application/json

{
  "content": "수정 문구",
  "search"  : false,
  "category_id" : 97,
  "tags":[
    "비행기"
  ]
}
```

응답
```json
{
    "success": true,
    "response": {
        "content": "수정 문구",
        "total_like": 0,
        "share_cnt": 0,
        "search": false,
        "user_id": 1,
        "category_id": 97,
        "tags": [
            "비행기"
        ],
        "id": 77
    }
}
```

<br>

#### 5. 문구 삭제
요청
```json
DELETE /user/post?postId= HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": true
}
```

<br>

#### 6. 나의 특정 카테고리의 모든 문구 조회
요청
```json
GET /user/post/category/{categoryId} HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
            "id": 77,
            "content": "샘플 문구",
            "total_like": 0,
            "share_cnt": 0,
            "search": 1,
            "tags": [
                "비행기",
                "사무"
            ],
            "created_at": "2021-07-23 15:49:46",
            "updated_at": "2021-07-23 15:49:46",
            "user_id": 1,
            "category_id": 97
        }
    ]
}
```

<br>

#### 7. 내가 좋아요한 문구들 조회
요청
```json
GET /user/post/like HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": [
        {
            "id": 77,
            "content": "샘플 문구",
            "total_like": 1,
            "share_cnt": 0,
            "search": 1,
            "tags": [
                "비행기",
                "사무"
            ],
            "created_at": "2021-07-23 15:49:46",
            "updated_at": "2021-07-23 15:49:46",
            "user_id": 1,
            "category_id": 97
        }
    ]
}
```

<br>

#### 8. 문구 좋아요
요청
```json
PATCH /user/post/{postId}/like HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": {
            "id": 77,
            "content": "샘플 문구",
            "total_like": 1,
            "share_cnt": 0,
            "search": 1,
            "tags": [
                "비행기",
                "사무"
            ],
            "created_at": "2021-07-23 15:49:46",
            "updated_at": "2021-07-23 15:49:46",
            "user_id": 1,
            "category_id": 97
        }
}
```

<br>

#### 9. 문구 좋아요 취소
요청
```json
DELETE /user/post/{postId}/like HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": true
}
```

<br>

#### 10. 문구 공유수 증가
요청
```json
PATCH /user/post/77/share HTTP/1.1
Host: localhost:8000
Content-Type: application/json
```

응답
```json
{
    "success": true,
    "response": {
        "id": 77,
        "content": "샘플 문구",
        "total_like": 0,
        "share_cnt": 1,
        "search": 1,
        "tags": [
            "비행기",
            "사무"
        ],
        "created_at": "2021-07-23 15:49:46",
        "updated_at": "2021-07-23 15:49:46",
        "user_id": 1,
        "category_id": 97
    }
}
```
