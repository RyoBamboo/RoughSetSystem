/*--------------------------------------------
 * 初期設定
 *------------------------------------------*/

var WIDTH = 1350, HEIGHT = 900;
var vbox_x = 0;
var vbox_y = 0;
var vbox_default_width = vbox_width = 1350;
var vbox_default_height = vbox_height = 900;
var STAGE; // 描画するステージ
var item1;
var item2;
var NODES = []; // ノードを格納する配列
var LINKS = []; // ノード間のリンク情報を納める配列

// ドラッグの設定
drag = d3.behavior.drag().on("drag", function(d) {
    vbox_x -= d3.event.dx;
    vbox_y -= d3.event.dy;
    return STAGE.attr("viewBox", "" + vbox_x + " " + vbox_y + " " + vbox_width + " " + vbox_height);  //svgタグのviewBox属性を更新
});

// ズームの設定
zoom = d3.behavior.zoom().on("zoom", function(d) {
    var befere_vbox_width, before_vbox_height, d_x, d_y;
    befere_vbox_width = vbox_width;
    before_vbox_height = vbox_height;
    vbox_width = vbox_default_width * d3.event.scale;
    vbox_height = vbox_default_height * d3.event.scale;
    d_x = (befere_vbox_width - vbox_width) / 2;
    d_y = (before_vbox_height - vbox_height) / 2;
    vbox_x += d_x;
    vbox_y += d_y;
    return STAGE.attr("viewBox", "" + vbox_x + " " + vbox_y + " " + vbox_width + " " + vbox_height);  //svgタグのviewBox属性を更新
});

init();

// グラフの初期設定
var force = d3.layout.force()
    .nodes(NODES)
    .size([WIDTH, HEIGHT])
    .links(LINKS)
    .linkStrength(0.1)
    .linkDistance(200)
    .charge(-2000)
    .gravity(0.05)

force.on("tick", function() {
    var node = STAGE.selectAll("g.node").data(NODES);
    /*
    node.attr("transform", function(d) {
        return "translate(" + d.x + "," + d.y + ") scale(1.0, 1.0)";
    });
    */
    node.attr("transform", function(d) {
        //階層毎にNodeの大きさを分ける
        if(isset(d.rayer)){
            var r = "";
            var rayer = parseInt(d.rayer)+1;
            switch(rayer) {
                case 1:
                    //r = "translate(" + d.x + ", " + d.y + ") scale( 1.2, 1.2)";
                    r = "translate(" + d.x + ", " + d._y + ") scale( 1.2, 1.2)";
                    break;
                case 2:
                    //r = "translate(" + d.x + ", " + d.y + ") scale( 1.4, 1.4)";
                    r = "translate(" + d.x + ", " + d._y + ") scale( 1.4, 1.4)";
                    break;
                case 3:
                    //r = "translate(" + d.x + ", " + d.y + ") scale( 1.7, 1.7)";
                    r = "translate(" + d.x + ", " + d._y + ") scale( 1.7, 1.7)";
                    break;
            }
            return r;
        }
        return "translate(" + d.x + "," + d.y + ") scale(1.0, 1.0)";
    });

    var link = STAGE.selectAll("line").data(LINKS);
    link.attr({
        x1: function(d) { return d.source.x; },
        y1: function(d) { return d.source._y; },
        x2: function(d) { return d.target.x; },
        y2: function(d) { return d.target._y; }
    });
});

function init() {
    STAGE = d3.select("div#graph")
        .append("svg:svg")
        .attr("width", WIDTH)
        .attr("height", HEIGHT)
        .attr("viewBox", "" + vbox_x + " " + vbox_y + " " + vbox_width + " " + vbox_height);

    loadContent();
    STAGE.call(zoom);
    STAGE.call(drag);
}


function loadContent() {
    var params = location.href.split("?")[1];
    var items = params.split("&");
    item1 = items[0].split("=")[1];
    item2 = items[1].split("=")[1];

    var data = {
        "item1" : item1,
        "item2" : item2
    };

    $.ajax({
        type: "POST",
        url: "/graph/test",
        data: data,
        success: function(res){
            if(res){
                json = $.parseJSON(res);
                ATTRS = json['ATTRS'];
                console.log(ATTRS);
                DR = json['DR'];
                ITEMS = json['ITEMS'];
                draw();
                update();
                setEvent();
            }
        }
    });
}


function draw() {
    // ITEMS描画
    for (var key in ITEMS) {
        NODES.push(ITEMS[key]);
    }

    // 決定ルールの描画
    for (var itemId in DR) {
        // ノードの追加
        for (var attr_id in ATTRS[itemId]) {
            var _y;
            var _rayer = parseInt(ATTRS[itemId][attr_id].rayer)+1;
            switch(_rayer) {
                case 1:
                    _y =  Math.floor( Math.random() * 150) + 650;
                    break;
                case 2:
                    _y =  Math.floor( Math.random() * 250) + 350;
                    break;
                case 3:
                    _y =  Math.floor( Math.random() * 150) + 100;
                    break;
            }
            ATTRS[itemId][attr_id]['_y'] = _y;

            NODES.push(ATTRS[itemId][attr_id]);
        }
        console.log(ATTRS);

        for (var dr in DR[itemId][1]['attrs']) {
            // 否定の感性ワードを条件に含む決定ルールは無視
            if (DR[itemId][1]['dr'][dr].indexOf('2') != -1) continue;
            var attrCnt = count(DR[itemId][1]['attrs'][dr]);
                // 決定ルールの条件部が複数の場合
                for (var i = 0; i < attrCnt; i++) {
                    if (i+1 >= attrCnt) break;
                    // いらない文字（A '1')を削除
                    var attrKey1 = DR[itemId][1]['attrs'][dr][i].replace('1', '');
                    var attrKey2 = DR[itemId][1]['attrs'][dr][i+1].replace('1', '');

                    LINKS.push({ source: ATTRS[itemId][attrKey1], target: ATTRS[itemId][attrKey2] });
                }
        }
    }
    console.log(NODES);

    // ATTRS描画
    //for (var key in ATTRS) {
    //    NODES.push(ATTRS[key]);
    //    for (var _key in ATTRS[key]['chunks']) {
    //        NODES.push(ATTRS[key]['chunks'][_key]);
    //        LINKS.push({ source: ATTRS[key], target: ATTRS[key]['chunks'][_key]});
    //    }
    //}

    for (key in NODES) {
        // 共通の評価句のリンク追加
        if(NODES[key].belong == 0) {
            LINKS.push({source: 0, target: NODES[key]});
            LINKS.push({source: 1, target: NODES[key]});
        } else if (NODES[key].belong == item1) {
            LINKS.push({source: 0, target: NODES[key]});
        } else if (NODES[key].belong == item2) {
            LINKS.push({source: 1, target: NODES[key]});
        }
    }
}

