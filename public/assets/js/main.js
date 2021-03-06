$(function() {

    var lists = [];

    /*------------------------------------------------
     * レビューの抽出
    *----------------------------------------------*/
    $(document).on('click', '#get-review', function() {
        var param = {
            "title" : $('#item-name').val(),
            "items": lists
        };

        var data = JSON.stringify(param);

        $.ajax({
            'type': "POST",
            'url': "/review/add",
            'dataType': 'json',
            'contentType': 'application/json',
            'data': data
        }).done(function(data) {

            //レビュー抽出後のモーダルの表示変更
            $(".uk-modal-dialog").html('<div class="uk-container uk-container-center"><p>レビューの抽出が完了しました</p></div>');

        }).fail(function(xhr, textStatus, errorThrown) {
            //レビュー抽出後のモーダルの表示変更
            console.log(textStatus);
            console.log('失敗したで！');
        });
    });

    /*------------------------------------------------
     * 登録するアイテムの予約
     *----------------------------------------------*/
    $(document).on('click', '.add-item', function() {
        var item = {
            "itemName" : $(this).data('name'),
            "itemCode" : $(this).data('code'),
            "itemFrom" : $(this).data('from')
        }

        if ($(this).prop('checked') == true) {
            lists.push(item);
        } else {
            $(lists).each(function(i) {
                if (this.itemCode == item.itemCode) {
                    lists.splice(i, 1);
                }
            });
        }
    });


    /*------------------------------------------------
     * レビューの更新
     *----------------------------------------------*/
    $('.update-review').on('click', function() {

        var params = {
            item_id: $(this).attr('data-id')
        }

        $.ajax({
            'url': "/review/update",
            'type': "POST",
            'data': params
        }).done(function(data) {

            // レビュー更新後のモーダル表示変更
            $('.modal-title').html('レビューの更新が完了しました');
            $('.modal-body').html('完了');
            $('.modal-footer').html('');

            setTimeout(function() {
                $("#update-review-modal").modal('hide');

                location.href = "/review";
            }, 2000);

        }).fail(function(data) {
            console.log(data);
        });
    });


    /*------------------------------------------------
     * 楽天APIによる商品検索
     *----------------------------------------------*/
    $('#item-search').on('click', function() {

        // Validation
        if ($('#item-name').val() !== '' && $('#item-keyword').val() !== '') {
            var params = {
                itemName    : $('#item-name').val(),
                itemKeyword : $('#item-keyword').val(),
                searchType  : $('#search-type').val()
            };

            $.ajax({
                'url': "/review/search",
                'data': params,
                'type': "POST",
                'dataType':'json'
            }).done(function(data) {
                var str = "<tr><th><input type='checkbox'></th><th>商品名</th><th>レビュー数</th><th></th></tr>";

                $.each(data, function(i, value) {


                    // 実験用の設定（実験終了後に削除）
                    console.log(value);
                    if (value.reviewCode == '221245_10000180') return true;
                    if (value.reviewCode == '237671_10000244') return true;
                    if (value.reviewCode == '199275_10000509') return true;
                    if (value.reviewCode == '225961_10000213') return true;
                    if (value.reviewCode == '212428_10000075') return true;
                    if (value.from !== 'rakuten') return true;
                    // 実験用の設定ここまで（実験終了後に削除）


                    str += '<tr>' +
                    '<td><input class="add-item" type="checkbox" data-name="'+ value.itemName +'" data-code="'+ value.reviewCode +'" data-count="'+ value.reviewCount +'" data-from="'+ value.from +'">' +
                        '<img src=' + value.itemImageUrl + '>' +
                    '</td>' +
                    '<td>' + value.itemName + '</td>' +
                    '<td>' + value.reviewCount + '</td>' +
                    '</tr>';
                });

                $('#resultTable').html(str);

            }).fail(function(data, status, errorThrown) {
                console.log(data);
            });
        } else {
            alert('未入力の箇所があります');
        }
    });

    /*------------------------------------------------
     * アイテムモーダル削除操作
     *----------------------------------------------*/
    $('.modal-delete').on('click', function() {

        var element = $('.uk-modal-footer > .uk-button-danger');
        var url = element.attr('href') + $(this).attr('data-id');

        element.attr('href', url);
    });



    /*------------------------------------------------
     * 係受け構文ネガポジ操作
     *----------------------------------------------*/
    $('.btn-chunk').on('click', function() {

        var node = $(this);

        var params = {
            id   : $(this).data("id"),
            type : $(this).text()
        };

        $.ajax({
            'url': "/chunk/update",
            'data': params,
            'type': "POST"
        }).done(function(data) {
            // ページ際読み込み
            location.reload();
        }).fail(function(data, status, errorThrown) {
        });
    });

    /*------------------------------------------------
     * 係受け構文ネガポジ操作
     *----------------------------------------------*/
    $('.btn-chunk').on('click', function() {

        var node = $(this);

        var params = {
            id   : $(this).data("id"),
            type : $(this).text()
        };

        $.ajax({
            'url': "/chunk/update",
            'data': params,
            'type': "POST"
        }).done(function(data) {
            // ページ際読み込み
            location.reload();
        }).fail(function(data, status, errorThrown) {
        });
    });

    /*------------------------------------------------
     * グラフの作成
     *----------------------------------------------*/
    $(document).on('click', '#make-modal', function() {
        var id = $(this).data('id')

        var data = {
            "item-id" : id
        }

        $.ajax({
            'url':"/graph/make",
            'data':data,
            'type': "POST"
        }).done(function(data) {
            $(".uk-modal-dialog").html('<div class="uk-container uk-container-center"><p>決定表の作成が完了しました</p></div>');
        }).fail(function(data, status, errorThrown) {
            console.log('fail');
            console.log(status);
        });
    });


    /*------------------------------------------------
     * 新規感性ワードの登録処理
     *----------------------------------------------*/
    $('#file-upload').on('change', function() {
        if (this.files[0]['size'] != 0) {
            var fd = new FormData();
            var file = $('#file-upload');
            fd.append(file.attr('name'), file.prop("files")[0]);

            $.ajax({
                'url':'/thesaurus/upload',
                'type': 'POST',
                'data':fd,
                'processData': false,
                'contentType': false,
                'dataType':'json'
            })
            .done(function(data) {
                    $.each(data, function(key, value) {
                        var text = key;
                        var rayer = value['rayer'];
                        var synonyms = '';
                        $.each(value['synonym'], function(i, synonym) {
                            if (i != 0) synonym =  "," + synonym;
                            synonyms += synonym;
                        });

                        $('tbody').append("<tr><td>"+ text +"</td><td>"+ synonyms +"</td><td>"+ rayer +"</td></tr>");
                    });
            });
        }
    });


    var menu = $('.slide_menu'), // スライドインするメニューを指定
        menuBtn = $('#button'), // メニューボタンを指定
        body = $(".wrapper"),
        menuWidth = menu.outerWidth();
        body.toggleClass('open');
        body.css('left', menuWidth);
        menu.css('left', 0);

    // メニューボタンをクリックした時の動き
    menuBtn.on('click', function(){
        // body に open クラスを付与する
        body.toggleClass('open');
        if(body.hasClass('open')){
            // open クラスが body についていたらメニューをスライドインする
            body.animate({'left' : menuWidth }, 300);
            menu.animate({'left' : 0 }, 300);
        } else {
            // open クラスが body についていなかったらスライドアウトする
            menu.animate({'left' : -menuWidth }, 300);
            body.animate({'left' : 0 }, 300);
        }
    });
});