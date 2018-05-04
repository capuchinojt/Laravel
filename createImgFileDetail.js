//Insert Image File button 
if(typeof i == 'undefined')
	var i = 2;

function createImgDetail(id, max) {
	if (i == max + 1) {
		alert("Can not insert image anymore!!");
		return false;
	}
    var form = "<div class='form-group'><label for='imgDetail'>Image Detail -" + i + "-</label><input type='file' name='imgDetail[]'></div>";
    document.getElementById(id).innerHTML = document.getElementById(id).innerHTML + form;
    i++;
};