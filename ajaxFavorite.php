<?php
// 共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// Ajax処理
//================================

// postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['boardId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('POST送信があります。');
  $b_id = $_POST['boardId'];
  debug('掲示板ID；' .$b_id);
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // レコードがあるか検索
    $sql = 'SELECT * FROM favorite WHERE board_id = :b_id AND user_id = :u_id';
    $data = array(':u_id' => $_SESSION['user_id'], ':b_id' => $b_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug($resultCount);
    // レコードが１件でもある場合
    if(!empty($resultCount)){
      // レコードを削除する
      $sql = 'DELETE FROM favorite WHERE board_id = :b_id AND user_id = :u_id';
      $data = array(':u_id' => $_SESSION['user_id'], ':b_id' => $b_id);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }else{
      // レコードを挿入する
      $sql = 'INSERT INTO favorite (board_id, user_id, create_date) VALUES (:b_id, :u_id, :date)';
      $data = array(':u_id' => $_SESSION['user_id'], ':b_id' => $b_id, ':date' => date('Y-m-d H:i:s'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);
    }
  }catch(Exception $e){
    error_log('エラー発生：' .$e->getMessage());
  }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>