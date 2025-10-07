<?php
date_default_timezone_set("Asia/Tokyo");

$comment_array = array();

$pdo = null;
$stmt = null;
$error_messages = array();

try {
    $pdo = new PDO("mysql:host=localhost;charset=UTF8;dbname=phpbbs", "root", "admin");
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (!empty($_POST["submitButton"])) {
    $username = $_POST["username"];
    $comment = $_POST["comment"];

    $username = htmlspecialchars($username);
    $comment = htmlspecialchars($comment);

    if (empty($username)) {
        echo "名前を入力してください。";
        $error_messages["username"] = "名前を入力してください。";
    }

    if (empty($comment)) {
        echo "コメントを入力してください。";
        $error_messages["comment"] = "コメントを入力してください。";
    }

    if (empty($error_messages)) {
        $postDate = date("Y-m-d H:i:s");
    
        try {
            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`username`, `comment`, `postDate`) VALUES (:username, :comment, :postDate);");
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
            $stmt->bindParam(":postDate", $postDate, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

$sql = "SELECT * FROM `bbs-table`";
$comment_array = $pdo->query($sql);

$pdo = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ch風アプリ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="title">2ch風掲示板アプリ</h1>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach ($comment_array as $comment): ?>
            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <span>名前：</span>
                        <p class="username"><?php echo $comment["username"]; ?></p>
                        <time> : <?php echo $comment["postDate"]; ?></time>
                    </div>
                    <p class="comment"><?php echo $comment["comment"]; ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </section>
        <form action="" method="post" class="formWrapper">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="usernameLabel">名前：</label>
                <input type="text" name="username" id="usernameLabel">
            </div>
            <div>
                <textarea name="comment" id="comment" class="commentTextarea"></textarea>
            </div>
        </form>
    </div>
</body>
</html>