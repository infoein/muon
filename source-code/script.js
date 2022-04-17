/*<![CDATA[*/

// DOM 

function gid(a) {
    return document.getElementById(a)
}

function gval(a) {
    return gid(a).value
}

function add_cl(a, c, b) {
    if (typeof c.length !== "undefined") {
        for (i = 0; i < c.length; i++) {
            add_cl(a, c[i])
        }
    } else {
        if (c.className.indexOf(a) < 0) {
            c.className = c.className + " " + a
        }
    }
}

function rem_cl(a, c) {
    if (typeof c.length !== "undefined") {
        for (var b = 0; b < c.length; b++) {
            rem_cl(a, c[b])
        }
    } else {
        c.className = c.className.replace(a, "").replace(/  /g, " ")
    }
}

function arr_cl(a, d, c) {
    add_cl(a[d], c);
    for (var b = 0; b < d; b++) {
        rem_cl(a[b], c)
    }
    for (var b = d + 1; b < a.length; b++) {
        rem_cl(a[b], c)
    }
}

// Editor

function editor_adjust(g, e) {
    g = g || false;
    e = e || false;
    var z = false;
    if (e) {
        var z = e.which || e.keyCode;
        var z = e.ctrlKey ? e.ctrlKey : ((z === 17) ? true : false);
        if (z) {
            z = true
        } else {
            z = false
        }
    }
    var b = gid("text-editor");
    var f = gid("text-editor-sizeadj");
    var e = gid("text-editor-numbers");
    var h = 20;
    var k = 1;
    var j = h;
    f.style.height = (b.offsetHeight - h) + "px";
    rem_cl("hidden", f);
    b.style.height = j + "px";
    j += b.scrollHeight;
    b.style.height = j + "px";
    e.style.height = (j + 5 * h) + "px";
    k = j / h;
    var a = "<br/><br/><br/><br/><br/>1";
    for (var c = 2; c <= k; c++) {
        a = a + "<br/>" + c
    }
    e.innerHTML = a;
    e.style.marginTop = "-" + (j + 5 * h) + "px";
    add_cl("hidden", f);
    if (z) {
        return
    }
    if (!g) {
        document.title = "* " + mu_session_title;
        var d = new Array("editor-changed", "editor-not-changed");
        arr_cl(d, 0, gid("layout"))
    }
}

function editor_discard(a) {
    if (confirm("Discard changes?")) {
        location.href = a
    }
}

function editor_text_wrap(b) {
    var a = new Array("editor-text-nowrap", "editor-text-wrap");
    var c = 0;
    if (b) {
        c = 1
    }
    arr_cl(a, c, gid("layout"));
    editor_adjust(true)
}

// Select items while browsing

function selected_qty(b, e) {
    b = b || "total";
    e = e || false;
    if (b == "one") {
        var a = document.getElementsByClassName("browse-chk");
        var d = 0;
        for (var c = 0; c < a.length; c++) {
            if (a[c].checked == true) {
                return a[c].value
            }
        }
        return false
    }
    if (b == "total") {
        return selected_qty("folder", e) + selected_qty("file", e)
    }
    if (b == "arr") {
        return new Array(selected_qty("folder", e), selected_qty("file", e))
    }
    var a = document.getElementsByClassName("browse-chk-" + b);
    var d = 0;
    if (e) {
        for (var c = 0; c < a.length && d < 2; c++) {
            if (a[c].checked == true) {
                d++
            }
        }
        return d
    }
    for (var c = 0; c < a.length; c++) {
        if (a[c].checked == true) {
            d++
        }
    }
    return d
}

function select_browse_update() {
    var f = selected_qty("folder");
    var a = selected_qty("file");
    var e = f + a;
    var g = "" + e + " files";
    var h = gid("layout");
    var c = new Array("browse-select-nothing", "browse-select-one-folder", "browse-select-one-file", "browse-select-big-selection");
    switch (e) {
        case 0:
            tree_visibility("close");
            arr_cl(c, 0, h);
            break;
        case 1:
            if (f) {
                g = "folder";
                arr_cl(c, 1, h)
            } else {
                g = "file";
                arr_cl(c, 2, h)
            }
            gid("input-rename").value = selected_qty("one").substring(1);
            break;
        default:
            arr_cl(c, 3, h)
    }
    var b = document.getElementsByClassName("live-files-qty");
    for (var d = 0; d < b.length; d++) {
        b[d].innerHTML = g
    }
}

