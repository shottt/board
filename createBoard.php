<?php

// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　掲示板作成ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETデータを格納
$b_id = (!empty($_GET['b_id'])) ? $_GET['b_id'] : '';
// DBから掲示板データを取得
$dbFormData = (!empty($b_id)) ? getBoard($_SESSION['user_id'], $b_id) : '';
// 新規作成画面か編集画面か判別用フラグ
$edit_flg= (empty($dbFormData)) ? false : true;
// DBからカテゴリデータを取得
$dbCategoryData = getCategory();
debug('掲示板ID：' . $b_id);
debug('フォーム用DBデータ：' . print_r($dbFormData, true));
debug('カテゴリーデータ：' .print_r($dbCategoryData,true));

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLを弄った）場合、正しい掲示板データが取れないので、マイページへ遷移させる
if(!empty($b_id) && empty($dbFormData)){
  debug('GETパラメータの掲示板IDが違います。マイページへ遷移します。');
  header("Loacation:mypage.php");
}


// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：' . print_r($_POST,true));
  debug('FILE情報：' .print_r($_FILES,true));


  // 変数に投稿情報を代入
  $title = $_POST['title'];
  $comment = $_POST['comment'];
  $category = $_POST['category_id'];
  // 画像をアップロードし、パスを格納
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
  // 画像をPOSTしてない（登録していない）が、既にDBに登録されている場合、DBのパスを入れる
  $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

  // バリデーションチェック
  //================================
  // 新規作成の場合（DBの情報が入っていない場合）
  if(empty($dbFormData)){
    // 未入力チェック（タイトル）
    validRequired($title, 'title');
    // 最大文字数チェック（タイトル）
    validMaxLen($title, 'title');
    // 最大文字数チェック（本文）
    validMaxLen($comment, 'comment', 500);
    // セレクトボックスチェック
    validSelect($category, 'category_id');
  // 更新の場合（DBに情報が入っている場合）
  }else{
    // DBの情報と入力情報が異なる場合にバリデーションを行う
    if($dbFormData['title'] !== $title){
      // 未入力チェック（タイトル）
      validRequired($title, 'title');
      // 最大文字数チェック（タイトル）
      validMaxLen($title, 'title');
    }
    if($dbFormData['comment'] !== $comment){
      // 最大文字数チェック（本文）
      validMaxLen($comment, 'comment', 500);
    }
    if($dbFormData['category_id'] !== $category){
      // セレクトボックスチェック
      validSelect($category, 'category_id');
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try{
      // DB接続
      $dbh = dbConnect();
      // SQL文作成
      if($edit_flg){
        debug('DB更新です。');
        $sql = 'UPDATE board SET title = :title, comment = :comment, category_id = :category, pic = :pic WHERE user_id = :u_id AND id = :b_id';
        $data = array(':title' => $title, ':comment' => $comment, ':category' => $category, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':b_id' => $b_id);
      }else{
        debug('DB新規登録です。');
        $sql = 'INSERT INTO board (title, comment, category_id, pic, user_id, create_date) VALUES (:title, :comment, :category, :pic, :u_id, :date)';
        $data = array(':title' => $title, ':comment' => $comment, ':category' => $category, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：' . $sql);
      debug('流し込みデータ：' . print_r($data, true));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg-success'] = SUC04;
        debug('マイページへ遷移します。');
        // マイページへ遷移
        header("Location:mypage.php"); 
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
  $siteTitle = (!$edit_flg) ? '掲示板作成' : '掲示板編集';
  require('head.php');
?>

<body class="page-1colum">
  
  <!-- ヘッダー -->
  <?php
    require('header.php');
  ?>

  <!-- メインコンテンツ -->
  <div id="contents" class="site-width">
    <h1 class="page-title"><?php echo (!$edit_flg) ? '掲示板を作成する' : '掲示板を編集する'; ?></h1>

    <!-- Main -->
    <section id="main">

      <div class="form-container">

        <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
          
          <div class="area-msg">
            <?php
              echo getErrMsg('common');
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['title'])) echo 'err'; ?>">
            タイトル<span class="label-require">必須</span>
            <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
          </label>
          <div class="area-msg">
            <?php
              echo getErrMsg('title');
            ?>
          </div>
          <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
            本文
            <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData('comment'); ?></textarea>
          </label>
          <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
          <div class="area-msg">
            <?php
              echo getErrMsg('comment');
            ?>
          </div>
          <label class="<? if(!empty($err_msg['category_id'])) echo 'err'; ?>">
            カテゴリ<span class="label-require">必須</span>
            <select name="category_id" id="">
              <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected'; } ?>>選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; } ?>><?php echo $val['name']; ?></option>
              <?php
               } 
              ?>
            </select>
          </label>
          <div class="area-msg">
            <?php
              echo getErrMsg('category_id');
            ?>
          </div>
          画像
          <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height:370px;line-height:370px;">
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic" class="input-file" style="height:370px;">
            <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none'; ?>">
            ドラッグ&ドロップ
          </label>
          <div class="area-msg">
            <?php
              echo getErrMsg('pic');
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>">
          </div>

        </form>
      </div>
    </section>

  </div>

  <!-- footer -->
  <?php
    require('footer.php');
  ?>

