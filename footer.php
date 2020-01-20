<footer id="footer">
  <p>&copy; <a href="index.php">パパチャンネル</a>. All Rights Reserverd.</p>
</footer>

<script src="jquery-3.4.1.min.js"></script>
<script>
  $(function(){

    // フッターを最下部に固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;' });
    }
  

  // メッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  // relaceメソッドで半角、タブ、全角スペースを取り除き、文字があった場合に処理を行う
  if(msg.replace(/^[\s ]+|[\s ]+$/g, "").length){
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
  }

  // 画像ライブプレビュー
  var $dropArea = $('.area-drop');
  var $fileInput =$('.input-file');
  $dropArea.on('dragover', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });
  $dropArea.on('dragleave', function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', 'none');
  });
  $fileInput.on('change', function(e){
    $dropArea.css('border', 'none');
    var file = this.files[0], // 登録されたFileListオブジェクトを取得して変数に格納
    $img = $(this).siblings('.prev-img'), // jQueryのsiblingメソッドで兄弟要素のimgを取得
    fileReader = new FileReader(); // ファイルを読み込むFileReaderオブジェクト

    // 読み込みが完了した際のイベントハンドラ。imgをsrcにデータをセット
    fileReader.onload = function(event){
      // 読み込んだデータをimgに設定
      $img.attr('src', event.target.result).show();
    }
    // 画像ファイルをDataURLに変換（画像を文字列として扱える）
    fileReader.readAsDataURL(file);
  });

  // テキストエリアカウント
  var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
  $countUp.on('keyup', function(e){
    $countView.html($(this).val().length);
  });

  // お気に入り登録・削除
  var $favorite,
      favoriteBoardId;
  $favorite = $('.js-click-favorite'); 
  favoriteBoardId = $favorite.data('boardid');
  
  if(favoriteBoardId !== undefined && favoriteBoardId !== null){
    $favorite.on('click',function(){
      var $this = $(this);
      $.ajax({
        type: "POST",
        url: "ajaxFavorite.php",
        data: { boardId : favoriteBoardId}
      }).done(function( data ){
        console.log('Ajax Success');
        // クラス属性をtoggleでつけ外しする
        $this.toggleClass('active');
      }).fail(function( msg ){
        console.log('Ajax Error');
      });
    });
  }

  // タブメニュー
  $('.tab_btn').on('click', function(){
    $('.tab_btn').removeClass('is-active-btn');
    $(this).addClass('is-active-btn');
    $('.tab_item').removeClass('is-active-item');
    // クリックしたタブからインデックス番号を取得
    var index = $(this).index();
    // クリックしたタブと同じインデックス番号を持つコンテンツを表示
    $('.tab_item').eq(index).addClass('is-active-item');
  });
});
</script>

</body>
</html>