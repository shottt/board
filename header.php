<header class="header">
  <h1 class="header__title">
    <a href="index.php" class="header__link">
      パパチャンネル<span class="header__sub-title">　〜パパのための雑談サイト〜</span>
    </a>
  </h1>
  <nav class="nav">
    <ul class="nav-list">
      <?php
        if(empty($_SESSION['user_id'])){
      ?>
        <li class="nav-list__item"><a href="signup.php" class="nav__link">ユーザー登録</a></li>
        <li class="nav-list__item"><a href="login.php" class="nav__link">ログイン</a></li>
      <?php
        }else{
      ?>
        <li class="nav-list__item"><a href="mypage.php" class="nav__link">マイページ</a></li>
        <li class="nav-list__item"><a href="logout.php" class="nav__link">ログアウト</a></li>
      <?php 
        } 
      ?>
    </ul>
  </nav>
</header>