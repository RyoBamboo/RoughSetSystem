/*var WIDTH = 2500, HEIGHT = 1500;*/
var WIDTH = 1800, HEIGHT = 1000;
var STAGE;
var nodes = [],    //ノードを収める配列
    links = [];    //ノード間のリンク情報を収める配列

var ATTRS = {};
var DR = {};
var TYPE = 1;//1:buy,2:not buy


//グラフの初期設定
var force = self.force = d3.layout.force()
    .nodes(nodes)
    .links(links)
    .gravity(0.05) //重力
    //.distance(500) //ノード間の距離
    .linkDistance(200)
    .charge(-150) //各ノードの引き合うor反発しあう力
    .size([WIDTH, HEIGHT]); //図のサイズ

function init() {
    //グラフを描画するステージ（svgタグ）を追加
    STAGE = d3.select("div#graph").append("svg:svg").attr("width", WIDTH).attr("height", HEIGHT);

    loadContent();
}

//グラフにアニメーションイベントを設置
force.on("tick", function(e) {
    var node = STAGE.selectAll("g.node").data(nodes, function(d) { return d.id;} );
        //node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ") scale(1.0, 1.0)"; });
	node
  .attr("transform", function(d) {
		//階層毎にNodeの大きさを分ける
		if(isset(d.params)){
			var r = "";
			switch(d.params.rayer) {
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
	})
	.attr("negaposi", function(n) { if(isset(n.negaposi)) { return n.negaposi; } } ); //ノード用画像の設定

    var link = STAGE.selectAll("line.link").data(links, function(d) { return d.source.id + ',' + d.target.id});
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) {
            return d.source._y; 
            // return getHeightByRayer(d.source.params.rayer);
            //return d.source.y;
        })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { 
          if (d.dr) return d.target._y;
            // return getHeightByRayer(d.source.params.rayer);
          return d.target.y; 
        })
	.attr("id", function(d) { return d.dr; })
	.style("stroke", function(d) {if(isset(d.params)) { return d.params.color;} return "#BABABA"; })
	.attr("stroke-width", function(d) {
          if(isset(d.matching) && d.matching.kl >= 1) { 
               return parseInt(d.matching.kl); 
          } 
          return (d.params.width > 10) ? 10 : d.params.width; 
      })//線の太さ
	.attr("stroke-dasharray", function(d) { if(isset(d.dr)){ return "0";} return "0"; });//破線
});

// Move nodes toward cluster focus.
function gravity(alpha) {
  return function(d) {
    d.y += (d.cy - d.y) * alpha;
    d.x += (d.cx - d.x) * alpha;
  };
}

function draw() {
    //ATTRS描画
    for(var key in ATTRS) {
     var _y;
     switch(ATTRS[key].params.rayer) {
          case 1:
            _y =  Math.floor( Math.random() * 150) + 650;
            break;
          case 2:
            _y =  Math.floor( Math.random() * 150) + 350;
            break;
          case 3:
            _y =  Math.floor( Math.random() * 150) + 50;
            break;
     } 
     ATTRS[key]['_y'] = _y;
    	nodes.push(ATTRS[key]);
      for(var k in ATTRS[key]['chunks']) {
          if(ATTRS[key]['chunks'][k]['dc'] != TYPE) continue;
          nodes.push(ATTRS[key]['chunks'][k]);
          links.push({ source:ATTRS[key], target: ATTRS[key]['chunks'][k], params:ATTRS[key]['params'] });
      }
    }
    //DR描画
    for(var key in DR) {
        for(var k in DR[key]['attrs']) {
            var k2 = parseInt(k)+1;
            if(k2 >= count(DR[key]['attrs'])) { break; }
            var atr1 = DR[key]['attrs'][k]; 
            var atr2 = DR[key]['attrs'][k2];
            links.push({dr:DR[key]['dr'], source: ATTRS[atr1], target: ATTRS[atr2], params: DR[key]['params'], matching: MATCHING[atr1 + "-" + atr2] });
        }
    }
}

