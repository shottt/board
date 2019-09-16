<?php

// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// DBからユーザーデータを取得
$userData = getUser($u_id);
// DBから作成した掲示板データを取得
$boardData = getMyBoard($u_id);
// DBからコメントした掲示板データを取得
$replyBoardData = getMyReplyAndBoard($u_id);
// DBからお気に入りの掲示板データを取得
$favoriteData = getMyFavorite($u_id);

// DBからきちんとデータが全て取れているかのチェックは行わず、取れなければなにも表示しないこととする

debug('取得したユーザーデータ：' .print_r($userData,true));
debug('取得した作成した掲示板データ：' .print_r($boardData,true));
debug('取得したコメントした掲示板データ：' .print_r($replyBoardData,true));
debug('取得したお気に入りの掲示板データ：' .print_r($favoriteData,true));


?>

<?php
  $siteTitle = 'マイページ';
  require('head.php');
?>

<body class="page-mypage page-2colum page-logined">
  <style>
    #main{
      border: none !important;
    }
  </style>
  <!-- メニュー -->
  <?php
    require('header.php');
  ?>

  <p id="js-show-msg" style="display:none;" class="msg-slide">
      <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    <h1 class="page-title">マイページ</h1>

    <!-- Main -->
    <section id="main" class="my-contents">
      <div class="profile">
        <h2 class="title"　 style="margin-bottom:15px">プロフィール</h2>
        <div class="prof-img">
          <img src="<?php echo (showImg($userData['pic'])); ?>" alt="">
        </div>
        <div class="username">
          <?php 
            echo (!empty($userData['username'])) ? sanitize($userData['username']) : '';
          ?>
        </div>
      </div>

      <div class="tab_menu">
        <a class="tab_btn is-active-btn" href="#item1">作成掲示板一覧</a>
        <a class="tab_btn" href="#item2">コメント掲示板一覧</a>
        <a class="tab_btn" href="#item3">お気に入り一覧</a>
      </div>

      <div class="tab_item is-active-item" id="item1">
        <?php
          if(!empty($boardData)):
            foreach($boardData as $key => $val):
        ?>
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&b_id=' .$val['id'] : '?b_id=' .$val['id']; ?>" class="mylist">
              <?php echo sanitize($val['title']); ?>
            </a>
        
        <?php
            endforeach;
          endif;
        ?>
      </div>

      <div class="tab_item" id="item2">
        <?php
          if(!empty($replyBoardData)):
            foreach($replyBoardData as $key => $val):
        ?>
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&b_id=' .$val['b_id'] : '?b_id=' .$val['b_id']; ?>" class="mylist">
              <?php echo sanitize($val['b_title']); ?>
           </a>

        <?php
            endforeach;
          endif;
        ?>
      </div>

      <div class="tab_item" id="item3">
        <?php
          if(!empty($favoriteData)):
            foreach($favoriteData as $key => $val):
        ?>
            <a href="boardDetail.php<?php echo (!empty(!appendGetParam())) ? appendGetParam() . '&b_id=' .$val['b_id'] : '?b_id=' .$val['b_id']; ?>" class="mylist"><?php echo sanitize($val['title']); ?></a>
        <?php
            endforeach;
          endif;
        ?>

      </div>
    </section>

    <!-- サイドバー -->
    <?php
      require('sidebar_mypage.php');
    ?>
  </div>

  <!-- footer -->
  <?php
    require('footer.php');
  ?>