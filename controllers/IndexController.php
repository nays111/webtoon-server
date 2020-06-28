<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "test":
            http_response_code(200);
            $res->result = getWebtoonData();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testDetail":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testPost":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getUser":
            http_response_code(200);
            $res->result = getUser();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
            /* ***************************************************************** */
            /* ***************************************************************** */
        /*
         * API No. 1
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.06.19
         */
        case "postUser":
            http_response_code(200);

            if(!isValidID($req->userID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "잘못된 ID 형식입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidPassword($req->pw)){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "잘못된 비밀번호 형식입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidAge($req->age)){
                $res->isSuccess = FALSE;
                $res->code = 203;
                $res->message = "잘못된 나이 형식 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if($req->gender!="남" && $req->gender!="여"){
                $res->isSuccess = FALSE;
                $res->code = 204;
                $res->message = "잘못된 형식의 성별 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            } else if(!isValidName($req->userName)){
                $res->isSuccess = FALSE;
                $res->code = 205;
                $res->message = "잘못된 형식의 이름 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(!isValidNickName($req->userNickname)){
                $res->isSuccess = FALSE;
                $res->code = 206;
                $res->message = "잘못된 형식의 닉네임 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }else if(validUser($req->userID)){
                $res->isSuccess = FALSE;
                $res->code = 207;
                $res->message = "중복된 아이디 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            postUser($req->userID,$req->pw,$req->userName,$req->userNickname,$req->age,$req->gender,$req->age);
            $result['유저ID']=$req->userID;
            $result['이름']=$req->userName;
            $result['닉네임']=$req->userNickname;
            $result['나이']=$req->age;
            $result['성별']=$req->gender;

            $jwt = getJWToken($req->userID,$req->pw,JWT_SECRET_KEY);
            $result['jwt']=$jwt;

            $res->result = $result;

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원 생성 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 2
         * API Name : 로그인 API
         * 마지막 수정 날짜 : 20.06.19
         */
        case "login":
            http_response_code(200);

            if(!isValidUser($req->userID,$req->pw)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "로그인에 실패하였습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $jwt = getJWToken($req->userID,$req->pw, JWT_SECRET_KEY);
            $result=[];
            $result['inf']=login($req->userID,$req->pw);
            $result['jwt']=$jwt;


            $res->result = $result;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;


        /*
         * API No. 3
         * API Name : 회원 탈퇴 API
         * 마지막 수정 날짜 : 20.06.20
         */
        case "deleteUser":
            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];


            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    if(!validUser($userID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 유저가 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    deleteUser($userID);
                    $result['유저ID']=$userID;

                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "유저 삭제 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }

            break;
        /*
         * API No. 4
         * API Name : 유저 조회 API
         * 마지막 수정 날짜 : 20.06.19
         */
        case "getUser":
            http_response_code(200);
            $res->result = getUser();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*


        /*
         * API No. 5
         * API Name : 전체 웹툰 리스트 조회 API
         * 마지막 수정 날짜 : 20.06.16
         */
        case "getWebtoons":
            http_response_code(200);

            $authorName= $_GET['authorName']; //쿼리스트링 사용하기 위하여 추가
            $webtoonTitle=$_GET['webtoonTitle']; //쿼리스트링 사용하기위하여 추가
            $webtoonDay = $_GET['webtoonDay'];
            $orderBy = $_GET['orderBy'];

            if ($authorName){

                if(!getAuthorID($authorName)){
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "해당 작가는 존재하지 않습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }else{
                    $res->result = getWebtoonsAuthor($authorName);
                    $res->isSuccess = TRUE;
                    $res->code = 105;
                    $res->message = "작가 검색 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }elseif($webtoonTitle){
                if(!getWebtoonID($webtoonTitle)){

                    $res->isSuccess = FALSE;
                    $res->code = 202;
                    $res->message = "해당 웹툰이 존재하지 않습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }else{
                    $res->result = getWebtoonsTitle($webtoonTitle);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "웹툰 검색 조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }elseif($webtoonDay) {
                if($webtoonDay != "mon" && $webtoonDay != "tue" && $webtoonDay != "wed" && $webtoonDay != "thu" && $webtoonDay != "fri" && $webtoonDay != "sat" && $webtoonDay != "sun" ){
                    $res->isSuccess = FALSE;
                    $res->code = 203;
                    $res->message = "테스트 실패(webtoonday가 없습니다)";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                if($orderBy){
                    if($orderBy == "history"){
                        $res->result = getWebtoonsDayHistory($webtoonDay);
                        $res->isSuccess = TRUE;
                        $res->code = 101;
                        $res->message = "조회순 검색 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }elseif($orderBy == "star"){
                        $res->result = getWebtoonsDayStar($webtoonDay);
                        $res->isSuccess = TRUE;
                        $res->code = 102;
                        $res->message = "별점순 검색 조회 성공";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }else{
                        $res->isSuccess = FALSE;
                        $res->code = 201;
                        $res->message = "ORDER BY 절 잘못입력";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                }

                $res->result = getWebtoonsDay($webtoonDay);
                $res->isSuccess = TRUE;
                $res->code = 103;
                $res->message = "요일별 웹툰 조회 성공";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res->result= getWebtoonsAll();
            $res->isSuccess = TRUE;
            $res->code = 104;
            $res->message = "전체웹툰 조회 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 7
         * API Name : 웹툰 하트 추가 API
         * 마지막 수정 날짜 : 20.06.25
         */
        case "postWebtoonHeart":
            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    if(!isvalidWebtoon($req->webtoonID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 웹툰이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;

                    }else if (isValidWebtoonHeart($req->webtoonID) and isValidUserHeart($userID)){
                        deleteWebtoonHeart($userID,$req->webtoonID);
                        $result['유저ID']=$userID;
                        $result['웹툰ID']=$req->webtoonID;
                        $res->result = $result;
                        $res->isSuccess=FALSE;
                        $res->code=203;
                        $res->message = "이미 하트를 누른 웹툰입니다(웹툰 하트를 취소하였습니다)";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postWebtoonHeart($userID,$req->webtoonID);
                    $result['유저ID']=$userID;
                    $result['웹툰ID']=$req->webtoonID;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "웹툰 하트 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }

            break;
        /*
         * API No. 8
         * API Name : MyWebtoon 추가 API
         * 마지막 수정 날짜 : 20.06.25
         */
        case "postMyWebtoon":

            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    if(!isValidWebtoon($req->webtoonID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 웹툰이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;

                    }else if (isValidMyWebtoon($req->webtoonID,$userID)){
                        deleteMyWebtoon($req->webtoonID,$userID);
                        $result['유저ID']=$userID;
                        $result['웹툰ID']=$req->webtoonID;
                        $res->result = $result;
                        $res->isSuccess=FALSE;
                        $res->code=203;
                        $res->message = "이미 관심등록한 웹툰입니다 (관심등록으로 부터 해제하였습니다)";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postMyWebtoon($req->webtoonID,$userID);
                    $result['유저ID']=$userID;
                    $result['웹툰ID']=$req->webtoonID;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "MyWebtoon 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }

            break;


        /*
         * API No. 13
         * API Name : 웹툰 컨텐츠 리스트 조회 API
         * 마지막 수정 날짜 : 20.06.16
         */
        case "getContents":
            http_response_code(200);

            $webtoonID = $vars["webtoonID"];

            if(!isValidWebtoon($webtoonID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "테스트 실패(해당웹툰이 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res->result = getContents($webtoonID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "컨텐츠 목록 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);


            break;

        /*
         * API No. 14
         * API Name : 웹툰 컨텐츠 조회 API
         * 마지막 수정 날짜 : 20.06.16
         */
        case "getContent":
            http_response_code(200);
            $contentID =$vars["contentID"];

            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당콘텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            $res->result = getContent($contentID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "콘텐츠 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 15
         * API Name : 첫번째 컨텐츠 조회 API
         * 마지막 수정 날짜 : 20.06.16
         */
        case "getFirstContent":
            http_response_code(200);
            $webtoonID =$vars["webtoonID"];

            if(!isValidWebtoon($webtoonID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당웹툰이 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            $res->result = getFirstContent($webtoonID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "첫번째 콘텐츠 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 16
         * API Name : 컨텐츠 하트 추가 API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "postContentHeart":
            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];


            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    if(!isvalidContent($req->contentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 콘텐츠가 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;

                    }else if (isValidContentHeart($req->contentID) and isValidUserContentHeart($userID)){
                        deleteContentHeart($userID,$req->contentID);
                        $result['유저ID']=$userID;
                        $result['웹툰ID']=$req->contentID;
                        $res->result = $result;
                        $res->isSuccess=FALSE;
                        $res->code=203;
                        $res->message = "이미 하트를 누른 컨텐츠입니다(컨텐츠 하트를 취소하였습니다)";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postContentHeart($userID,$req->contentID);
                    $result['유저ID']=$userID;
                    $result['컨텐츠ID']=$req->contentID;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 하트 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }

            break;

        /*
         * API No. 17
         * API Name : 웹툰 별점 추가 API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "postContentStar":


            http_response_code(200);
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];


            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    if(!isvalidContent($req->contentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 콘텐츠가 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;

                    }else if (isValidContentStar($req->contentID) and isValidUserStar($userID)){
                        $res->isSuccess=FALSE;
                        $res->code=203;
                        $res->message = "이미 별점를 누른 컨텐츠입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }else if (!isValidStar($req->stars)){
                        $req->isSuccess=FALSE;
                        $res->code=204;
                        $res->message = "잘못된 별점을 입력하였습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }

                    postContentStar($userID,$req->contentID,$req->stars);
                    $result['유저ID']=$userID;
                    $result['컨텐츠ID']=$req->contentID;
                    $result['별점']=$req->stars;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "컨텐츠 별점 추가 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }

            break;

        /*
         * API No. 18
         * API Name : 웹툰 이전 컨텐츠 조회 API
         * 마지막 수정 날짜 : 20.06.20
         */
        case "getPrevContent":
            http_response_code(200);
            $contentID =$vars["contentID"];

            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당콘텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $result = getPrevContent($contentID);
            if($result == null){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res -> message = "이전 컨텐츠가 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            $res->result = getPrevContent($contentID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "콘텐츠 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 19
         * API Name : 다음 웹툰 컨텐츠 조회 API
         * 마지막 수정 날짜 : 20.06.20
         */
        case "getNextContent":
            http_response_code(200);
            $contentID =$vars["contentID"];

            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당콘텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }


            $result = getNextContent($contentID);
            if($result == null){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res -> message = "다음 컨텐츠가 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res->result= getNextContent($contentID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "콘텐츠 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;




        /*
         * API No. 20
         * API Name : 댓글 리스트 조회 API
         * 마지막 수정 날짜 : 20.06.25
         */
        case "getComment":
            http_response_code(200);
            $contentID = $vars["contentID"];
            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당콘텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $count = getCommentCount($contentID);
            $res->result->count =  $count;
            $res->result->list = getComment($contentID);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 리스트 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 21
         * API Name : 베스트 댓글 리스트 조회 API
         * 마지막 수정 날짜 : 20.06.16
         */
        case "getBestComment":
            http_response_code(200);
            $contentID = $vars["contentID"];
            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당콘텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            $res->result = getBestComment($vars[contentID]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "베스트 댓글 리스트 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 22
         * API Name : 댓글 작성 API
         * 마지막 수정 날짜 : 20.06.17
         */
        case "postComment":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if($jwt) {
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    if(!isvalidContent($req->contentID)){
                         $res->isSuccess = FALSE;
                         $res->code = 202;
                         $res->message = "해당 컨텐츠가 없습니다";
                         echo json_encode($res, JSON_NUMERIC_CHECK);
                         return;
                     }else if(strlen($req->commentContent) > 500){
                         $res->isSuccess = FALSE;
                         $res->code = 203;
                         $res->message = "댓글 내용이 너무 깁니다";
                         echo json_encode($res, JSON_NUMERIC_CHECK);
                         return;
                     }else if(isValidComment($req->commentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 204;
                        $res->message = "이미 존재하는 댓글아이디입니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    postComment($req->commentID, $req->contentID, $userID, $req->commentContent);
                    $result['댓글ID'] = $req->commentID;
                    $result['컨텐츠ID'] = $req->contentID;
                    $result['유저ID'] = $userID;
                    $result['댓글내용'] = $req->commentContent;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 작성 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            else{
                $res->code = 201;
                $res->message = "로그인 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
        /*
         * API No. 23
         * API Name : 댓글 좋아요 API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "postCommentLike":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if($jwt) {
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    if(!isvalidComment($req->commentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 댓글이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }else if(isValidCommentLike($req->commentID,$userID)){
                        deleteCommentLike($req->commentID,$userID);
                        $result['댓글ID']=$req->commentID;
                        $result['유저ID']=$userID;
                        $res->result = $result;
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "이미 좋아요가 눌린 댓글이기 때문에 좋아요를 취소합니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }


                    postCommentLike($req->commentID,$userID);
                    $result['댓글ID']=$req->commentID;
                    $result['유저ID']=$userID;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 좋아요 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            else{
                $res->code = 204;
                $res->message = "로그인 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        /*
         * API No. 24
         * API Name : 댓글 싫어요 API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "postCommentDislike":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if($jwt) {
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    if(!isvalidComment($req->commentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 댓글이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }else if(isValidCommentDislike($req->commentID,$userID)){
                        deleteCommentDislike($req->commentID,$userID);
                        $result['댓글ID']=$req->commentID;
                        $result['유저ID']=$userID;
                        $res->result = $result;
                        $res->isSuccess = FALSE;
                        $res->code = 203;
                        $res->message = "이미 싫어요가 눌린 댓글이기 때문에 좋아요를 취소합니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }


                    postCommentDislike($req->commentID,$userID);
                    $result['댓글ID']=$req->commentID;
                    $result['유저ID']=$userID;
                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 싫어요 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            else{
                $res->code = 203;
                $res->message = "로그인 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;
        /*
         * API No. 25
         * API Name : 댓글 삭제 API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "deleteComment":
            http_response_code(200);
            $commentID = $vars["commentID"];
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if($jwt) {
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    if(!isValidComment($commentID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "해당 댓글이 없습니다";
                        echo json_encode($res, JSON_NUMERIC_CHECK);
                        return;
                    }
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    deleteComment($commentID);
                    $result['댓글ID'] = $commentID;
                    $result['유저ID'] = $userID;

                    $res->result = $result;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 삭제 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }
            else{
                $res->code = 203;
                $res->message = "로그인 필요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;




            /*http_response_code(200);
            $commentID = $vars["commentID"];

            if(!isvalidComment($commentID)){
                $res->isSuccess = FALSE;
                $res->code = 202;
                $res->message = "해당 댓글이 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            deleteComment($commentID);
            $result['댓글ID']=$commentID;

            $res->result = $result;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 삭제 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
------------------------------------------------------------------
        /*
          * API No. 26
          * API Name : 댓글 개수 조회 API -> 필요없는 API
          * 마지막 수정 날짜 : 20.06.16

        case "getCommentCount":
            http_response_code(200);
            $contentID =$vars["contentID"];

            if(!isValidContent($contentID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "해당 컨텐츠가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $res->result=getCommentCount($contentID);

            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "댓글 개수 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
*/
        /*
          * API No. 27
          * API Name : MY 웹툰 리스트 조회 API
          * 마지막 수정 날짜 : 20.06.16
          */
        case "getMyWebtoon":
            http_response_code(200);
            //$userID =$vars["userID"];
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];

            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {
                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;

                    $res->result = getMyWebtoon($userID);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "My웹툰 리스트 조회 성공";
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                return;
            }
            /*if(!validUser($userID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "테스트 실패(해당 사용자가 없습니다)";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $res->result = getMyWebtoon($userID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "My웹툰 리스트 조회 성공";*/

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        /*
         * API No. 29
         * API Name : My Webtoon 삭제하기기API
         * 마지막 수정 날짜 : 20.06.18
         */
        case "deleteMyWebtoon":
            http_response_code(200);
            //$userID =$vars["userID"];
            $jwt = $_SERVER['HTTP_X_ACCESS_TOKEN'];
            $webtoonID = $vars["webtoonID"];

            if ($jwt) {
                // jwt 유효성 검사
                if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                    $res->isSuccess = FALSE;
                    $res->code = 205;
                    $res->message = "유효하지 않은 토큰입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    addErrorLogs($errorLogs, $res, $req);

                } else {

                    $userInfo = getDataByJWToken($jwt, JWT_SECRET_KEY);
                    $userID = $userInfo->id;
                    if(!isValidMyWebtoon($webtoonID,$userID)){
                        $res->isSuccess = FALSE;
                        $res->code = 202;
                        $res->message = "mywebtoon안에 존재하지 않는 웹툰입니다.";
                        echo json_encode($res, JSON_NUMERIC_CHECK);

                        return;
                    }

                    deleteMyWebtoon($webtoonID,$userID);

                    $result['유저ID']=$userID;
                    $result['삭제된 웹툰 ID']=$webtoonID;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "My웹툰 리스트 삭제 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                    return;
                }
            }else{
                $res->code = 200;
                $res->message = "로그인이 필요합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;



        /*
          * API No. 29
          * API Name : 광고 조회 API
          * 마지막 수정 날짜 : 20.06.16
          */
        case "getAd":
            http_response_code(200);
            $adID = $vars["adID"];

            if(!isValidAd($adID)){
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res -> message = "해당 광고가 없습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            $res->result = getAd($adID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "광고 조회 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;



    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
