<?php
ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');

session_start();
unset($_SESSION['history']);

$animals = array();

class Sex
{
    const MAN = 1;
    const WOMAN = 2;
}

abstract class Creature
{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;
    protected $cry;
    abstract public function sayCry();
    public function setName($str)
    {
        $this->name = $str;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setHp($num)
    {
        $this->hp = $num;
    }
    public function getHp()
    {
        return $this->hp;
    }

    public function setCry($str)
    {
        $this->cry = $str;
    }

    public function getCry()
    {
        return $this->cry;
    }

    public function attack($targetObj)
    {
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        if (!mt_rand(0, 9)) {
            $attackPoint = $attackPoint * 2;
            $attackPoint = (int) $attackPoint;
            History::set($this->getName() . 'のクリティカルヒット');
        }
        $targetObj->setHp($targetObj->getHp() - $attackPoint);
        History::set($attackPoint . 'のダメージ');
    }

    public function defense()
    {
        History::set($this->getName() . 'は防御した');
    }
}

// class Human extends Creature {
//     public function __construct($name, $hp, $attackMin, $attackMax){
//         $this->name = $name;
//         $this->hp = $hp;
//         $this->attackMin = $attackMin;
//         $this->attackMax = $attackMax;
//     }
//     public function sayCry(){
//             History::set('イタッ');
//         }
//     }

class Human extends Creature
{
    protected $sex;
    public function __construct($name, $sex, $hp, $attackMin, $attackMax, $cry)
    {
        $this->name = $name;
        $this->sex = $sex;
        $this->hp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
        $this->cry = $cry;
    }
    public function setSex($num)
    {
        $this->sex = $num;
    }
    public function getSex()
    {
        return $this->sex;
    }
    public function sayCry()
    {
        History::set($this->name . 'が叫ぶ！');
        History::set($this->cry);
    }
}

class Animal extends Creature
{
    protected $img;
    public function __construct($name, $img, $hp, $attackMin, $attackMax, $cry)
    {
        $this->name = $name;
        $this->img = $img;
        $this->hp = $hp;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
        $this->cry = $cry;
    }
    public function getImg()
    {
        return $this->img;
    }
    public function sayCry()
    {
        History::set($this->name . 'が吠える');
        History::set($this->cry);
    }
}

interface HistoryInterface
{
    public static function set($str);
    public static function clear();
}

class History implements HistoryInterface
{
    public static function set($str)
    {
        if (empty($_SESSION['history'])) $_SESSION['history'] = '';
        $_SESSION['history'] .= $str . '<br>';
    }
    public static function clear()
    {
        unset($_SESSION['history']);
    }
}

$human = new Human('どうぶつハンター', Sex::MAN, 500, 50, 200, 'うぅっ');
$animals[] = new Animal('ゾウ', 'img/animal_zou.png', 600, 60, 250, 'パオォーン');
$animals[] = new Animal('アライグマ', 'img/animal_araiguma_side.png', 100, 20, 80, 'ギューッ');
$animals[] = new Animal('クマ', 'img/animal_bear_america_kurokuma.png', 500, 50, 180, 'グワァー');
$animals[] = new Animal('パンダ', 'img/animal_bear_panda.png', 300, 20, 150, 'グォォ');
$animals[] = new Animal('ゴリラ', 'img/animal_gorilla_drumming.png', 400, 50, 180, 'ン゛ッン゛ー');
$animals[] = new Animal('ヒョウ', 'img/animal_hyou_panther.png', 250, 50, 140, 'シャァー');
$animals[] = new Animal('マンモス', 'img/animal_mammoth.png', 800, 60, 300, 'バオォ〜');
$animals[] = new Animal('キツネ', 'img/animal_fox_kitsune.png', 100, 10, 50, 'ギャッ');
$animals[] = new Animal('タヌキ', 'img/animal_tanuki.png', 100, 10, 50, 'グルルル');
$animals[] = new Animal('オオカミ', 'img/animal_ookami_tooboe.png', 200, 30, 120, 'オォーン');
$animals[] = new Animal('ネコ', 'img/monogatari_alice_cheshire_neko.png', 100, 10, 50, 'シャァー');
$animals[] = new Animal('リス', 'img/shoes_32.png', 50, 10, 40, 'キュゥ');



function createAnimal()
{
    global $animals;
    $animal = $animals[mt_rand(0, 11)];
    History::set('▶︎' . $animal->getName() . 'が現れた');
    $_SESSION['animal'] = $animal;
}
function createHuman()
{
    global $human;
    $_SESSION['human'] = $human;
}
function init()
{
    History::clear();
    History::set('▶︎ゲームスタート！');
    $_SESSION['knockDownCount'] = 0;
    createHuman();
    createAnimal();
}
function gameOver()
{
    $_SESSION = array();
    header("Location:gameOver.php");
}


if (!empty($_POST)) {
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $defenseFlg = (!empty($_POST['defense'])) ? true : false;
    $escapeFlg = (!empty($_POST['escape'])) ? true : false;

    error_log('POSTされました。');

    if ($startFlg) {
        History::set('ゲームスタート');
        init();
    } else {
        if ($attackFlg) {
            History::set($_SESSION['human']->getName() . 'の攻撃');
            $_SESSION['human']->attack($_SESSION['animal']);
            $_SESSION['animal']->sayCry();

            if ($_SESSION['animal']->getHp() <= 0) {
                History::set($_SESSION['animal']->getName() . 'を倒した');
                createAnimal();
                $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
            } else {

                History::set($_SESSION['animal']->getName() . 'の攻撃');
                $_SESSION['animal']->attack($_SESSION['human']);
            }


            if ($_SESSION['human']->getHp() <= 0) {
                gameOver();
            } else {
                if ($_SESSION['animal']->getHp() <= 0) {
                    History::set($_SESSION['animal']->getName() . 'を倒した');
                    createAnimal();
                    $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
                }
            }
        } elseif ($escapeFlg) {
            History::set($_SESSION['human']->getName() . 'は逃げ出した');
            if (mt_rand(0, 1)) {
                History::set('しかし、逃げられなかった！');
                History::set($_SESSION['animal']->getName() . 'の攻撃');
                $_SESSION['animal']->attack($_SESSION['human']);
                $_SESSION['human']->sayCry();

                if ($_SESSION['human']->getHp() <= 0) {
                    gameOver();
                }
            } else {
                createAnimal();
            }
        } elseif ($defenseFlg) {
            $_SESSION['human']->defense();
            History::set($_SESSION['animal']->getName() . 'の攻撃');
            History::set($_SESSION['human']->getName() . 'はダメージを受けない！');
        }
    }
    $_POST = array();
}


?>


<!DOCTYPE html>
<html lang="ja">

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
  <!-- ここからゲームラップ -->
  <div class="game-wrap">
    <!-- ここからゲームコンテナ -->

