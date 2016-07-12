<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class uploadController
{
	public function indexAction() {
		$renderData = array('title' => '', 'season' => '', 'episode' => '', 'language' => '', 
			'original_language' => '', 'comment' => '', 'framerate' => '', 'status' => '');
		require BASEFOLDER . '/templates/upload/index.php';
	}

	public function editAction() {
		//TODO verificar que sea un usuario admin o el usuario que lo subio
		$renderData = Tools::loadSubData($_GET['id']);
		require BASEFOLDER . '/templates/upload/index.php';
	}

	public function deleteAction() {
		//TODO verificar que sea un usuario admin o el usuario que lo subio
		$params = array((int)$_GET['id']);
		Integration::query('DELETE FROM {{PREFIX}}arika_subtitles WHERE subID = %d', $params);
		Integration::query('DELETE FROM {{PREFIX}}arika_subtitle_content WHERE subID = %d', $params);
		//TODO mensaje
		Tools::redirect(Tools::genURI('index', 'index'));
	}

	public function uploadAction() {
		$user = Integration::getUserData();
		//TODO terminar/mejorar validador
		if ($user === false || !isset($_POST) || $_FILES['subfile']['error'] > 0)
			exit('error with upload');
		
		require BASEFOLDER . '/include/srtfile.php';
		$subtitle = new srtFile($_FILES['subfile']['tmp_name']);
		unlink($_FILES['subfile']['tmp_name']);

		$sql = 'INSERT INTO {{PREFIX}}arika_subtitles (userID, title, season, episode, language,' .
			'original_language, comment, framerate, status, total_lines) VALUES (%d, %s, %d, %d, %d, %d, %s, %d, %d, %d)';
		$params = array(
			$user['userID'], $_POST['title'], $_POST['season'], $_POST['episode'], $_POST['language'], 
			$_POST['original_language'], $_POST['comment'], $_POST['framerate'], $_POST['status'], $subtitle->getSubCount()
		);
		$subID = Integration::query($sql, $params);

		//TODO si es un edit hacer que borre los subtitulos
		/*if () {
			Integration::query('DELETE FROM {{PREFIX}}arika_subtitle_content WHERE subID = %d', $params);
		}*/

		$completed = ($_POST['status'] == 1); //TODO si cambia el id de completado esto fallaria
		foreach ($subtitle->getSubs() as $value) {
			$text = Tools::cleanTags($value['text']);
			//$text = utf8_encode($value['text']);
			//elseif ($charset != 'u') $text = cyrillic2utf($value['text'], $charset);
			
			$original_text = $text;
			$translated_text = '';
			if ($completed) {
				$original_text = '';
				$translated_text = $text;
			}

			$sql = 'INSERT INTO {{PREFIX}}arika_subtitle_content (subID, userID, start, end, original_text, translated_text) ' .
				'VALUES (%d, %d, %s, %s, %s, %s)';
			$params = array($subID, $user['userID'], $value['startTC'], $value['stopTC'], $original_text, $translated_text);
			Integration::query($sql, $params);
		}
	}
}
