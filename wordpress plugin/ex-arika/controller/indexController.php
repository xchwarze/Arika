<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class indexController
{
	public function indexAction() {
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$params = Tools::genParams('index', 'index', '');
		$renderData = Tools::genResults($params, $page);
		require BASEFOLDER . '/templates/index/index.php';
	}

	public function viewAction() {
		$sub = isset($_GET['id']) ? $_GET['id'] : 1;
		$renderData = Tools::genResults($params, $page);
		require BASEFOLDER . '/templates/index/view.php';
	}

	public function previewAction() {
		$subID = isset($_GET['id']) ? $_GET['id'] : 1;
		$querydata = Tools::getSubs($subID, 10);
		//$subtitle = Tools::genWEBVTT($querydata);
		$subtitle = Tools::genSRT($querydata);
		unset($querydata);
		
		//TODO terminar....
	}

	public function downloadAction() {
		$subID = isset($_GET['id']) ? $_GET['id'] : 1;
		$subname = str_replace(' ', '_', $info['title']) . '-' . $subID;
		
		// counter filter
		session_start();
		$ids = isset($_SESSION['subs']) ? $_SESSION['subs'] : array();
		if (!in_array($subID, $ids)) {
			array_push($ids, $subID);
			$_SESSION['subs'] = $ids;

			Integration::query('UPDATE {{PREFIX}}arika_subtitles SET downloads = downloads + 1 WHERE subID = %d', $subID);
		}

		$querydata = Tools::getSubs($subID);
		//$subtitle = Tools::genWEBVTT($querydata);
		$subtitle = Tools::genSRT($querydata);
		unset($querydata);

		//download file
		require BASEFOLDER . '/classes/zip.php';
		$zip = new PHPZip;
		$zip->addFile($subtitle, $subname . '.srt');

		header('Content-type: application/octet-stream');
		header('Accept-Ranges: bytes');
		//header('Accept-Length: ' . strlen($subtitle));
		header('Content-Disposition: attachment;filename=' . $subname . '.zip');
		exit( $zip->file() );
	}
}