function select_browse(a) {
    var b = document.getElementsByClassName("browse-chk");
    if (a == "all") {
        for (var c = 0; c < b.length; c++) {
            b[c].checked = true
        }
    } else {
        if (a == "none") {
            for (var c = 0; c < b.length; c++) {
                b[c].checked = false
            }
        } else {
            if (a == "invert") {
                for (var c = 0; c < b.length; c++) {
                    b[c].checked = !b[c].checked
                }
            } else {
                gid("browse-chk-" + a).checked = true
            }
        }
    }
    select_browse_update()
}

function select_browse_rightclick(a) {
    select_browse("none");
    if (a != false) {
        select_browse(a)
    }
}


// Tree

function tree_meaning(a) {
    var c = gid("layout");
    var b = new Array("tree-browse", "tree-copy", "tree-cut");
    if (a == "copy") {
        arr_cl(b, 1, c)
    } else {
        if (a == "cut") {
            arr_cl(b, 2, c)
        } else {
            arr_cl(b, 0, c)
        }
    }
}

function tree_visibility(a) {
    var b = gid("layout");
    if (a == "open") {
        add_cl("tree-visible", b)
    } else {
        rem_cl("tree-visible", b)
    }
    tree_meaning()
}

function tree_open(c, a) {
    var b = new Array(gid("tree-li-" + c), gid("tree-ul-" + c));
    if (c == "all") {
        b = document.getElementsByClassName("tree-li-ul")
    }
    if (a == "open") {
        add_cl("tree-open", b)
    } else {
        rem_cl("tree-open", b)
    }
}

// Tools

function tool_inpt_show(a) {
    rem_cl("tool-mobile-visible", document.getElementsByClassName("tool-mobile-visible"));
    if (a != false) {
        add_cl("tool-mobile-visible", gid("tool-inpt-" + a))
    }
}

function tool_copy() {
    if (selected_qty() == 0) {
        return
    }
    tree_visibility("open");
    tree_meaning("copy");
    gid("paste-req-meaning").value = "copy"
}

function tool_cut() {
    if (selected_qty() == 0) {
        return
    }
    tree_visibility("open");
    tree_meaning("cut");
    gid("paste-req-meaning").value = "cut"
}

function paste_tool(a) {
    request_send("paste__" + a)
}

function tool_delete() {
    if (confirm("Delete selected files?")) {
        request_send("delete")
    }
}

function tool_perms() {
    if (gval("input-perms") == "") {
        return
    }
    request_send("perms")
}

function tool_zip() {
    request_send("zip")
}

function tool_rename() {
    if (gval("input-rename") == "") {
        return
    }
    request_send("rename")
}

function tool_unzip() {
    request_send("unzip")
}

function tool_newfolder() {
    if (gval("input-newfolder") == "") {
        return
    }
    request_send("newfolder")
}

function tool_newfile() {
    if (gval("input-newfile") == "") {
        return
    }
    request_send("newfile")
}

function tool_edit_save() {
    request_send("editsave")
}

function tool_upload() {
    request_send("upload", "form-upload")
}

// Upload ux