//アップデート
function update() {
    var link = STAGE.selectAll("line.link")
    .data(links, function(l) { return l.source.id + '-' + l.target.id; }); //linksデータを要素にバインド

    link.enter().append("svg:line")
    .attr("class",function(d) { if(isset(d.dr)) { return "link " + d.dr; } return "link lchunk attr_" + d.source.attrid; } )
    .attr("attr_id",function(d) { if(isset(d.dr)) { return null; } return d.source.attrid; } )
    .attr("x1", function(d) { return d.source.x; })
    .attr("y1", function(d) { return d.source.y; })
    .attr("x2", function(d) { return d.target.x; })
    .attr("y2", function(d) { return d.target.y; });

    link.exit().remove(); //要らなくなった要素を削除

    var node = STAGE.selectAll("g.node")
    .data(nodes, function(d) { return d.dpid;});//nodesデータを要素にバインド
    var nodeEnter = node.enter().append("svg:g")
    .attr("id", function(n) { return "n_" +  n.id; })
    .attr("class", function(n) { if(isset(n.params)) { return  "node attr " + "r" + n.params.rayer +  " attr_" + n.attrid; } else { return "node chunk attr_" + n.attrid; }})
    .attr("attr_id", function(n) { return n.attrid })
    .attr("review_id", function(n) { if(isset(n.review_id)) { return  n.review_id ; }})
    .call(force.drag); //ノードをドラッグできるように設定

    nodeEnter.append("svg:image")
    .attr("class", "circle")
    .attr("xlink:href", function(n) { if(isset(n.params)) { return "http://rough.prodrb.com/img/rayer/" + n.params.rayer + ".png"; } return "http://rough.prodrb.com/img/negaposi/" + n.negaposi + ".png";} ) //ノード用画像の設定
    .attr("negaposi", function(n) { if(isset(n.negaposi)) { return n.negaposi; } } ) //ノード用画像の設定
    .attr("x", "-16px")
    .attr("y", "-16px")
    .attr("width", "32px")
    .attr("height", "32px")

    nodeEnter.append("svg:text")
    .attr("class", "nodetext")
    .attr("dx", 18)
    .attr("dy", ".37em")
    .text(function(d) { return d.text });

    node.exit().remove(); //要らなくなった要素を削除

    setEvent();
    force.start(); //forceグラグの描画を開始
}


/***********************************************************************/
/* Load Content                                                        */
/***********************************************************************/
function loadContent() {
	showFilter();
	var ret = location.href.split("/");
    var item_id = ret[ret.length - 1];
    var data = {
        "item_id": item_id
    }

	//var sendData = "";
    //if(count(ret) != 0) {
    //	sendData += "car=" + ret[count(ret) - 1];
    //}

    //sendData += "id=1";
    //sendData += "&type=1";

	$.ajax({
		type: "POST",
		//url: "/ajax/load_content2.php",
        url: "/graph/load",
		data: data,
		success: function(res){
			if(res){
				json = $.parseJSON(res);
				//TODO:DCで分類するロジック
				DR = json['DR'][TYPE];
				MATCHING = json['MATCHING'][TYPE];
				ATTRS = json['ATTRS'][TYPE];
				setReview(json['REVIEWS'][TYPE]);
				$("#DR").html(json['DR_TEXT']);
				$("#DRH").html(json['DRH_TEXT']);
				$("#ATTR").html(json['ATTR_TEXT']);
				hideFilter();
				draw();
				update();
                      hideAttr();
                      hideAllChunk();
			}
		}
	});

}

function hideAllChunk() {
    d3.selectAll(".chunk").style("display", "none");
    d3.selectAll(".lchunk").style("display", "none");
}

function hideAttr() {
  d3.selectAll(".attr").style("display", function(n) {
    if (n.text.indexOf("^") != -1) {
      return "none";
    }
  });

  d3.selectAll(".link").style("display", function(n) {
    if (n.dr) {
      if (n.dr.indexOf("2") != -1) {
        return "none";
      }
    }
  });
}

function setReview(reviews) {
	for(key in reviews) {
		$("#reviews ul").append('<li id="rev' + reviews[key]['id']  + '"' +"><p><h4><総評></h4>"  + reviews[key]['souhyou'] + "</p><p><h4><長所></h4>" + reviews[key]['chousho'] + "</p><p><h4><総評></h4>" + reviews[key]['tansho'] + "</p><p><h4><要点></h4>" + reviews[key]['points'] + "</p></li>");
        $("#reviews ul").append('<li id="rev' + reviews[key][0]['id']  + '"' +"><p><h4>レビュー・要点</h4>"  + reviews[key][0]['content'] + "</li>");
		$("#reviews ul").append('<li id="rev' + reviews[key]['id']  + '">' + reviews[key]['review'] + "</li>");
	}
}

init();

/***************************************************************
 * Event Handler 
 ***************************************************************/
$("#menu_attr,#ATTR").click(function() {
	if($("#ATTR").css("display") == "none") {
		$("#ATTR").show();
	} else {
		$("#ATTR").hide();
	}
});

$("#menu_dr,#DR").click(function() {
	if($("#DR").css("display") == "none") {
		$("#DR").show('normal');
	} else {
		$("#DR").hide('normal');
	}
});

$("#menu_drh,#DRH").click(function() {
	if($("#DRH").css("display") == "none") {
		$("#DRH").show('normal');
	} else {
		$("#DRH").hide('normal');
	}
});

$("#menu_reviews, #right_content").click(function() {
	if($("#right_content").css("display") == "none") {
		$("#right_content").show('normal');
	} else {
		$("#right_content").hide('normal');
	}
});

