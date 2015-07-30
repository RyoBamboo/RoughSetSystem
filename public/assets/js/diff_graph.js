/*--------------------------------------------
* 初期設定
*------------------------------------------*/
var WIDTH = 1000, HEIGHT = 600; // 描画する幅と高さ
var STAGE; // 描画するステージ
var item1;
var item2;
var NODES = []; // ノードを格納する配列
var LINKS = []; // ノード間のリンク情報を納める配列

init();

// グラフの初期設定
var force = d3.layout.force()
    .nodes(NODES)
    .size([WIDTH, HEIGHT])
    .links(LINKS)
    .linkStrength(0.1)
    .friction(0.7)
    .distance(50)
    .charge(-30)
    .gravity(0.0)
    .theta(0.1)
    .alpha(0.1);

force.on("tick", function() {
    var node = STAGE.selectAll("g.node").data(NODES);
    node.attr("transform", function(d) {
        return "translate(" + d.x + "," + d.y + ") scale(1.0, 1.0)";
    });

    var link = STAGE.selectAll("line").data(LINKS);
    link.attr({
        x1: function(d) { return d.source.x; },
        y1: function(d) { return d.source.y; },
        x2: function(d) { return d.target.x; },
        y2: function(d) { return d.target.y; }
    });
});

function init() {
    STAGE = d3.select("div#graph").append("svg:svg").attr("width", WIDTH).attr("height", HEIGHT);

    loadContent();
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

    // ATTRS描画
    for (var key in ATTRS) {
        NODES.push(ATTRS[key]);
        for (var _key in ATTRS[key]['chunks']) {
            NODES.push(ATTRS[key]['chunks'][_key]);
            LINKS.push({ source: ATTRS[key], target: ATTRS[key]['chunks'][_key]});
        }
    }

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
        .style({stroke: "#ccc", "stroke-width": 1});

    var node = STAGE.selectAll("g.node").data(NODES);
    var nodeEnter = node.enter().append("svg:g")
        .attr("class", function(d) {
           return "node " + d.type;
        })
        .attr("attr_text", function(d) {
            return d.attr_text;
        })
        .call(force.drag);

    nodeEnter.append("image")
        .attr("class", "circle")
        .attr("xlink:href", function (d) {
            switch (d.type) {
                case 'item':
                    return '/assets/img/red.png';
                    break;
                case 'attr':
                    return '/assets/img/blue.png';
                    break;
                case 'chunk':
                    return '/assets/img/green.png';
                default:
                    return 'http://www.webdesignlibrary.jp/images/article/ps_12659_1.jpg'
                    break;
            }
        } ) //ノード用画像の設定
        .attr("x", "-16px")
        .attr("y", "-16px")
        .attr("width", "32px")
        .attr("height", "32px");

    nodeEnter.append("text")
        .text(function(d) { return d.text });

    node.exit().remove();
    force.start();

    // レビューノードを非表示にする
    hideReviewNodes();
}



/*--------------------------------------------------
 * Filter
 *------------------------------------------------*/
function hideReviewNodes() {
    $(".chunk").hide();
}

/*--------------------------------------------------
 * Event Handler
 *------------------------------------------------*/
// 評価句ノードをクリックするとレビューノードの表示/非表示切り替え
function setEvent() {
    STAGE.selectAll("g.attr")
        .on("click", function() {
            var attr_text = $(this).closest('text').context.textContent;
            $("line[attr_text=" + attr_text +"]").toggle();
            $("g[attr_text=" + attr_text +"]").toggle();
        });
}

