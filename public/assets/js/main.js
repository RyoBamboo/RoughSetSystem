$(function() {

    var lists = [];

    /*------------------------------------------------
     * レビューの抽出
    *----------------------------------------------*/
    $(document).on('click', '#get-review', function() {
        return;
        var param = {
            title : $('#item-name').val(),
            items: lists
        };

        console.log(param);

        var data = JSON.stringify(param);

        $.ajax({
            'type': "POST",
            'url': "/review/add",
            'dataType': 'json',
            'contentType': 'application/json',
            'data': data
        }).done(function(data) {

            //レビュー抽出後のモーダルの表示変更
            $('.modal-title').html('レビューの抽出が完了しました');
            $('.modal-body').html('完了');
            $('.modal-footer').html(
                '<a href="/review/add" type="button" class="btn btn-default">続けて抽出する</a>' +
                '<a href="/review" type="button" class="btn btn-default">一覧に戻る</a>'
            );

        }).fail(function(data) {
            console.log(data);
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

        var element = $('.modal-footer > a');

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
});