$("#review").click(function() {
	if($("#review").css("display") != "none") {
		$("#review").hide('normal');
	}
});

function setEvent() {
	/* Node の表示/非表示 */
	STAGE.selectAll("g.node").on("click", function() {
		if(isset(d3.select(this).attr("review_id")) && d3.select(this).attr("review_id") != null ) {
			//TODO:textを取得して,レビューに反映
			var negaposi = d3.select(this).attr("negaposi");
			 
			var c_text = d3.select(this).text();
			var _c_text = c_text.split("-");
			var rev_id = "#rev" + d3.select(this).attr("review_id");
			var review = String($(rev_id).html());
			var h = String(_c_text[0]); var f = String(_c_text[1]);
			//review = review.replace(h, ('<b class="point">' + h));
			//review = review.replace("。", ("。" + '</b>'));
			var str = SplitStr(review, _c_text[0], "。");
			str = h + str;
			review = review.replace(str, ('<b class="point_' + negaposi + '">' + str + '</b>'));

			//$("#review").html($(rev_id).html());
			$("#review").html(review);
			$("#review").show("normal");
		}
	});


	STAGE.selectAll("g.node")
	.on("mouseover", function() {
		if(on_ctl_key) {
		    var attrid = ".attr_"+ d3.select(this).attr("attr_id");
		    d3.selectAll("line.link").style("display", "none");
		    d3.selectAll("g.node").style("display", "none");
		    d3.selectAll(attrid).style("display", "block");
		}
	})
	.on("mouseout", function() {
		if(on_ctl_key) {
		    d3.selectAll("line.link").style("display", "block");
		    d3.selectAll("g.node").style("display", "block");
		}
	});
	/*STAGE.selectAll("line.link").on("mouseover", function() {
		var drid = "#" + d3.select(this).attr("id");
		d3.selectAll("line.link").style("display", "none");
		d3.selectAll(drid).style("display", "block");
	})
	.on("mouseout", function() {
		d3.selectAll("line.link").style("display", "block");
	});*/

	STAGE.selectAll("line.link").on("click", function() {
		var id = "#" + d3.select(this).attr("id");
		if(d3.select(id).style("display") != "none") {
			d3.selectAll("line.link").style("display", "none");
			d3.selectAll("g.node").style("display", "none");
			d3.selectAll(id)
			.attr("id", function(l) {
			    var s_attrid = ".attr_" + (l.source.attrid);
			    var t_attrid = ".attr_" + (l.target.attrid);
			    d3.selectAll(s_attrid).style("display", "block");
			    d3.selectAll(t_attrid).style("display", "block");
			})
			.style("display", "block");
			d3.select("#menu_hidedr").style("display", "block");
			d3.select("#menu_hidedr").attr("val", id);
		}
	});

    d3.selectAll("line.link").on("mouseover", function() {
        var id = "#" + d3.select(this).attr("id");
        if(d3.select(id).style("display") != "none") {
            d3.selectAll("line.link").style("opacity", 0.2);
            d3.selectAll("line.lchunk").style("opacity", 1);
            d3.selectAll(id)
                .attr("id", function(l) {
                    var s_attrid = ".attr_" + (l.source.attrid);
                    var t_attrid = ".attr_" + (l.target.attrid);
                    //d3.selectAll(s_attrid).style("display", "block");
                    //d3.selectAll(t_attrid).style("display", "block");
                })
                .style("opacity", 1);
        }

    });



    // 評価句の表示/非表示切り替え
    d3.selectAll(".attr").on("click", function() {
        var attr_id = $(this).attr("attr_id");
        var attr_text = $(this).text();

        $(".chunk.attr_" + attr_id).toggle();
        $(".lchunk.attr_" + attr_id).toggle();
    });
}

var on_ctl_key = false;
$('html').keydown(function(e){
    on_ctl_key = true;
});

$('html').keyup(function(e){
    on_ctl_key = false;
});

d3.select("#menu_hidedr").on("click",function() {
    d3.selectAll("line.link").style("display", "block");
    d3.selectAll("g.node").style("display", "block");
    d3.select("#menu_hidedr").style("display", "none");
});

d3.select("#menu_chunk").on("click", function() {
    if(d3.selectAll(".chunk").style("display") != "none") {
        d3.selectAll(".chunk").style("display", "none");
        d3.selectAll(".lchunk").style("display", "none");
    } else {
        d3.selectAll(".chunk").style("display", "inline");
        d3.selectAll(".lchunk").style("display", "inline");
    }
});

