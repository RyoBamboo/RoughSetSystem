/***********************************************************************/
/* Count Function is like PHP's one.                                   */
/***********************************************************************/
var count = function(obj) {
	var cnt = 0;
	for (var key in obj) {
	cnt++;
	}
	return cnt;
}

/***********************************************************************/
/* Isset Function is like PHP's one.                                   */
/***********************************************************************/
function isset( data ){
	return ( typeof( data ) != 'undefined' );
}

/**********************************************************************/
/* Filter                                                             */
/**********************************************************************/
function showFilter() {
	if(!document.getElementById("filter")){
	    $("body").append("<div id=\"filter\"></div>");
	}else{
	    $("#filter").show();
	}	
}

function hideFilter() {
	$("#filter").hide();
}


/**********************************************************************/
/* SplitStr                                                           */
/**********************************************************************/
function SplitStr(str, header, footer) {
        var h = str.indexOf(header , 0);
        if(h === -1) return "";
        h += count(header);
        var str2 = str.substring(h);
        var f = str2.indexOf(footer, 0);
        if(f === -1) return "";

        return str2.substring(0, f);
}
