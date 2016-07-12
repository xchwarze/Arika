
<header class="entry-header">
	<h1 class="entry-title">Last added</h1>
</header>

<?php 
$url = Integration::getCurrentURL();
foreach ($renderData['subs'] as $value) {
?>
	<div id="asubs_title">
		<div id="asubs_title_text">
			<a class="asubs_text" href="<?php echo $url . Tools::genParams('index', 'view', "id={$value['subID']}"); ?>">
				<?php echo $value['title']; ?>
			</a>

			<?php 
			//TODO faltaria un AND para que solo sea si es editable
			if (Integration::isLogged()) {
			?>
				[EDIT]
			<?php
			}
			?>
		</div>
	</div>
	<div id="asubs_details">
		<div id="asubs_details_text">
			<?php echo $value['comment']; ?>
		</div>
		<div id="asubs_details_info">
			<b>Language:</b> <?php echo $value['tlang']; ?> 
			<b>Downloads:</b> <?php echo $value['downloads']; ?> 
			<b>Status:</b> <?php echo $value['sstatus']; ?> 
			<b>Uploader:</b> <?php echo $value['username']; ?> 
			<b>Date:</b> <?php echo $value['fdate']; ?>
		</div>
	</div>
<?php
}

echo $renderData['paginator'];

?>

<style type="text/css">
#asubs_title {
	float:left;
	text-align:left;
	background-color:#444;
	height:26px;
	width:735px;
	margin-left:10px;
}

#asubs_title_text {
	float:left;
	margin-top:3px;
	font-size:14px;
	font-weight:700;
	margin-left:10px;
}

#asubs_details {
	background-color:#777;
	float:left;
	width:735px;
	margin-left:10px;
	font-size:10px;
	padding-bottom:15px;
}

#asubs_details_text {
	font-size:12px;
	padding-top:5px;
	margin-left:10px;
}

#asubs_details_info {
	font-size:11px;
	padding-top:5px;
	padding-bottom:3px;
	margin-left:10px;
}

.asubs_text {
	text-decoration:none;
	text-align:left;
	font-size:14px;
	font-weight:700;
	margin-top:5px;
}
</style>
