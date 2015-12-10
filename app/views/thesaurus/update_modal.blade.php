<div id="update" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header">
            感性ワードの階層構造を変更することができます
        </div>
        <div class="uk-container uk-container-center">
            <form class="uk-form">
                <select name="rayer">
                    <option value=2>認識</option>
                    <option value=1>認知</option>
                    <option value=0>知覚</option>
                </select>
            </form>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="uk-button">キャンセル</button>
            <a href="#" type="button" class="update-button uk-button uk-button-primary">更新</a>
        </div>
    </div>
</div>

<script>
    $('.modal-update').on('click', function() {
        var id = $(this).data('id');
        $('.update-button').data('id', id);
    });

    $('.update-button').on('click', function() {
        var params = {
            "id": $(this).data('id'),
            "rayer": $('.uk-form [name=rayer]').val()
        }

        $.ajax({
            'url': "/thesaurus/update",
            'data': params,
            'type': "POST"
        }).success(function(data) {
            var rayerText;
            switch (parseInt(params.rayer)) {
                case 2:
                    rayerText = '認識';
                    break;
                case 1:
                    rayerText = '認知';
                    break;
                case 0:
                    rayerText = '知覚';
                    break;
            }
        }).fail(function(data, status, errorThrown) {
            console.log(data);
        });
    });
</script>