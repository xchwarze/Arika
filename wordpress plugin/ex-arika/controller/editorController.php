<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class editorController
{
	public function indexAction() {
		$id = isset($_GET['id']) ? $_GET['id'] : 1;
		$params = Tools::genParams('index', 'index', '');
		$renderData = Tools::genResults($params, $id);
		require BASEFOLDER . '/templates/editor/index.php';
	}

	public function getsubsAction() {
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
		//$pag = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;
		$start = isset($_GET['iDisplayStart']) ? (int)$_GET['iDisplayStart'] : 0;
		$max = isset($_GET['iDisplayLength']) ? (int)$_GET['iDisplayLength'] : EX_MAX_EDITOR_RESULT;

		//$limit = $max;
		//if ($pag > 1)
			//$limit = ($pag - 1) * $max . ',' . $max;
		$limit = "{$start}, {$max}";

		$output = array();
		$output['sEcho'] = (int)(isset($_GET['sEcho']) ? $_GET['sEcho'] : 0);
		$output['iTotalRecords'] = Integration::queryVar(
			'SELECT COUNT(start) ' .
			'FROM {{PREFIX}}arika_subtitle_content ' .
			'WHERE subID = %d AND is_bk = 0', array($id)
		);
		$output['iTotalDisplayRecords'] = $output['iTotalRecords'];
		
		$subtitles = Tools::getSubs($id, $limit);
		$output['lines'] = array();
		foreach ($subtitles as $value) {
			$output['lines'][] = array(
				$value['subcontID'], $value['start'], $value['end'], 
				$value['original_text'], $value['translated_text'], $value['done']
			);
		}

		$output['substart'] = $subtitles[0]['start'];
		$subtitles = end($subtitles);
		$output['subend'] = $subtitles['end'];
		
		echo json_encode($output);
	}

	public function getplayersubsAction() {
		ini_set('default_charset', 'ansi');

		$subID = isset($_GET['id']) ? $_GET['id'] : 1;
		$querydata = Tools::getSubs($subID);
		//$subtitle = Tools::genWEBVTT($querydata);
		$subtitle = Tools::genSRT($querydata);
		unset($querydata);

		//header('Content-Type: text/vtt');
		header('Content-Type: application/x-subrip');
		header('Accept-Ranges: bytes');
		header('Connection: close');
		exit($subtitle);
	}

	public function getthislineAction() {
		$subtitles = array(
			0 => array(
				'start' => $_GET['start'],
				'end' => $_GET['end'],
				'translated_text' => $_GET['text']
			)
		);
		//exit( Tools::genWEBVTT($subtitles) );
		exit( Tools::genSRT($subtitles) );
	}

	public function toolsAction() {
		$subID = isset($_GET['id']) ? $_GET['id'] : false;
		$subcontID = isset($_POST['id']) ? $_POST['id'] : false;

		//TODO aplicar niveles de permisos a esto
		if (!isset($_POST['func']) || Integration::isLogged())
			exit;

		//TODO ver el estado del sub por el $subID para saber que acciones puede hacer y cuales no
		switch ($_POST['func']) {
			case 'setdone':
				$this->_setDone($subcontID);
				break;
			case 'setdelete':
				$this->_setDelete($subcontID);
				break;
			case 'setedit':
				$this->_setEdit($subID, $subcontID);
				break;
		}
	}

	private function _setEdit($subID, $subcontID) {
		try {
			//rowId=1&columnPosition=3&columnId=4&columnName=New+Text

			$sub['start'] = '';
			$sub['end'] = '';
			$sub['original_text'] = '';
			$sub['translated_text'] = '';
			if ($subcontID) {
				$sub = Integration::queryRow(
					'SELECT start, end, original_text, translated_text FROM {{PREFIX}}arika_subtitle_content WHERE subcontID = %d', 
					array($subcontID)
				);

				Integration::query(
					'UPDATE {{PREFIX}}arika_subtitle_content SET is_bk = 1 WHERE subcontID = %d',
					array($subcontID)
				);
			}

			$user = Integration::getUserData();
			$value = Tools::cleanTags($_POST['value']);
			switch ($_POST['columnId']) {
				case '1':
					$sub['start'] = $value;
					break;
				case '2':
					$sub['end'] = $value;
					break;
				case '3':
					$sub['translated_text'] = $value;
					break;
				case '4':
					$sub['translated_text'] = $value;
					break;
			}

			if (!$subID || ($_POST['columnId'] < 3 && Tools::validateTC($value)))
				throw new Exception('Data validation error');
				
			$sql = 'INSERT INTO {{PREFIX}}arika_subtitle_content (subID, userID, start, end, original_text, translated_text) ' .
				'VALUES (%d, %d, %s, %s, %s, %s)';
			$params = array(
				$subID, $user['userID'], $sub['start'], $sub['end'], $sub['original_text'], $sub['translated_text']
			);
			$newID = Integration::query($sql, $params);
			$this->_ajaxResp(true, '', true);
		} catch (Exception $e) {
			$this->_ajaxResp(false, $e->getMessage(), true);
		}
	}

	private function _setDone($subcontID) {
		$done = (isset($_POST['done']) && $_POST['done'] === '1' ? '1' : '0');

		$ret = Integration::query(
			'UPDATE {{PREFIX}}arika_subtitle_content SET done = %d WHERE subcontID = %d',
			array($done, $subcontID)
		);

		$this->_ajaxResp($ret, '');
	}

	private function _setDelete($subcontID) {
		$delete = (isset($_POST['undelete']) && $_POST['undelete'] === '1' ? '0' : '1');

		$ret = Integration::query(
			'UPDATE {{PREFIX}}arika_subtitle_content SET done = %d WHERE subcontID = %d',
			array($delete, $subcontID)
		);

		$this->_ajaxResp($ret, '', true);
	}

	private function _ajaxResp($ret, $message = '', $jeditable = false) {
		$status = 'ok';
		if ($ret == 0)
			$status = 'error';

		if ($jeditable)
			$response = "{$status}{$message}";
		else
			$response = json_encode(array('status' => $status, 'message' => $message));
		
		exit($response);
	}
}