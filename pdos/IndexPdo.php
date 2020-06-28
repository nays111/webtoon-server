<?php

//READ
function getWebtoonData()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM webtoon;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUser(){
    $pdo=pdoSqlConnect();
    $query = "SELECT * FROM user;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



function postUser($userID,$pw,$userName,$userNickname,$age,$gender){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO user(userID,pw,userName,userNickname,age,gender) VALUES (?,?,?,?,?,?);";
    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw,$userName,$userNickname,$age,$gender]);


    $st = null;
    $pdo = null;


}

function login($userID,$pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT userID as 유저ID,userName as 이름, userNickname as 닉네임 FROM user WHERE userID=? and pw=?";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getWebtoonsAll(){
    $pdo = pdoSqlConnect();
    $query = "
    select 
       webtoon.webtoonID                                                                           as 웹툰ID,
       webtoon.webtoonTitle                                                                        as 웹툰제목,
       webtoon.webtoonThumbNailURL                                                                 as 웹툰썸네일,
       author.authorName as 작가이름,
       (case
            when
                round(sum(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end) / count(content.contentID), 2) is null then 0
            else round(sum(
                               case
                                   when contentStar.stars is null
                                       then 0
                                   else contentStar.stars end) / count(content.contentID), 2) end) as 별점,#소수점 2자리만 남겨야하기 때문에 round를 사용하여
       webtoon.webtoonDay                                                                          as 웹툰요일#,max(content.createdAt)
from webtoon
         left outer join content on webtoon.webtoonID = content.webtoonID
         left outer join contentStar on content.contentID = contentStar.contentID#별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
         inner join author on author.authorID = webtoon.authorID

group by webtoon.webtoonID
order by max(content.createdAt) desc;
#max(content.createdAt)는 어떠한 웹툰 중에서 가장 최근에 생성된 컨텐츠를 이야기한다.";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    //$st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    //$st->bindParam(':aname', $authorName);
    //$st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function getWebtoonsTitle($webtoonTitle){
    $pdo = pdoSqlConnect();
    $query = "
    select 
       webtoon.webtoonID                                                                           as 웹툰ID,
       webtoon.webtoonTitle                                                                        as 웹툰제목,
       webtoon.webtoonThumbNailURL                                                                 as 웹툰썸네일,
       author.authorName as 작가이름,
       (case
            when
                round(sum(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end) / count(content.contentID), 2) is null then 0
            else round(sum(
                               case
                                   when contentStar.stars is null
                                       then 0
                                   else contentStar.stars end) / count(content.contentID), 2) end) as 별점,#소수점 2자리만 남겨야하기 때문에 round를 사용하여
       webtoon.webtoonDay                                                                          as 웹툰요일#,max(content.createdAt)
from webtoon
         left outer join content on webtoon.webtoonID = content.webtoonID
         left outer join contentStar on content.contentID = contentStar.contentID#별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
         inner join author on author.authorID = webtoon.authorID
where webtoon.webtoonTitle like concat('%',?,'%')
group by webtoon.webtoonID
order by max(content.createdAt) desc;
#max(content.createdAt)는 어떠한 웹툰 중에서 가장 최근에 생성된 컨텐츠를 이야기한다.";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$webtoonTitle]);
    //$st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    //$st->bindParam(':aname', $authorName);
    //$st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function getWebtoonsAuthor($authorName){
    $pdo = pdoSqlConnect();
    $query = "
    select 
       webtoon.webtoonID                                                                           as 웹툰ID,
       webtoon.webtoonTitle                                                                        as 웹툰제목,
       webtoon.webtoonThumbNailURL                                                                 as 웹툰썸네일,
       author.authorName as 작가이름,
       (case
            when
                round(sum(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end) / count(content.contentID), 2) is null then 0
            else round(sum(
                               case
                                   when contentStar.stars is null
                                       then 0
                                   else contentStar.stars end) / count(content.contentID), 2) end) as 별점,#소수점 2자리만 남겨야하기 때문에 round를 사용하여
       webtoon.webtoonDay                                                                          as 웹툰요일#,max(content.createdAt)
from webtoon
         left outer join content on webtoon.webtoonID = content.webtoonID
         left outer join contentStar on content.contentID = contentStar.contentID#별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
         inner join author on author.authorID = webtoon.authorID
where author.authorName like concat('%',?,'%')
group by webtoon.webtoonID
order by max(content.createdAt) desc;
#max(content.createdAt)는 어떠한 웹툰 중에서 가장 최근에 생성된 컨텐츠를 이야기한다.";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$authorName]);
    //$st->setFetchMode(PDO::FETCH_ASSOC);
    //$res = $st->fetchAll();

    //$st->bindParam(':aname', $authorName);
    //$st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function getWebtoonsDay($webtoonDay)
{
    $pdo = pdoSqlConnect();
    $query = "
select 
        webtoon.webtoonID                                                                           as 웹툰ID,
        webtoon.webtoonTitle                                                                        as 웹툰제목,
       webtoon.webtoonThumbNailURL                                                                 as 웹툰썸네일,
       author.authorName                                                                           as 작가,
       (case
            when
                round(sum(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end) / count(content.contentID), 2) is null then 0
            else round(sum(
                               case
                                   when contentStar.stars is null
                                       then 0
                                   else contentStar.stars end) / count(content.contentID), 2) end) as 별점,#소수점 2자리만 남겨야하기 때문에 round를 사용하여
       webtoon.webtoonDay                                                                          as 웹툰요일#,max(content.createdAt)
from webtoon
         left outer join content on webtoon.webtoonID = content.webtoonID
         left outer join contentStar
                         on content.contentID = contentStar.contentID #별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
         inner join author on author.authorID = webtoon.authorID
where webtoon.webtoonDay = :wday
group by webtoon.webtoonID
order by max(content.createdAt) desc;";

    $st = $pdo->prepare($query);

    $st->bindParam(':wday', $webtoonDay);
    $st->execute();


    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}

function getWebtoonsDayStar($webtoonDay)
{
    $pdo = pdoSqlConnect();
    $query = "
select 
        webtoon.webtoonID                                                                           as 웹툰ID,
        webtoon.webtoonTitle                                                                        as 웹툰제목,
       webtoon.webtoonThumbNailURL                                                                 as 웹툰썸네일,
       author.authorName                                                                           as 작가,
       (case
            when
                round(sum(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end) / count(content.contentID), 2) is null then 0
            else round(sum(
                               case
                                   when contentStar.stars is null
                                       then 0
                                   else contentStar.stars end) / count(content.contentID), 2) end) as 별점,#소수점 2자리만 남겨야하기 때문에 round를 사용하여
       webtoon.webtoonDay                                                                          as 웹툰요일#,max(content.createdAt)
from webtoon
         left outer join content on webtoon.webtoonID = content.webtoonID
         left outer join contentStar
                         on content.contentID = contentStar.contentID #별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
         inner join author on author.authorID = webtoon.authorID
where webtoon.webtoonDay = :wday
group by webtoon.webtoonID
order by 별점 desc;";

    $st = $pdo->prepare($query);

    $st->bindParam(':wday', $webtoonDay);
    $st->execute();


    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}

function getWebtoonsDayHistory($webtoonDay)
{
    $pdo = pdoSqlConnect();
    $query = "
select 조회수고른테이블.웹툰제목  as 웹툰제목
     , 조회수고른테이블.웹툰썸네일 as 웹툰썸네일
     , 별나타내는테이블.별점    as 웹툰평점
     , 별나타내는테이블.작가    as 웹툰작가
     , 별나타내는테이블.웹툰요일  as 웹툰요일
     , 조회수고른테이블.총조회수  as 웹툰조회수_생략
from (select webtoon.webtoonID           as 아이디,
             webtoon.webtoonTitle        as 웹툰제목,
             webtoon.webtoonThumbNailURL as 웹툰썸네일,
             webtoon.webtoonID,
             ifnull(sum(ci.c), 0)        as 총조회수
      from (select content.webtoonID, content.contentID, count(history.contentID) as c
            from history
                     right outer join content using (contentID)
            group by contentID) as ci
               right outer join webtoon using (webtoonID)
      group by webtoon.webtoonID) as 조회수고른테이블
         inner join
     (select webtoon.webtoonID  as 아이디,
             round(avg(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                           when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                               then 0 #0이라고 가정한상태로 평균을 구한다.
                           else contentStar.stars
                 end), 2)       as 별점,
             author.authorName  as 작가,
             webtoon.webtoonDay as 웹툰요일
      from webtoon
               left outer join content on webtoon.webtoonID = content.webtoonID
               left outer join contentStar
                               on content.contentID = contentStar.contentID #별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
               inner join author on author.authorID = webtoon.authorID
      group by webtoon.webtoonID) as 별나타내는테이블
     on 조회수고른테이블.아이디 = 별나타내는테이블.아이디
where 별나타내는테이블.웹툰요일 = :wday
order by 조회수고른테이블.총조회수 desc;";

    $st = $pdo->prepare($query);

    $st->bindParam(':wday', $webtoonDay);
    $st->execute();


    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}

function postWebtoonHeart($userID,$webtoonID){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO webtoonHeart(userID,webtoonID) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$webtoonID]);

    $st = null;
    $pdo = null;

}