function update() {
    var link = STAGE.selectAll("line")
        .data(LINKS)
        .enter()
        .append("line")
        .attr("attr_text", function(d) {
            return d.source.text;
        })
        .attr("class", function(d) {
            return d.target.type;
        })
        .style({stroke: "#ccc", "stroke-width": 4});

    var node = STAGE.selectAll("g.node").data(NODES);
    var nodeEnter = node.enter().append("svg:g")
        .attr("class", function(d) {
            return "node " + d.type;
        })
        .attr("attr_text", function(d) {
            return d.attr_text;
        })
        .attr("attr_id", function(d) {
            return d.identified_string;
        })
        .attr("item_id", function(d) {
            console.log(d);
            return d.item_id;
        })
        .attr("review_text", function(d) {
            return d.review_text;
        })
        .call(force.drag);

    nodeEnter.append("svg:image")
        .attr("class", "circle")
        .attr("xlink:href", function(n) {console.log(n); if(isset(n.rayer)) { var _rayer = parseInt(n.rayer) + 1; return "http://rough.prodrb.com/img/rayer/" + _rayer + ".png"; } return "http://rough.prodrb.com/img/negaposi/" + n.negaposi + ".png";} ) //ノード用画像の設定
        .attr("negaposi", function(n) { if(isset(n.negaposi)) { return n.negaposi; } } ) //ノード用画像の設定
        .attr("x", "-16px")
        .attr("y", "-16px")
        .attr("width", "32px")
        .attr("height", "32px")

    /*
    nodeEnter.append("image")
        .attr("class", "circle")
        .attr("xlink:href", function (d) {
            if (d.type == 'item') {
                return '/assets/img/red.png';
                //switch (d.type) {
                //    case 'item':
                //        return '/assets/img/red.png';
                //        break;
                //    case 'attr':
                //        return '/assets/img/blue.png';
                //        break;
                //    case 'chunk':
                //        return '/assets/img/green.png';
                //    default:
                //        return 'http://www.webdesignlibrary.jp/images/article/ps_12659_1.jpg'
                //        break;
            } else if (d.type == 'attr' && d.belong == 0) {
                return '/assets/img/green.png';
            } else {
                return '/assets/img/blue.png';
            }
        } ) //ノード用画像の設定
        .attr("x", "-16px")
        .attr("y", "-16px")
        .attr("width", "32px")
        .attr("height", "32px");
        */
    nodeEnter.append("text")
        .text(function(d) {
            if (d.type == 'attr') {
                var text = d.text + " : " + d.rf + "%";
            } else {
                var text = d.text;
            }
            return text;
        });

    node.exit().remove();
    force.start();

    // レビューノードを非表示にする
    hideReviewNodes();

    // 決定ルールが1つだけの感性ワードを非表示にする
    hideOnlyAttrDR();
}



/*--------------------------------------------------
 * Filter
 *------------------------------------------------*/
function hideReviewNodes() {
    $(".chunk").hide();
}

function hideOnlyAttrDR() {
   for (var itemId in DR) {
        for(var dr in DR[itemId][1]['attrs']) {
            if (count(DR[itemId][1]['attrs'][dr]) == 1 && DR[itemId][1]['attrs'][dr].indexOf("2" == -1)) {
                var attr_id = DR[itemId][1]['dr'][dr].replace('1', '');
                $(".node").each(function() {
                    if ($(this).attr('attr_id') == attr_id && $(this).attr('item_id') == itemId) {
                        $(this).css('display', 'none');
                    }
                });
            }
        }
   }
}

/*--------------------------------------------------
 * Event Handler
 *------------------------------------------------*/
function setEvent() {
    // 評価句ノードをクリックするとレビューノードの表示/非表示切り替え
    STAGE.selectAll("g.attr")
        .on("click", function() {
            var attr_text = $(this).closest('text').context.textContent;
            attr_text = attr_text.split(" ")[0];
            $("line[attr_text=" + attr_text +"]").toggle();
            $("g[attr_text=" + attr_text +"]").toggle();
        });

    // チャンクノードをクリックすると元のレビューを表示
    STAGE.selectAll("g.chunk")
        .on("click", function(d) {
            var modal = UIkit.modal(".uk-modal");
            if ( modal.isActive() ) {
                modal.hide();
            } else {
                $(".uk-modal-dialog .review_text").text(d.review_text);
                $(".uk-modal-dialog .chunk_text").text(d.text);
                modal.show();
            }
        });
}

