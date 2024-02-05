<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset = "UTF-8">
        <title>m5</title>
        【投稿一覧】
        <br>
    </head>
    <body>

<?php
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors',1);
    
    //DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //変数用意
    $id = null;
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d H:i:s");
    $password = $_POST["password"];
    $deletePassword = $_POST["deletePassword"];
    $editPassword = $_POST["editPassword"];
    $editName = "";
    $editComment = "";
    $editNumber = "";
    
    //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS BOARD1"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "password char(255)"
    .");";
    $stmt = $pdo->query($sql);
    
    //テーブル表示
    /*$sql = 'SHOW TABLES';
    $result = $pdo -> query($sql);
    foreach($result as $row){
        echo $row[0];
        echo '<br>';*/
        
    //データレコード表示    
    /*$sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].'<br>';
    echo "<hr>";
    }
    }*/
?>





<?php
    
    //投稿(新規または編集)
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])){
        //編集投稿
        if(!empty($_POST["editPost"])){
            $hiddenNum = $_POST["editPost"];
            
            $sql = "UPDATE BOARD1 SET name=:name, comment=:comment, password=:password WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $hiddenNum, PDO::PARAM_INT);
            $stmt->execute();
        }
        //新規投稿
        else{
            $sql = "INSERT INTO BOARD1 (id, name, comment, date, password) VALUES (:id, :name, :comment, :date, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    
            $stmt->execute();
        }
    }
    
    //削除処理
    if(!empty($_POST["deleteNum"]) && !empty($_POST["deletePassword"])){
        $deleteNum = $_POST["deleteNum"];
        $deletePassword = $_POST["deletePassword"];
            
        //データベースからパスワードを取得
        $sql = 'SELECT password FROM BOARD1 WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $deleteNum, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
            
        if($result){
        //パスワードが一致したときに削除を実行
            if($deletePassword == $result["password"]){
            $sql = 'DELETE FROM BOARD1 WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $deleteNum, PDO::PARAM_INT);
            $stmt->execute();
            }else{
                echo "パスワードが一致しません。<br>";
            }
        } else {
            echo "指定された投稿は存在しません。<br>";
        }
    }
    
    //編集番号取得
    if(!empty($_POST["editNum"]) && !empty($_POST["editPassword"])){
        
        $editNum = $_POST["editNum"];
        
        $sql = "SELECT * FROM BOARD1 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(":id", $editNum, PDO::PARAM_INT);
        $stmt -> execute();
        $result = $stmt->fetch();

        if ($result && $result["password"] == $_POST["editPassword"]) {
            $editName = $result["name"];
            $editComment = $result["comment"];
            $editNumber = $result["id"];
        }
        
    }
    
    $sql = "SELECT * FROM BOARD1";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();
    $results = $stmt -> fetchAll();
    foreach($results as $result){
        echo $result["id"] . " " . $result["name"] . " " . $result["comment"] . " " . $result["date"] . "<hr>";
    }
    
?>
    
    
        <form action="" method="post">
        【投稿フォーム】<br>
        <input type="text" name="name" value="<?php echo $editName; ?>" placeholder = "名前">
        <input type="text" name="comment" size = "40" value="<?php echo $editComment; ?>" placeholder = "コメント"　>
        <input type="password" name="password" placeholder = "パスワードを入力してください">
        <input type="hidden" name="editPost" value="<?php echo $editNumber; ?>">
        <input type="submit" name="submit">
        <br>
        </form>
        <form action="" method = "post">
        【削除フォーム】<br>
        <input type="number" name="deleteNum" placeholder="削除対象番号">
        <input type="password" name="deletePassword" placeholder = "パスワード">
        <input type="submit" name="delete" value="削除">
        <br>
        </form>
        <form action="" method = "post">
        【編集フォーム】<br>
        <input type="number" name="editNum" placeholder="編集対象番号">
        <input type="password" name="editPassword" placeholder = "パスワード">
        <input type="submit" name="edit" value="編集する">
        </form>
        <hr>

    
    </body>
    
    
</html>
