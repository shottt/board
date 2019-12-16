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
        <div class="prof-img">
          <img src="<?php echo (showImg($userData['pic'])); ?>" alt="">
        </div>
        <div class="username">
          <?php 
            echo (!empty($userData['username'])) ? sanitize($userData['username']) : '';
          ?>
        </div>
      </div>

      <ul class="tab_menu">
        <li class="tab_btn is-active-btn">作成した掲示板一覧</li>
        <li class="tab_btn">コメントした掲示板一覧</li>
        <li class="tab_btn">お気に入り一覧</li>
      </ul>
        <ol class="panel-list tab_item is-active-item">
          <?php
            if(!empty($boardData)) :
              foreach($boardData as $key => $val):
          ?>
          <li class="panel-cnt">
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam(). '&b_id='.$val['id'] : '?b_id='.$val['id']; ?>" class="panel">
              <div class="panel-image">
                <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?>" ?>
              </div>
              <div class="panel-text">
                <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                <p class="panel-comment"><?php echo mb_substr(sanitize($val['comment']),0,40); ?></p>
              </div>
            </a>
          </li>
          <?php
              endforeach;
            endif;
          ?>
        </ol>
        <ol class="panel-list tab_item">
          <?php
            if(!empty($replyBoardData)) :
              foreach($replyBoardData as $key => $val):
          ?>
          <li class="panel-cnt">
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam(). '&b_id='.$val['b_id'] : '?b_id='.$val['b_id']; ?>" class="panel">
              <div class="panel-image">
                <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['b_title']); ?>" ?>
              </div>
              <div class="panel-text">
                <p class="panel-title"><?php echo sanitize($val['b_title']); ?></p>
                <p class="panel-comment"><?php echo mb_substr(sanitize($val['comment']),0,40); ?></p>
              </div>
            </a>
          </li>
          <?php
              endforeach;
            endif;
          ?>
        </ol>
        
        <ol class="panel-list tab_item">
          <?php
            if(!empty($favoriteData)) :
              foreach($favoriteData as $key => $val):
          ?>
          <li class="panel-cnt">
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam(). '&b_id='.$val['board_id'] : '?b_id='.$val['board_id']; ?>" class="panel">
              <div class="panel-image">
                <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?>" ?>
              </div>
              <div class="panel-text">
                <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                <p class="panel-comment"><?php echo mb_substr(sanitize($val['comment']),0,40); ?></p>
              </div>
            </a>
          </li>
          <?php
              endforeach;
            endif;
          ?>
        </ol>
      <!-- </div> -->
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