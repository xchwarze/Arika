<?php $url = Integration::getBaseURL() . '/wp-content/plugins/ex-arika'; ?>

<link href="<?php echo $url; ?>/js/css/jquery/jquery-ui.min.css?<?php echo EX_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo $url; ?>/js/css/jquery.dataTables.css?<?php echo EX_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo $url; ?>/js/css/demo_table.css?<?php echo EX_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo $url; ?>/js/css/demo_page.css?<?php echo EX_VERSION; ?>" rel="stylesheet" type="text/css">
<link href="<?php echo $url; ?>/js/css/arika.base.css?<?php echo EX_VERSION; ?>" rel="stylesheet" type="text/css">

<script src="<?php echo $url; ?>/js/jquery/jquery-1.12.3.min.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>
<script src="<?php echo $url; ?>/js/jquery/jquery-ui.min.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>
<script src="<?php echo $url; ?>/js/jquery/jquery.validate.min.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>

<script src="<?php echo $url; ?>/js/jquery/jquery.dataTables.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>
<script src="<?php echo $url; ?>/js/jquery/jquery.jeditable.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>
<script src="<?php echo $url; ?>/js/jquery/jquery.dataTables.editable.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>
<script src="<?php echo $url; ?>/player/jwplayer.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>

<input type="hidden" id="loaded_video" name="loaded_video" value="">
<input type="hidden" id="subId" name="subId" value="<?php echo $id; ?>">
<input type="hidden" id="pluginUrl" name="pluginUrl" value="<?php echo $url; ?>">
<script src="<?php echo $url; ?>/js/editor.js?<?php echo EX_VERSION; ?>" type="text/javascript"></script>

<div id="player">
	Video URL: 
	<input type="text" name="url_video" id="url_video" value="http://www.youtube.com/watch?v=v3Kcw0UrIFI"> 
	<input type="button" value="Load" onclick="loadPlayer()">	
</div>

<div id="epiw">
	<div id="substart">Start:<br>00:00:00.000</div>
	<div id="subcurrent">Current:<br>00:00:00.000</div>
	<div id="substop">End:<br>00:00:00.000</div>
</div>

<br>


<div id="subteditordiv">
<!-- Place holder where add and delete buttons will be generated -->
<button id="btnAddNewRow" class="add_row">Add</button>
<button id="btnDeleteRow" class="delete_row">Delete</button>

<!-- Custom form for adding new records -->
<form id="formAddNewRow" action="#" title="Add new record">
	<label for="start">Start</label><br />
	<input type="text" name="start" id="start" class="required" rel="1" />
	<br />
	<label for="stop">Stop</label><br />
	<input type="text" name="stop" id="stop" class="required" rel="2" />
	<br />
	<label for="text">Text</label><br />
	<input type="text" name="text" id="text" rel="3" />
	<br />
	<label for="newtext">New Text</label><br />
	<input type="text" name="newtext" id="newtext" class="required" rel="4" />
	
	<input type="hidden" name="dummy" value="" rel="0">
	<input type="hidden" name="dummy" value="" rel="5">
	<input type="hidden" name="func" value="setnew">
</form>

	<table cellpadding="0" cellspacing="0" border="0" class="display" id="ajaxeditor">
		<thead>
			<tr>
				<th>ID</th>
				<th>Start</th>
				<th>Stop</th>
				<th>Text</th>
				<th>New Text</th>
				<th>Tools</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>