d3.select("#menu_posi").on("click", function() {
   d3.selectAll(".chunk").style("display",
       function(g) {
           if(isset(g.negaposi)) {
                if(g.negaposi == "p") {
                    return "block";
                }
           }
           return "none";
       }
   );
   d3.selectAll(".lchunk").style("display", "none");
   d3.selectAll(".lchunk").style("display",
      function(l) {
          var r = "none";
          var id = "#n_" + l.source.id;
          if(d3.select(id).style("display") == "none") {
              r = "none";
          } else {
              if(isset(l.target.negaposi)) {
	          if(l.target.negaposi == "p") r = "block";
              }
          }
          return r;
      }
   );
});

d3.select("#menu_nega").on("click", function() {
   d3.selectAll(".chunk").style("display",
       function(g) {
           if(isset(g.negaposi)) {
                if(g.negaposi == "n") {
                    return "block";
                }
           }
           return "none";
       }
   );
   d3.selectAll(".lchunk").style("display", "none");
   d3.selectAll(".lchunk").style("display",
      function(l) {
          var r = "none";
          var id = "#n_" + l.source.id;
          if(d3.select(id).style("display") == "none") {
              r = "none";
          } else {
              if(isset(l.target.negaposi)) {
	          if(l.target.negaposi == "n") r = "block";
              }
          }
          return r;
      }
   );
});

d3.select("#menu_negaposi").on("click", function() {
   d3.selectAll(".chunk").style("display",
       function(g) {
           if(isset(g.negaposi)) {
               if(g.negaposi != "f") {
	           return "block";
               }
           }
           return "none";
       }
   );
   d3.selectAll(".lchunk").style("display", "none");
   d3.selectAll(".lchunk").style("display",
      function(l) {
          var r = "none";
          var id = "#n_" + l.source.id;
          if(d3.select(id).style("display") == "none") {
              r = "none";
          } else {
              if(isset(l.target.negaposi)) {
	          if(l.target.negaposi != "f") r = "block";
              }
          }
          return r;
      }
   );

});

d3.select("#rayer1").on("click",function() {
    d3.selectAll(".r1").style("display", "block"); 
    d3.selectAll(".r2").style("display", "none"); 
    d3.selectAll(".r3").style("display", "none");
    d3.selectAll("line.link").style("display", function(l) {
        if(isset(l.source.params)) {
            if(l.source.params.rayer == "1") {
                var id = "#n_" + l.target.id;
                d3.select(id).style("display", "block");
                return "inline";
            }
        } 
        if(isset(l.target.params)) {
            if(l.target.params.rayer == "1") {
                var id = "#n_" + l.source.id;
                d3.select(id).style("display", "block");
                return "inline";
            }
        }
        return "none"; 
    });
    d3.selectAll(".chunk").style("display", "none"); 
    d3.selectAll(".lchunk").style("display", "none");
});

d3.select("#rayer1").on("click", function() {
})

d3.select("#rayer2").on("click",function() {
    d3.selectAll(".r1").style("display", "none"); 
    d3.selectAll(".r2").style("display", "block"); 
    d3.selectAll(".r3").style("display", "none");
    d3.selectAll("line.link").style("display", function(l) {
        if(isset(l.source.params)) {
            if(l.source.params.rayer == "2") {
                var id = "#n_" + l.target.id;
                d3.select(id).style("display", "block");
                return "inline";
            }
        } 
        if(isset(l.target.params)) {
            if(l.target.params.rayer == "2") {
                var id = "#n_" + l.source.id;
                d3.select(id).style("display", "block");
                return "inline";
            }
        }
        return "none"; 
    });
    d3.selectAll(".chunk").style("display", "none"); 
    d3.selectAll(".lchunk").style("display", "none"); 
});

d3.select("#rayer3").on("click",function() {
    d3.selectAll(".r1").style("display", "none"); 
    d3.selectAll(".r2").style("display", "none"); 
    d3.selectAll(".r3").style("display", "block");
    d3.selectAll("line.link").style("display", function(l) {
        if(isset(l.source.params)) {
            if(l.source.params.rayer == "3") {
		var id = "#n_" + l.target.id;
		d3.select(id).style("display", "block");
                return "inline";
            }
        } 
        if(isset(l.target.params)) {
            if(l.target.params.rayer == "3") {
                var id = "#n_" + l.source.id;
                d3.select(id).style("display", "block");
                return "inline";
            }
        }
        return "none"; 
    });
    d3.selectAll(".chunk").style("display", "none"); 
    d3.selectAll(".lchunk").style("display", "none"); 
});

d3.select("#rayer4").on("click",function() {
    d3.selectAll("line.link").style("display", "block");
    d3.selectAll("g.node").style("display", "block");
    d3.selectAll(".r1").style("display","block");
    d3.selectAll(".r2").style("display","block");
    d3.selectAll(".r3").attr("display","block");
});


function getHeightByRayer(rayerId) {
    switch(rayerId) {
        case 1:
            return 800;
            break;
        case 2:
            return 500;
            break;
        case 3:
            return 200;
            break;
        default:
            return 0;
    }
}