function upload_add() {
    var b = gid("upload-inputs");
    var e = b.getElementsByClassName("upload-file-input").length + 1;
    var a = document.createElement("INPUT");
    a.setAttribute("type", "file");
    a.setAttribute("name", "upload_file_" + e);
    a.setAttribute("id", "upload-file-" + e);
    a.setAttribute("class", "upload-file-input");
    a.setAttribute("onchange", "upload_change(" + e + ")");
    b.insertBefore(a, b.firstChild);
    b = gid("upload-labels");
    var d = document.createElement("div");
    var c = '<div class="item inpt upload-label" id="upload-label-' + e + '"> 						<table class="tab tab-row"> 							<tr> 								<td class="intext"><input type="text" readonly onclick="upload_choose(' + e + ')" id="upload-label-name-' + e + '" placeholder="Choose file..." /></td> 								<td class="submit ns"><a title="Choose file" onclick="upload_choose(' + e + ')" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-file" /></svg></a></td> 								<td class="submit ns"><a title="Remove" onclick="upload_remove(' + e + ')" class="btnic"><svg class="svg-ic"><use xlink:href="#ic-close" /></svg></a></td> 							</tr> 						</table> 					</div>';
    d.innerHTML = c;
    b.insertBefore(d.firstChild, b.lastChild)
}

function upload_change(d) {
    var c = gval("upload-file-" + d);
    if (c == "") {
        if (gval("upload-label-name-" + d) == "") {
            return
        }
        gid("upload-label-name-" + d).value = "";
        var a = document.getElementsByClassName("upload-label").length;
        if (a > 1) {
            gid("upload-file-" + d).disabled = true;
            if (gval("upload-label-name-" + d) == "") {
                var b = gid("upload-label-" + d)
            }
            b.parentNode.removeChild(b)
        }
        if (a > 2) {
            rem_cl("uploading-nothing", gid("layout"))
        } else {
            add_cl("uploading-nothing", gid("layout"))
        }
        return
    }
    rem_cl("uploading-nothing", gid("layout"));
    c = c.split("\\");
    c = c[c.length - 1].split("/");
    c = c[c.length - 1];
    if (gval("upload-label-name-" + d) == "") {
        upload_add()
    }
    gid("upload-label-name-" + d).value = c
}

function upload_choose(a) {
    gid("upload-file-" + a).click()
}

function upload_remove(a) {
    gid("upload-file-" + a).value = "";
    upload_change(a)
}

// XHR request handling

function request_send(c, b) {
    b = b || "form-main";
    var a = mu_requests_to;
    var d = new FormData(gid(b));
    var e = new XMLHttpRequest();
    d.append("req", c);
    e.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            request_done(this.responseText)
        }
    };
	
	if(c == "upload")
		e.upload.onprogress = function(e){
			var p = Math.ceil((e.loaded / e.total) * 100);
    		gid("response-output").innerHTML = "Upload progress: <b>"+p+"%</b>";
    		arr_cl(new Array("resp-none", "resp-error", "resp-success","resp-progress"), 3, gid("layout"));
		};

    e.open("POST", a, true);
    e.send(d)
}