function deleteWebtoonHeart($userID,$webtoonID){
    $pdo = pdoSqlConnect();
    $query = "DELETE FROM webtoonHeart WHERE userID=? AND webtoonID=?;";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$webtoonID]);

    $st = null;
    $pdo = null;

}



function postMyWebtoon($webtoonID,$userID){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO myWebtoon(webtoonID,userID) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$webtoonID,$userID]);

    $st = null;
    $pdo = null;
}

function deleteMyWebtoon($webtoonID,$userID){
    $pdo = pdoSqlConnect();
    $query = "DELETE FROM myWebtoon WHERE webtoonID=? AND userID=?;";

    $st = $pdo->prepare($query);
    $st->execute([$webtoonID,$userID]);

    $st = null;
    $pdo = null;
}

function postContentHeart($userID,$contentID){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO contentHeart(userID,contentID) VALUES (?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$contentID]);

    $st = null;
    $pdo = null;
}

function deleteContentHeart($userID,$contentID){
    $pdo = pdoSqlConnect();
    $query = "DELETE FROM contentHeart WHERE userID=? AND contentID=?;";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$contentID]);

    $st = null;
    $pdo = null;
}

function postContentStar($userID,$contentID,$stars){
    $pdo=pdoSqlConnect();
    $query = "INSERT INTO contentStar(userID,contentID,stars) VALUES (?,?,?)";

    $st = $pdo->prepare($query);
    $st->execute([$userID,$contentID,$stars]);

    $st = null;
    $pdo = null;
}



