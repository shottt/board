<?php

// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　掲示板詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================

// 掲示板IDのGETパラメータを取得
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
// DBから掲示板データを取得
$viewBoardData = getBoardOne($b_id);
// ユーザーIDを変数に格納（ログインしていない場合は空文字にする）
$user_id  = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
// パラメータに不正な値が入っているかチェック
if(empty($viewBoardData)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  // トップページへ遷移
  header("Location:index.php");
}
debug('取得したDBデータ' .print_r($viewBoardData,true));

// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; // デフォルトは1ページ目
// パラメータに不正な値が入っていないかチェック
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  // トップページへ
  header("Location:index.php");
}
// 表示件数
$listSpan = 20;
// 現在のレコード先頭を算出
$currentMinNum = (($currentPageNum-1) * $listSpan);

// DBから掲示板データと返信データ一覧を取得
$viewReplyData = getReplyList($currentMinNum, $b_id);
debug('取得したDBデータ：' .print_r($viewReplyData,true));


// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  // ログイン認証
  require('auth.php');

  // POST送信データを変数に格納
  $reply = $_POST['reply'];
  // バリデーションチェック

  // 未入力チェック
  validRequired($reply, 'reply');
  // 最大文字数チェック
  validMaxLen($reply,'reply', 500);

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'INSERT INTO reply (board_id, write_date, reply_user_id, reply, create_date) VALUES (:b_id, :write_date, :u_id, :reply, :date)';
      $data = array(':b_id' => $b_id, ':write_date' => date('Y-m-d H:i:s'), ':u_id' => $_SESSION['user_id'], ':reply' => $reply, ':date' => date('Y-m-d H:i:d'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        debug('ページをリロードします。');
        // 自分自身に遷移する
        header("Location:" . $_SERVER['PHP_SELF'] . '?b_id=' .$b_id);
      }
    }catch(Exception $e){
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
 $siteTitle = '掲示板詳細';
 require('head.php');
?>

<body class="page-boardDetail page-1colum">
  
  <!-- ヘッダー -->
  <?php
    require('header.php');
  ?>

  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">

    <!-- Main -->
    <section id="main">

      <div class="board-container">
        <div class="head">
          <span class="badge"><?php echo sanitize($viewBoardData['category']); ?></span>
          <h2 class="board-title"><?php echo sanitize($viewBoardData['title']); ?></h2>
          <i class="fa fa-heart icn-favorite js-click-favorite <?php if(isFavorite($user_id, $viewBoardData['id'])){ echo 'active'; } ?>" aria-hidden="true" data-boardid="<?php echo sanitize($viewBoardData['id']); ?>" ></i>
        </div>
        <div class="board-body">
          <div class="board-img">
            <img src="<?php echo showImg(sanitize($viewBoardData['pic'])); ?>" alt="サムネイル">
          </div>
          <div class="board-comment"> 
            <p><?php echo sanitize($viewBoardData['comment']); ?></p>
          </div>
          <button class="btn btn-link"><a href="#reply-form">コメントを投稿</a></button>
        </div>
      </div>
        <ol class="reply-list">
          <?php
            if(!empty($viewReplyData['data'])):
              foreach($viewReplyData['data'] as $key => $val):
          ?>
                <li class="reply-cnt">
                  <span class="username"><?php echo (!empty($val['username'])) ? sanitize($val['username']) : '匿名'; ?></span>
                  <span class="date"><?php echo sanitize($val['write_date']); ?></span>
                  <p><?php echo sanitize($val['reply']); ?></p>
                </li>
        <?php
              endforeach;
            endif;
        ?>
        </ol>
        
        <?php pagination($currentPageNum, $viewReplyData['total_page'], $b_id); ?>

        

    <form action="" method="post" id="reply-form">
      <h2 style="font-size:16px; margin-bottom:10px;">コメント内容</h2>
      <div class="form__area-msg">
        <?php
         echo getErrMsg('common');
        ?>
      </div>
      <textarea name="reply" placeholder="ここにコメントを入力してください(500文字以内)"></textarea>
      <div class="form__area-msg">
        <?php
          echo getErrMsg('reply');
        ?>
      </div>
      <div class="btn-container">
        <input type="submit" value="投稿する" class="btn btn-mid">
      </div>
    </form>
    <a style="margin: 20px 0; display: inline-block;" href="index.php<?php appendGetParam(array('b_id')); ?>">&lt; 掲示板一覧に戻る</a>
    </section>
  </div>

<!-- footer -->
<?php
  require('footer.php');
?>