function request_done(d) {
    var j = "Unknown error";
    var a = false;
    var f = true;
    var h = false;
    switch (d) {
        case "err_login":
            j = "You're not logged in!";
            break;
        case "err_guest":
            j = "Guest Session";
            a = "Permission denied.";
            f = false;
            break;
        case "rename_err_missing":
            j = "File not found";
            break;
        case "rename_err_invalid_name":
            j = "Invalid name";
            a = "File name can't contain special characters.";
            f = false;
            break;
        case "rename_err_already_existing":
            j = "Already in use";
            a = "Please, choose a different file name.";
            f = false;
            break;
        case "rename_err":
            j = "Unable to rename this file";
            a = "Check permissions.";
            f = false;
            break;
        case "rename_ok":
            h = true;
            j = "Succesfully renamed!";
            break;
        case "unzip_err_extension":
            j = "Missing extension";
            a = "Enable PHP Zip extension, please.";
            f = false;
            break;
        case "unzip_err_missing":
            j = "File not found";
            break;
        case "unzip_err_opening":
            j = "Unable to open zip file";
            a = "Is this a zip file?";
            f = false;
            break;
        case "unzip_err_extracting":
            j = "Unable to unzip";
            a = "Check permissions.";
            f = false;
            break;
        case "unzip_ok":
            h = true;
            j = "Succesfully unzipped!";
            break;
        case "delete_err":
            j = "The selected files can't be deleted";
            a = "Check permissions.";
            f = false;
            break;
        case "delete_ok":
            h = true;
            j = "Successfully deleted!";
            break;
        case "paste_err_destination":
            j = "Destination path not found";
            break;
        case "paste_err_cut_dest_inside_source":
            j = "Incorrect destination";
            a = "It's a subfolder of one of the items you're moving.";
            f = false;
            break;
        case "paste_err_copy":
            j = "Unable to copy one or more files";
            a = "Check permissions.";
            f = false;
            break;
        case "paste_err_cut":
            j = "Unable to move one or more files";
            a = "Check permissions.";
            f = false;
            break;
        case "paste_ok_copy":
            h = true;
            j = "Successfully copied!";
            break;
        case "paste_ok_cut":
            h = true;
            j = "Successfully moved!";
            break;
        case "perms_err_input":
            j = "Wrong input";
            a = "Type a valid perms string (e.g. 0775).";
            f = false;
            break;
        case "perms_err":
            j = "Unable to update permissions";
            f = false;
            break;
        case "perms_ok":
            h = true;
            j = "Permissions succesfully changed!";
            break;
        case "zip_err_extension":
            j = "Missing extension";
            a = "Enable PHP Zip extension, please.";
            f = false;
            break;
        case "zip_err":
            j = "Unable to create a zip archive";
            a = "Check permissions.";
            f = false;
            break;
        case "zip_ok":
            h = true;
            j = "Succesfully zipped!";
            break;
        case "newfolder_err_invalid_name":
            j = "Invalid name";
            a = "The name can't contain special characters.";
            f = false;
            break;
        case "newfolder_err_already_existing":
            j = "Already in use";
            a = "Please, choose a different folder name.";
            f = false;
            break;
        case "newfolder_err":
            j = "Unable to create the new folder";
            a = "Check permissions.";
            f = false;
            break;
        case "newfolder_ok":
            h = true;
            j = "Folder successfully created!";
            break;
        case "newfile_err_invalid_name":
            j = "Invalid name";
            a = "The name can't contain special characters.";
            f = false;
            break;
        case "newfile_err_already_existing":
            j = "Already in use";
            a = "Please, choose a different file name.";
            f = false;
            break;
        case "newfile_err":
            j = "Unable to create the new file";
            a = "Check permissions.";
            f = false;
            break;
        case "newfile_ok":
            h = true;
            j = "File successfully created!";
            break;
        case "upload_err":
            j = "Unable to upload files";
            a = "Maybe thery're too big.";
            f = false;
            break;
        case "upload_ok":
            h = true;
            j = "Succesfully uploaded!";
            break;
        case "editsave_err_location":
            j = "File not found";
            break;
        case "editsave_err":
            j = "Unable to save changes";
            a = "Check file's permissions.";
            f = false;
            break;
        case "editsave_ok":
            h = true;
            j = "Succesfully saved!";
            f = false;
            document.title = mu_session_title;
            arr_cl(new Array("editor-changed", "editor-not-changed"), 1, gid("layout"));
            break
    }
    var c = "<b> ERROR: " + j + "</b>";
    var e = gid("response-output");
    var g = new Array("resp-none", "resp-error", "resp-success","resp-progress");
    var b = 1;
    if (h) {
        b = 2;
        c = "<b>" + j + "</b>"
    }
    if (a) {
        c = c + " &mdash; " + a
    }
    e.innerHTML = c;
    arr_cl(g, b, gid("layout"));
    if (f) {
        setTimeout(function() {
            location.reload()
        }, 700)
    } else {
        setTimeout(function() {
            arr_cl(g, 0, gid("layout"))
        }, 1500)
    }
}

// Ctrl + key shortcuts

function ctrl_on_textbox(a) {
    if (a.target.nodeName == "INPUT" && (a.target.type == "text" || a.target.type == "password") || a.target.nodeName == "TEXTAREA") {
        return true
    }
    return false
}