function updateStar($stars,$userID,$contentID){
    $pdo=pdoSqlConnect();
    $query = "UPDATE contentStar SET stars=? WHERE userID=? AND contentID=?";

    $st = $pdo->prepare($query);
    $st->execute([$stars,$userID,$contentID]);

    $st = null;
    $pdo = null;
}

function getContents($webtoonID){
    $pdo = pdoSqlConnect();
    $query = "
    select distinct content.contentTitle                       as 컨텐츠제목,
                content.contentID as 컨텐츠ID,
                contentThumbNailURL                        as 컨텐츠썸네일,
                date_format(content.createdAt, '%Y.%m.%d') as 작성날짜,
                round(avg(case #사용자들이 각각 콘텐츠에 대해 매긴 별점의 평균
                              when contentStar.stars is null #콘텐츠에 대한 별점을 매긴 사용자가 없을 경우
                                  then 0 #0이라고 가정한상태로 평균을 구한다.
                              else contentStar.stars
                    end), 2)                               as 별점 #소수점 2자리만 남겨야하기 때문에 round를 사용하여
    from content
             left outer join contentStar on content.contentID = contentStar.contentID #별점 매긴 사용자가 없을 수도 있으므로 left outer join을 사용
    where content.webtoonID = ?
    group by content.contentID
    order by 작성날짜 desc;
    ";
    $st = $pdo->prepare($query);
    $st->execute([$webtoonID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


function getContent($contentID){
    $pdo=pdoSqlConnect();
    $query="
    select A.contentID as 컨텐츠ID, A.contentTitle as 컨텐츠제목, A.contentURL as 컨텐츠화면, A.댓글수 as 댓글개수, B.하트 as 하트개수
    from (select content.contentID, content.contentTitle, content.contentURL, count(comment.commentID) as 댓글수
          from content
                   left outer join comment using (contentID)
          group by content.contentID) as A
             inner join
         (select count(contentHeart.userID) as 하트, content.contentID
          from content
                   left outer join contentHeart using (contentID)
          group by content.contentID) as B on A.contentID = B.contentID
    where A.contentID = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getPrevContent($contentID){
    $pdo=pdoSqlConnect();
    $query="
    select A.contentID as 컨텐츠ID, A.contentTitle as 컨텐츠제목, A.contentURL as 컨텐츠화면, A.댓글수 as 댓글개수, B.하트 as 하트개수
    from (select content.contentID, content.contentTitle, content.contentURL, count(comment.commentID) as 댓글수
          from content
                   left outer join comment using (contentID)
          group by content.contentID) as A
             inner join
         (select count(contentHeart.userID) as 하트, content.contentID
          from content
                   left outer join contentHeart using (contentID)
          group by content.contentID) as B on A.contentID = B.contentID
    where A.contentID = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID-1]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getNextContent($contentID){
    $pdo=pdoSqlConnect();
    $query="
    select A.contentID as 컨텐츠ID, A.contentTitle as 컨텐츠제목, A.contentURL as 컨텐츠화면, A.댓글수 as 댓글개수, B.하트 as 하트개수
    from (select content.contentID, content.contentTitle, content.contentURL, count(comment.commentID) as 댓글수
          from content
                   left outer join comment using (contentID)
          group by content.contentID) as A
             inner join
         (select count(contentHeart.userID) as 하트, content.contentID
          from content
                   left outer join contentHeart using (contentID)
          group by content.contentID) as B on A.contentID = B.contentID
    where A.contentID = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID+1]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function webtoonSearch($input){
    $pdo=pdoSqlConnect();
    $str="%".$input."%";
    $query = "SELECT * FROM webtoon WHERE webtoonTitle like :webtoonTitle";
    $st = $pdo->prepare($query);
    $st->bindParam(':webtoonTitle',$str);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function authorSearch($input){
    $pdo=pdoSqlConnect();
    $str="%".$input."%";
    $query = "SELECT * FROM author INNER JOIN webtoon USING(authorID) WHERE authorName like :authorName";
    $st = $pdo->prepare($query);
    $st->bindParam(':authorName',$str);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}



function getFirstContent($webtoonID){
    $pdo=pdoSqlConnect();
    $query="
        select webtoon.webtoonID,A.contentID as 컨텐츠ID, A.contentTitle as 컨텐츠제목, A.contentURL as 컨텐츠화면, A.댓글수 as 댓글개수, B.하트 as 하트개수
        from (select content.webtoonID,content.createdAt,content.contentID, content.contentTitle, content.contentURL, count(comment.commentID) as 댓글수
              from content
                       inner join comment using (contentID)
              group by content.contentID) as A
                 inner join
             (select count(contentHeart.userID) as 하트, content.contentID
              from content
                       inner join contentHeart using (contentID)
              group by content.contentID) as B on A.contentID = B.contentID inner join webtoon using(webtoonID)
        WHERE webtoonID=?
        ORDER BY A.createdAt
        limit 1;";
    $st = $pdo->prepare($query);
    $st->execute([$webtoonID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getComment($contentID){
    $pdo = pdoSqlConnect();
    $query = "select 
                        U.userID as 사용자ID, C.commentID as 댓글ID,
                        U.userNickname                                                                             as 사용자닉네임,
                       C.commentContent                                                                           as 댓글내용,
                       date_format(C.createdAt, '%Y-%m-%d %H:%m')                                                 as 댓글작성날짜,
                       (select count(commentID)
                        from commentLike
                        where C.commentID = commentLike.commentID and commentLike.isDeleted='N')                 as 좋아요개수, #null은 count하지 않기 위해서 count(컬럼)
                       (select count(commentID) from commentDislike where C.commentID = commentDislike.commentID) as 싫어요개수
                from comment as C
                         inner join user as U on C.userID = U.userID
                where C.contentID = ?
                group by C.commentID
                order by 댓글작성날짜 desc;";

    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getBestComment($contentID){
    $pdo = pdoSqlConnect();
    $query = "select    U.userID as 사용자ID, C.commentID as 댓글ID,
                        U.userNickname                                                                             as 사용자닉네임,
                       C.commentContent                                                                           as 댓글내용,
                       date_format(C.createdAt, '%Y-%m-%d %H:%m')                                                 as 댓글작성날짜,
                       (select count(commentID)
                        from commentLike
                        where C.commentID = commentLike.commentID and commentLike.isDeleted='N')               as 좋아요개수, #null은 count하지 않기 위해서 count(컬럼)
                       (select count(commentID) from commentDislike where C.commentID = commentDislike.commentID) as 싫어요개수
                from comment as C
                         inner join user as U on C.userID = U.userID
                where C.contentID = ?
                group by C.commentID
                order by 좋아요개수 desc;";

    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}




//READ
function testDetail($testNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Test WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$testNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function testPost($name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Test (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$name]);

    $st = null;
    $pdo = null;

}

function showComment(){
    $pdo=pdoSqlConnect();
    $query = "SELECT * FROM comment;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
function postComment($commentID,$contentID,$userID,$commentContent){
    $pdo=pdoSqlConnect();
    //유효한 contentID인지 유효한 userID인지 판단해야함
    $query="INSERT INTO comment(commentID,contentID,userID,commentContent) VALUES (?,?,?,?)";
    $st=$pdo->prepare($query);
    $st->execute([$commentID,$contentID,$userID,$commentContent]);
    $st=null;
    $pdo=null;
}

function getCommentCount($contentID){
    $pdo = pdoSqlConnect();
    $query = "
    select count(*) as 댓글개수
    from comment inner join content using(contentID)
    where contentID=?
    group by contentID;";
    $st=$pdo->prepare($query);
    $st->execute([$contentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function deleteUser($userID){
    $pdo = pdoSqlConnect();
    $query = "DELETE FROM user WHERE userID=?";
    $st=$pdo->prepare($query);
    $st->execute([$userID]);
    $st=null;
    $pdo=null;
}


function deleteComment($commentID){
    $pdo = pdoSqlConnect();
    $query = "DELETE FROM comment WHERE commentID=?";
    $st=$pdo->prepare($query);
    $st->execute([$commentID]);
    $st=null;
    $pdo=null;
}


function postCommentLike($commentID,$userID){
    $pdo=pdoSqlConnect();
    $query="INSERT INTO commentLike(commentID,userID) VALUES (?,?)";
    $st=$pdo->prepare($query);
    $st->execute([$commentID,$userID]);
    /*$st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();*/


   /* if($res["commentID"]) {
        $st=$pdo->prepare("UPDATE commentLike SET isDeleted='Y' WHERE userID=? and commentID=?");
        $st->execute([$userID,$commentID]);
        $message = "댓글 좋아요 취소";

    }else{
        $st=$pdo->prepare("INSERT INTO commentLike(commentID,userID) VALUES (?,?)");
        $st->execute($userID,$commentID);
        $message = "댓글 좋아요 누름";
    }*/
    $st = null;
    $pdo = null;
}

function deleteCommentLike($commentID,$userID){
    $pdo=pdoSqlConnect();
    $query="DELETE FROM commentLike WHERE commentID=? AND userID=?;";
    $st=$pdo->prepare($query);
    $st->execute([$commentID,$userID]);
    $st = null;
    $pdo = null;
}

function isValidCommentLike($commentID,$userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM commentLike WHERE commentID=? AND userID=?) AS validCommentLike;";
    $st = $pdo->prepare($query);
    $st->execute([$commentID,$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validCommentLike"]);
}


function postCommentDislike($commentID,$userID)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO commentDislike(commentID,userID) VALUES (?,?)";
    $st = $pdo->prepare($query);
    $st->execute([$commentID, $userID]);

}
function deleteCommentDislike($commentID,$userID){
    $pdo=pdoSqlConnect();
    $query="DELETE FROM commentDislike WHERE commentID=? AND userID=?;";
    $st=$pdo->prepare($query);
    $st->execute([$commentID,$userID]);
    $st = null;
    $pdo = null;
}

function isValidCommentDislike($commentID,$userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM commentDislike WHERE commentID=? AND userID=?) AS validCommentDislike;";
    $st = $pdo->prepare($query);
    $st->execute([$commentID,$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validCommentDislike"]);
}



function getMyWebtoon($userID){
    $pdo=pdoSqlConnect();
    $query = "
    select myWebtoon.userID as 유저ID,webtoon.webtoonID as 웹툰ID,webtoonTitle as 웹툰제목,webtoonThumbNailURL as 웹툰썸네일,
           (select MAX(date_format(content.createdAt, '%Y.%m.%d'))
            from content
            where webtoon.webtoonID = content.webtoonID) as 작성날짜
    from myWebtoon inner join webtoon using(webtoonID)
    where myWebtoon.userID= ? ;";

    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function getAd($adID){
    $pdo=pdoSqlConnect();
    $query = "SELECT  adID as 광고ID, addURL as 광고URL FROM advertise WHERE adID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$adID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function validUser($userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE userID= ?) AS validUser;";
    $st = $pdo -> prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validUser"]);
}

function getWebtoonID($webtoonTitle){
    $pdo = pdoSqlConnect();
    $query = "SELECT webtoonID FROM webtoon WHERE webtoonTitle like concat('%',?,'%');";
    $st = $pdo->prepare($query);
    $st->execute([$webtoonTitle]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function getAuthorID($authorName){
    $pdo = pdoSqlConnect();
    $query = "SELECT authorID FROM author WHERE authorName like concat('%',?,'%');";
    $st = $pdo->prepare($query);
    $st->execute([$authorName]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]);
}

function isValidWebtoon($webtoonID){
    $pdo=pdoSqlConnect();
    $query="SELECT EXISTS(SELECT * FROM webtoon WHERE webtoonID= ?) AS validWebtoon;";
    $st = $pdo -> prepare($query);
    $st->execute([$webtoonID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validWebtoon"]);
}

function isValidContent($contentID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM content WHERE contentID=?) AS validContent;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validContent"]);
}

function isValidComment($commentID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM comment WHERE commentID=?) AS validComment;";
    $st = $pdo->prepare($query);
    $st->execute([$commentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validComment"]);
}

function isValidUserHeart($userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM webtoonHeart WHERE userID=?) AS validUserHeart;";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validUserHeart"]);
}
function isValidUserContentHeart($userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM contentHeart WHERE userID=?) AS validUserHeart;";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validUserHeart"]);
}

function isValidWebtoonHeart($webtoonID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM webtoonHeart WHERE webtoonID=?) AS validWebtoonHeart;";
    $st = $pdo->prepare($query);
    $st->execute([$webtoonID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validWebtoonHeart"]);
}
function isValidContentHeart($contentID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM contentHeart WHERE contentID=?) AS validContentHeart;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validContentHeart"]);
}

function isValidContentStar($contentID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM contentStar WHERE contentID=?) AS validContentStar;";
    $st = $pdo->prepare($query);
    $st->execute([$contentID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validContentStar"]);
}

function isValidUserStar($userID){
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM contentStar WHERE userID=?) AS validUserStar;";
    $st = $pdo->prepare($query);
    $st->execute([$userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;
    $pdo = null;

    return intval($res[0]["validUserStar"]);
}

function isValidMyWebtoon($webtoonID,$userID){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM myWebtoon WHERE webtoonID=? AND userID=?) AS validMyWebtoon;";
    $st = $pdo->prepare($query);
    $st->execute([$webtoonID, $userID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return intval($res[0]["validMyWebtoon"]);
}


function isValidUser($userID, $pw){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM user WHERE userID= ? AND pw = ?) AS exist;";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userID, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);

}

function isValidAd($adID){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM advertise WHERE adID= ?) AS existAd;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$adID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return intval($res[0]["existAd"]);
}


// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
