/*--------------------------------------------
* 初期設定
*------------------------------------------*/
var WIDTH = 1000, HEIGHT = 600; // 描画する幅と高さ
var STAGE; // 描画するステージ

var ATTRS_1 = []; // サンプル１の評価句配列
var ATTRS_2 = []; // サンプル２の評価句配列
var ATTRS_COMMON = []; // 共通の評価句配列
var NODES = []; // ノードを格納する配列
var LINKS = []; // ノード間のリンク情報を納める配列

init();


// グラフの初期設定
var force = d3.layout.force()
    .nodes(NODES)
    .size([WIDTH, HEIGHT])
    .links(LINKS)
    .linkStrength(0.1)
    .friction(0.9)
    .distance(200)
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
    $.ajax({
        type: "POST",
        url: "http://rst.prodrb.com/graph/test",
        success: function(res){
            if(res){
                json = $.parseJSON(res);
                ATTRS = json['ATTRS'];
                draw();
                update();
            }
        }
    });
}

function draw() {
    // ATTRS描画
    for (var key in ATTRS) {
        NODES.push(ATTRS[key]);
    }

    for (key in NODES) {
        // 共通の評価句のリンク追加
        if(NODES[key].belong == 0) {
            LINKS.push({source: 0, target: NODES[key]});
            LINKS.push({source: 1, target: NODES[key]});
        } else if (NODES[key].belong == 1) {
            LINKS.push({source: 0, target: NODES[key]});
        } else if (NODES[key].belong == 2) {
            LINKS.push({source: 1, target: NODES[key]});
        }
    }
}

function update() {
    var link = STAGE.selectAll("line")
        .data(LINKS)
        .enter()
        .append("line")
        .style({stroke: "#ccc", "stroke-width": 1});

    var node = STAGE.selectAll("g.node").data(NODES);
    var nodeEnter = node.enter().append("svg:g")
        .attr("class", "node")
        .call(force.drag);

    nodeEnter.append("image")
        .attr("class", "circle")
        .attr("xlink:href", 'http://www.webdesignlibrary.jp/images/article/ps_12659_1.jpg' ) //ノード用画像の設定
        .attr("x", "-16px")
        .attr("y", "-16px")
        .attr("width", "32px")
        .attr("height", "32px");

    nodeEnter.append("text")
        .text(function(d) { return d.label });

    node.exit().remove();
    force.start();
}