window.onload = function() {
	document.body.addEventListener("keydown", function(g) {
		g = g || window.event;
		var d = g.which || g.keyCode;
		var f = g.ctrlKey ? g.ctrlKey : ((d === 17) ? true : false);
		if (f) {
			switch (d) {
				case 82:
					location.reload();
					event.preventDefault();
					break;
				case 83:
					if(mu_session_status=="edit")
						tool_edit_save();
					event.preventDefault();
					break;
				case 67:
					if (mu_session_status=="edit") {
						return
					}
					if (ctrl_on_textbox(g)) {
						return
					}
					tool_copy();
					event.preventDefault();
					break;
				case 88:
					if (ctrl_on_textbox(g) || mu_session_status=="edit") {
						return
					}
					tool_cut();
					event.preventDefault();
					break;
				case 65:
					if (ctrl_on_textbox(g) || mu_session_status=="edit") {
						return
					}
					select_browse("all");
					event.preventDefault();
					break;
			}
		}
	}, false);
}




// Sort browse elements (BETA)
var sort_browse_elements_by__status = {iter_name:0,iter_size:0,iter_perms:0,currently_by:"name"}
function sort_browse_elements_by(optn){
	var list = document.getElementById("Sortable_Browse_Elements");
	var items = list.getElementsByClassName("browse-item");
	var itemsArr = [];
	for (i=0;i<items.length;i++){
		itemsArr[i] = {
			isdir:parseInt(items[i].getElementsByClassName("sortable_browse__isdir")[0].value),
			fname:items[i].getElementsByClassName("sortable_browse__name")[0].value,
			fsize:parseFloat(items[i].getElementsByClassName("sortable_browse__size")[0].value),
			perms:items[i].getElementsByClassName("sortable_browse__perms")[0].value.toString(),
			domobj:items[i]
		};
        itemsArr[i].dirfname = (1-itemsArr[i].isdir).toString()+""+itemsArr[i].fname;
	}
    // select option
	var cmpfunction;
    if (optn=="name"){
		if (sort_browse_elements_by__status.currently_by==optn)
			sort_browse_elements_by__status.iter_name++;
		if (sort_browse_elements_by__status.iter_name==1)
			cmpfunction = function(a, b) {return a.dirfname < b.dirfname;};//desc-dirs
		else if (sort_browse_elements_by__status.iter_name==2)
			cmpfunction = function(a, b) {return a.fname > b.fname;};//asc
		else if (sort_browse_elements_by__status.iter_name==3)
			cmpfunction = function(a, b) {return a.fname < b.fname;};//desc
		else{
			sort_browse_elements_by__status.iter_name=0;//Default
			cmpfunction = function(a, b) {return a.dirfname > b.dirfname;};//asc-dirs
        }
	}
    else if (optn=="size"){
		if (sort_browse_elements_by__status.currently_by==optn)
			sort_browse_elements_by__status.iter_size++;
		if (sort_browse_elements_by__status.iter_size==1)
			cmpfunction = function(a, b) {return a.fsize < b.fsize;};//desc
		else{
			sort_browse_elements_by__status.iter_size=0;//Default
			cmpfunction = function(a, b) {return a.fsize > b.fsize;};//asc
        }
	}
    else if (optn=="perms"){
		if (sort_browse_elements_by__status.currently_by==optn)
			sort_browse_elements_by__status.iter_perms++;
		if (sort_browse_elements_by__status.iter_perms==1)
			cmpfunction = function(a, b) {return a.perms < b.perms;};//desc
		else{
			sort_browse_elements_by__status.iter_perms=0;//Default
			cmpfunction = function(a, b) {return a.perms > b.perms;};//asc
        }
	}
	sort_browse_elements_by__status.currently_by=optn;
	// sort
	for (var i=0;i<itemsArr.length-1;i++)
		for (var j=i+1;j<itemsArr.length;j++){
			//console.log([i,j]);
			if (cmpfunction(itemsArr[i],itemsArr[j])){
				var t = itemsArr[i];
				itemsArr[i] = itemsArr[j];
				itemsArr[j] = t;
	        }
         }
	// print
	for (var i=0;i<itemsArr.length;i++)
		list.appendChild(itemsArr[i].domobj)
}





/*]]>*/