    <div class="game-container">

      <?php if (empty($_SESSION)) { ?>

      <!-- ここからゲームスタート画面 -->
      <div class="game-start">
        <form method="post">
          <h2>どうぶつゲット
          </h2>
          <input type="submit" name="start" value="▶ゲームスタート">
          <img src="img/tree_animals_group.png">
        </form>

      </div>
      <!-- ここまでゲームスタート画面 -->
      <?php } else { ?>

      <!-- ここからアニマルコンテナ -->
      <div class="animal-container">
        <img src="img/bg_natural_mori2.jpg" alt="">
        <!-- ここからアニマルレフト -->
        <div class="animal-left">
          <p><?php echo $_SESSION['animal']->getName(); ?> 　 <i class="fas fa-heart"></i>
            <?php echo $_SESSION['animal']->getHp(); ?></p>
        </div>
        <!-- ここまでアニマルレフト -->
        <!-- ここからアニマルライト -->
        <div class="animal-right">
          <img src="<?php echo $_SESSION['animal']->getImg(); ?>">
        </div>
        <!-- ここまでアニマルコンテナ -->
      </div>

      <!-- ここからプレイヤートップ -->
      <div class="player-top">
        <p>なまえ : <?php echo $_SESSION['human']->getName(); ?></p>
        <p>HP　:　<span
            class="<?php if ($_SESSION['human']->getHp() < 100) echo 'hp-low'; ?>"><?php echo $_SESSION['human']->getHp(); ?></span>
        </p>
        <p>つかまえたどうぶつ :　<?php echo $_SESSION['knockDownCount']; ?></p>
      </div>
      <!-- ここまでプレイヤートップ -->

      <!-- ここからプレイヤーコンテナ -->
      <div class="player-container">

        <!-- ここからプレイヤーレフト -->
        <div class="player-left js-auto-scroll">
          <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
        </div>
        <!-- ここまでプレイヤーレフト -->

        <!-- ここからプレイヤーライト -->
        <div class="player-right">
          <form method="post">
            <div class="player-action">
              <input class="button" type="submit" name="attack" value="つかまえる"">
                                <input class=" button" type="submit" name="escape" value="　にげる　">
              <input class="button" type="submit" name="defense" value="　まもる　">
            </div>
            <div class="restart">
              <input type="submit" name="start" value="リスタート">
            </div>
          </form>
          <!-- ここまでプレイヤーライト -->
        </div>

        <!-- ここまでプレイヤーコンテナ -->
      </div>

      <?php } ?>
      <!-- ゲームコンテナ -->
    </div>
  </div>

  <script src="js/vendor/jquery-2.2.2.min.js"></script>
  <script>
  (function($) {
    var $scrollAuto = $('.js-auto-scroll');
    $scrollAuto.animate({
      scrollTop: $scrollAuto[0].scrollHeight
    }, 'slow');
  }(jQuery));
  </script>
</body>

</html>