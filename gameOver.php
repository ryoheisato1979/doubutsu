<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>どうぶつゲット</title>
  <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru|Paytone+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/earlyaccess/nicomoji.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

  <!-- fontawesome -->
  <script src="https://kit.fontawesome.com/6c845a5a9e.js" crossorigin="anonymous"></script>
</head>

<body>
  <div class="game-wrap">
    <!-- ここからゲームコンテナ -->
    <div class="game-container">
      <div class="game-over">
        <form method="post" action="index.php">
          <h2>GAME OVER</h2>
          <input type="submit" name="start" value="▶ゲームスタート">
          <img src="img/pose_lose_boy.png">
        </form>
      </div>
    </div>
  </div>
</body>

</html>