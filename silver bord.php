<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>silver_bulletinboard</title>
    </head>
    <body>
        <h2>好きな色を教えてください( *︾▽︾)</h2>
        <?php
        $editnum = ""; //編集用変数
        $editname = ""; //編集用変数
        $editcom = ""; //編集用変数
        $newpass = ""; //パスワード
        $tableName = "silver"; //テーブル名
        
            // DB接続設定
            $dsn = 'DBNAME';
            $user = 'USERNAME';
            $password = 'PASSWORD';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            
            //DB作成
             $sql = "CREATE TABLE IF NOT EXISTS ".$tableName //テーブル作成
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
            . "time timestamp,"
            . "pass TEXT"
            .");";
            $stmt = $pdo->query($sql);
            
            
            //投稿する
            if(!empty($_POST["name"]) && !empty($_POST["com"]) && !empty($_POST["passText"])){
                
                if(!empty($_POST["num"])){ //編集モード
                
                $id = $_POST["num"]; //変更する投稿番号
                $name = $_POST["name"];
                $comment = $_POST["com"]; //変更したい名前、変更したいコメントは自分で決めること
                $pass = $_POST["passText"];
                $sql = "UPDATE ".$tableName." SET name=:name,comment=:comment,pass=:pass,time=NOW() WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                 echo "<br>編集しました<br>";
                    
                } else { //新規登録モード
        
                //DB書き込み
                $sql = $pdo -> prepare("INSERT INTO ".$tableName." (name, comment, time, pass) VALUES (:name, :comment, NOW(), :pass)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $name = $_POST["name"];            
                $comment = $_POST["com"];
                $pass = $_POST["passText"];
                $sql -> execute();
                echo "<br>書き込み成功！<br>";
                
                }
        
            } //if(!empty($_POST["name"]))のかっこ閉じ

            //削除する
            else if(!empty($_POST["delete"]) && !empty($_POST["passDele"])){
                $id = $_POST["delete"];
                $pass = $_POST["passDele"];
                
                //削除するか判定
                $sql = "SELECT * FROM ".$tableName." where id =".$id;
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                    if($row['pass'] == $_POST["passDele"]){
                        echo "<br>削除しました<br>"; 
                    }else {
                        echo "<br>パスワードが違います<br>";
                    }
                }
                
                //削除
                $sql = "delete from ".$tableName." where id=:id and pass=:pass"; //パスワードが一致したら削除
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->execute();
                
            } //else if(!empty($_POST["delete"]))
            
            //編集する
            else if(!empty($_POST["edit"]) && !empty($_POST["passEdit"])){
                $editNum = $_POST["edit"];
                $sql = "SELECT * FROM ".$tableName." where id =".$editNum;
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                //パスワードが一致していたら
                if($_POST["passEdit"] == $row['pass']){
                    echo "<br>編集してください<br>";
                    $editnum = $row['id'];
                    $editname = $row['name'];
                    $editcom = $row['comment'];
                    $newpass = $row['pass'];
                } else {
                    echo "<br>パスワードが違います。<br>";
                }
                } //foreach ($results as $row)
            } //elseif(!empty($_POST["edit"]))
            
            else {
                echo "<br>入力してください(._.)<br>";
            }
            
           
        ?>
        <form action="" method="post">
            <input type="hidden" name="num"  placeholder="投稿番号" value=<?php echo $editnum; ?>><br>
            <input type="text" name="name"  placeholder="名前" value=<?php echo $editname; ?>><br>
            <input type="text" name="com"  placeholder="コメント" value=<?php echo $editcom; ?>><br>
            <input type="password" name="passText" placeholder="パスワード" value=<?php echo $newpass; ?>>
            <input type="submit" name="submit"><br><br>
            <input type="number" name="delete" placeholder="削除対象番号"><br>
            <input type="password" name="passDele" placeholder="パスワード">
            <button type="submit" name="dele">削除</button><br><br>
            <input type="number" name="edit" placeholder="編集対象番号"><br>
            <input type="password" name="passEdit" placeholder="パスワード">
            <button type="submit" name="editButton">編集</button>
        </form>
        <?php 
                //DB表示
                $sql = "SELECT * FROM ".$tableName;
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                foreach ($results as $row){
                //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].'  ';
                echo $row['name'].'  ';
                echo $row['comment'].'  ';
                echo $row['time'].'<br>';
                echo "<hr>";
                } 
            
        ?>
    </body>
</html>
