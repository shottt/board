<?php

// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
// GETパラメータを取得
//----------------------------------
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; // デフォルトは1ページ目
// カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
// ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
// パラメータに不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  // トップページへ
  header("Location:index.php");
}
// 表示件数
$listSpan = 20;
// 現在のレコード先頭を算出
$currentMinNum = (($currentPageNum-1) * $listSpan);
// DBから掲示板データを取得（掲示板一覧に表示）
$dbBoardData = getBoardList($currentMinNum, $category, $sort);
debug('取得した掲示板データ：' . print_r($dbBoardData, true));
// DBからカテゴリデータを取得(検索エリアに表示)
$dbCategoryData = getCategory();
debug('取得したカテゴリデータ：' .print_r($dbCategoryData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
  $siteTitle = 'HOME';
  require('head.php');
?>

  <body class="page-home page-2colum">
    
    <!-- ヘッダー -->
    <?php 
      require('header.php');
    ?>

    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">

      <!-- Main -->
      <section id="main">
        <div class="search-title">
          <div class="search-left">
            <span class="total-num">トピックが<?php echo sanitize($dbBoardData['total']); ?>件見つかりました。</span>
          </div>
          <div class="search-right">
            <span class="num"><?php echo (!empty($dbBoardData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum + count($dbBoardData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbBoardData['total']); ?></span>件中
          </div>
        </div>
        <ol class="panel-list">
          <?php
            foreach($dbBoardData['data'] as $key => $val):
          ?>
          <li class="panel-cnt">
            <a href="boardDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam(). '&b_id='.$val['id'] : '?b_id='.$val['id']; ?>" class="panel">
              <div class="panel-image">
                <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?>" ?>
              </div>
              <div class="panel-text">
                <p class="panel-title"><?php echo sanitize($val['title']); ?></p>
                <p class="panel-comment"><?php echo mb_substr(sanitize($val['comment']),0,40); ?></p>
                <span class="panel-category"><?php echo sanitize($val['category']); ?></span>
              </div>
            </a>
          </li>
          <?php
            endforeach;
          ?>
        </ol>

        <?php pagination($currentPageNum, $dbBoardData['total_page']); ?>

      </section>

      <section id="sidebar">
        <h2 class="sidebar-title">検索エリア</h2>
        <form action="" name="" method="get">
          <h1 class="title">カテゴリー</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="c_id" id="">
              <option value="0" <?php if(getFormData('c_id',true) == 0){ echo 'selected';} ?>>選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id',true) == $val['id']){ echo 'selected'; } ?>>
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
            </select>
          </div>
          <div class="title">表示順</div>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="sort" id="">
              <option value="0" <?php if(getFormData('sort',true) == 0){ echo 'selected'; } ?>>選択してください</option>
              <option value="1" <?php if(getFormData('sort',true) == 1){ echo 'selected'; } ?>>投稿順</option>
              <option value="2" <?php if(getFormData('sort',true) == 2){ echo 'selected'; } ?>>新着順</option>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>
      </section>
    </div>

    <!-- footer -->
    <?php
      require('footer.php');
    ?>