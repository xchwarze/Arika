var stopAt, startAt, currentTime = "";
var player, dataTable = false;
var pluginUrl = document.getElementById('pluginUrl').value;
var subId = document.getElementById('subId').value;
jwplayer.key = "77xwNIrTjdAJS7kliYgvONhVZyLEWyfBT9RD9A==";

function secToHMS(seconds) {
	var hr = Math.floor(seconds / 3600);
	var min = Math.floor((seconds - (hr * 3600)) / 60);
	var sec = seconds - ((hr * 3600) + (min * 60));

	hr = (hr) ? ":" + hr : "";
	min += "";
	sec += "";

	var parse_sec = sec.split(".");
	sec = parse_sec[0];
	var ms = (parse_sec.length == 2) ? parse_sec[1].substr(0, 3) : "";

	while (hr.length < 2) hr = "0" + hr;
	while (min.length < 2) min = "0" + min;
	while (sec.length < 2) sec = "0" + sec;
	while (ms.length < 3) ms = ms + "0";

	return hr + ":" + min + ":" + sec + "." + ms;
}
	
function hmsToSec(str) {
	var p = str.split(":"), s = 0, m = 1;
	while (p.length > 0) {
		s += m * parseInt(p.pop());
		m *= 60;
	}

	return s;
}

function loadPlayer() {
	document.getElementById("loaded_video").value = document.getElementById("url_video").value;
	setupPlayer(pluginUrl + "/ajax.php?page=editor&func=getplayersubs&id=" + subId);
}

function setupPlayer(srtfile) {
	player = false;
	player = jwplayer("player");
	player.setup({
		file: document.getElementById('loaded_video').value,
		image: pluginUrl + "/img/player_logo.jpg",
		width: 500,
		height: 305,
		tracks: [{ 
			file: srtfile, 
			kind: "captions",
			default: true 
		}],
		skin: {
			name: 'stormtrooper'
		}
	});

	player.onTime(function () {
		currentTime = jwplayer().getPosition();
		document.getElementById("subcurrent").innerHTML = secToHMS(currentTime);
		if (currentTime >= stopAt && stopAt != "") {
			jwplayer().pause();
			stopAt = "";
		}
	});

	jQuery(document).bind("keydown", function (event) {
		if (event.altKey) {
			//if (event.which == 81) jwplayer().getPosition() //letra Q bindeada //con esta leo el tiempo y lo pego en el input donde este parado el cursor
			if (event.which == 65) jwplayer().pause(); //letra A bindeada
			if (event.which == 83) jwplayer().seek(jwplayer().getPosition() + 0.15); //letra S bindeada
			if (event.which == 68) jwplayer().seek(jwplayer().getPosition() - 0.15); //letra D bindeada
		}
	});
}

function getRowData(t) {
	nTr = $(t).parents("tr")[0];
	return dataTable.fnGetData(nTr);
}

function execTestSub(t) {
	aData = getRowData(t);
	if (typeof player === "undefined" || player == false)
		return;

	setupPlayer(
		pluginUrl + "/ajax.php?page=editor&func=getthisline&id=" + subId + 
			"&start=" + encodeURIComponent(aData[1]) + 
			"&end=" + encodeURIComponent(aData[2]) + 
			"&text=" + encodeURIComponent(aData[4])
	);

	document.getElementById("substart").innerHTML = aData[1];
	document.getElementById("substop").innerHTML = aData[2];
	startAt = hmsToSec(aData[1]);
	stopAt = hmsToSec(aData[2]);

	//console.log(aData);
	jwplayer().play();	
	jwplayer().seek( hmsToSec(aData[1]) - 2 );
}
		
function setRowStatus(t, done) {
	done = (done == 0 ? 1 : 0);
	aData = getRowData(t);
	$.ajax({
		data: { func: 'setdone', id: aData[0], done: done }
	}).done(function( msg ) {
		alert(msg.status);

		if ($(t).hasClass("imgqaok"))
			$(t).removeClass("imgqaok").addClass("imgqaerror");
		else
			$(t).removeClass("imgqaerror").addClass("imgqaok");
	});
}


$(document).ready(function () {
	$.ajaxSetup({
		cache: false,
		type: "POST",
		url: pluginUrl + "/ajax.php?page=editor&func=tools&id=" + subId
	});

	//TODO sacar de los datos de este ajax los tiempos cuando se tiene que parar
	//el player y cambiar de pagina
	dataTable = $("#ajaxeditor").dataTable({
		bPaginate: true,
		bProcessing: true,
		bServerSide: true,
		bFilter: false, 
		bInfo: false,
		sAjaxDataProp: "lines",
		aoColumnDefs: [
			{ bVisible: false, aTargets: [0] }
		],
		fnRowCallback: function( nRow, aData, iDisplayIndex ) {
			cname = 'imgqaok';
			if (aData[5] === '1')
				cname = 'imgqaerror';

			$('td:eq(4)', nRow).html(
				'<div class="minimenu">' +
				'	<span class="imgbase imgreplay" onclick="execTestSub(this);" title="Replay" ></span>' +
				//'	<span class="imgbase imgadd" onclick="execAdd(this);" title="Add" ></span>' +
				//'	<span class="imgbase imgdelete" onclick="execDelete(this);" title="Delete" ></span>' +
				'	<span class="imgbase ' + cname + '" onclick="setRowStatus(this, \'' + aData[5] + '\');" title="Done" ></span>' +
				'</div>'
			);
		},
		sAjaxSource: pluginUrl + "/ajax.php?page=editor&func=getsubs&id=" + subId
	}).makeEditable({
		sUpdateURL: pluginUrl + "/ajax.php?page=editor&func=tools&id=" + subId,
		sDeleteURL: pluginUrl + "/ajax.php?page=editor&func=tools&id=" + subId,
		sAddURL: pluginUrl + "/ajax.php?page=editor&func=tools&id=" + subId,
		oDeleteParameters: { func: "setdelete" },
		oUpdateParameters: { func: "setedit" },
		sSuccessResponse: "ok",
		sFailureResponsePrefix: "error",
		aoColumns: [
			{ indicator: "Saving...", tooltip: "Click to edit" }, 
			{ indicator: "Saving...", tooltip: "Click to edit" }, 
			null,
			{ indicator: "Saving...", tooltip: "Click to edit", type: "textarea", submit: "Save" }, 
			null
		],
		/*fnShowError: function (message, action) {
			switch (action) {
				case "update":
					jAlert(message, "Update failed");
					break;
				case "delete":
					jAlert(message, "Delete failed");
					break;
				case "add":
					$("#lblAddError").html(message);
					$("#lblAddError").show();
					break;
			}
		},
		fnStartProcessingMode: function () {
			$("#processing_message").dialog();
		},
		fnEndProcessingMode: function () {
			$("#processing_message").dialog("close");
		},
		fnOnDeleting: function (tr, id, fnDeleteRow) {
			jConfirm('Please confirm that you want to delete row with id ' + id, 'Confirm Delete', function (r) {
				if (r) 
					fnDeleteRow(id);
			});
			return false;
		}*/
	});
});
