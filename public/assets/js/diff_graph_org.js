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

NODES = [
    {belong:3, label:"赤霧島"},
    {belong:4, label:"赤兎馬"},
    {belong:0, label:"美味しい"},
    {belong:0, label:"辛い"},
    {belong:0, label:"まずい"},
    {belong:0, label:"甘い"},
    {belong:0, label:"重い"},
    {belong:1, label:"臭い"},
    {belong:1, label:"高い"},
    {belong:2, label:"安い"},
    {belong:2, label:"にがい"}
];



STAGE = d3.select("div#graph").append("svg:svg").attr("width", WIDTH).attr("height", HEIGHT);


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
    .alpha(0.1)
    .start();

var link = STAGE.selectAll("line")
    .data(LINKS)
    .enter()
    .append("line")
    .style({stroke: "#ccc", "stroke-width": 1});

var node = STAGE.selectAll("circle")
    .data(NODES)
    .enter()
    .append("circle")
    .attr({r: 20, opacity: 0.5})
    .style({fill: "red"})
    .call(force.drag);



var label = STAGE.selectAll('text')
    .data(NODES)
    .enter()
    .append('text')
    .attr({"text-anchor":"middle", "fill": "white"})
    .style({"font-size": 11})
    .text(function(d) { return d.label; });


force.on("tick", function() {
    node.attr({
        cx: function(d) { if (d.belong == 1) { return 100; } if (d.belong == 2) {return 700;}if (d.belong == 3) { return 200;} if (d.belong == 4) { return 600; } return 400; return d.x; },
        cy: function(d) { return d.y; }
    });

    label.attr({
        x: function(d) { if (d.belong == 1) { return 100; } if (d.belong == 2) { return 700; }if (d.belong == 3) { return 200;} if (d.belong == 4) { return 600; } return 400; return d.x; },
        y: function(d) { return d.y; }
    });

    link.attr({
        x1: function(d) { if (d.source.belong == 3) { return 200; } if (d.source.belong == 4) { return 600; } return 400; return d.source.x; },
        y1: function(d) { return d.source.y; },
        x2: function(d) { if (d.target.belong == 1){ return 100}; if (d.target.belong == 2) { return 700;}return 400; return d.target.x; },
        y2: function(d) { return d.target.y; }
    });

    //node.attr({
    //    cx: function(d) { return d.x; },
    //    cy: function(d) { return d.y; }
    //});
    //
    //label.attr({
    //    x: function(d) { return d.x; },
    //    y: function(d) { return d.y; }
    //});
    //
    //link.attr({
    //    x1: function(d) { return d.source.x; },
    //    y1: function(d) { return d.source.y; },
    //    x2: function(d) { return d.target.x; },
    //    y2: function(d) { return d.target.y; }
    //});
});



