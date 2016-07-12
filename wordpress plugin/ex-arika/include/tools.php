<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
class Tools
{
	// functions used in subs 
	/**
	 * Converts timecode string into milliseconds
	 *
	 * @param string $tc timecode as string 
	 * @return int
	 */
	public static function tc2ms($timecode){
		$split = explode(':', $timecode);
		
		$value = $split[0] * 3600000;//60 * 60 * 1000
		$value += $split[1] * 60000;//60 * 1000 
		$value += floatval(str_replace(',', '.', $split[2])) * 1000;

		return $value;
	}

	/**
	 * Converts milliseconds into timecode string
	 *
	 * @param int $ms
	 * @return string
	 */
	public static function ms2tc($ms){
		$tc_ms = round((($ms / 1000) - intval($ms / 1000)) * 1000);
		$x = $ms / 1000;
		$tc_seg = intval($x % 60);
		$x /= 60;
		$tc_min = intval($x % 60);
		$x /= 60;
		$tc_hour = intval($x % 24);
		
		return str_pad($tc_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($tc_min, 2, '0', STR_PAD_LEFT) . ':' . 
			   str_pad($tc_seg, 2, '0', STR_PAD_LEFT) . ',' . str_pad($tc_ms, 3, '0', STR_PAD_LEFT);
	}

	/**
	 * Validate timecode value
	 *
	 * @param string $tc
	 * @return boolean
	 */
	public static function validateTC($tc){
		return preg_match( '/^[0-9]*\:?[0-9]*\:?[0-9]*\,?[0-9]+$/', $tc);
	}

	/**
	 * Clean html tabs
	 *
	 * @param string $string
	 * @return string
	 */
	public static function cleanTags($string) {
	    $string = preg_replace('#<br\s*?/?>#i', PHP_EOL, $string);
	    return strip_tags($string);
	}

	/**
	 * Clean subtitle text
	 *
	 * @param string $text
	 * @return string
	 */
	public function subCleanText($text) {
		return str_replace(array("\n", "\r\r"), array("\r\n", "\r"), $text);
	}


	//helper functions
	public static function genParams($cont, $func, $extra = '') {
		if ($extra !== '')
			$extra = "&{$extra}";

		return "?page={$cont}&func={$func}{$extra}";
	}

	public static function genURI($cont, $func, $direct = false, $extra = '') {
		return Integration::getCurrentURL($direct) . self::genParams($cont, $func, $extra);
	}

	public static function redirect($url, $terminate = true, $statusCode = 302) {
		if (strpos($url, '/') === 0)
			$url = self::getHostInfo() . $url;

		header('Location: ' . $url, true, $statusCode);
		if($terminate)
			exit;
	}

	public static function getHostInfo() {
		$http = 'http';
		if 	(self::getIsSecureConnection())
			$http .= 's';

		return "{$http}://" . isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	}

	public static function getIsSecureConnection() {
		return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
	}
	
	public static function getRequestType() {
		return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
	}
	
	public static function getIsPostRequest() {
		return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
	}


	//arika functions	
	public static function genLanguages($selected = false) {
	    $languages = Integration::queryFetchAll('SELECT langID as id, lang_name as name FROM {{PREFIX}}arika_languages WHERE enabled = 1');
	    return self::genOptions($languages, $selected);
	}

	public static function genFramerates($selected = false) {
		$framerates = Integration::queryFetchAll('SELECT frID as id, fr_name as name FROM {{PREFIX}}arika_framerate WHERE enabled = 1');
	    return self::genOptions($framerates, $selected);
	}

	public static function genStatus($selected = false) {
		$framerates = Integration::queryFetchAll('SELECT statusID as id, status_name as name FROM {{PREFIX}}arika_status WHERE enabled = 1');
	    return self::genOptions($framerates, $selected);
	}

	public static function genOptions($options, $selected = false) {
		$html = '';
		foreach ($options as $value) {
			$html .= "<option value='{$value['id']}'>{$value['name']}</option>";
		}

		if ($selected)
			$html = str_replace("='$selected'", "='$selected' selected='selected'", $html);

	    echo $html;
	}

	public static function genResults($params, $page = 1, $mode = false, $max = false) {
		if (!$max)
			$max = EX_MAX_PAGE_RESULT;

		$page = (int)$page;
		$max = (int)$max;

		//TODO mode
		$where = '';
		$whereParams = false;

		$limit = $max;
		if ($page > 1)
			$limit = ($page - 1) * $max . ',' . $max;

		$renderData['total'] = Integration::queryVar("SELECT COUNT(subID) FROM {{PREFIX}}arika_subtitles {$where}", $whereParams);
		$renderData['subs'] = Integration::queryFetchAll(
			"SELECT subID, DATE_FORMAT(date, '" . EX_MYSQL_DATEFORMAT . "') AS fdate, title, season, " .
				"episode, downloads, comment, alt.lang_name AS tlang, ast.status_name AS sstatus " .
			"FROM {{PREFIX}}arika_subtitles AS asu " .
			"INNER JOIN {{PREFIX}}arika_languages AS alt ON asu.language = alt.langID " .
			"INNER JOIN {{PREFIX}}arika_status AS ast ON asu.status = ast.statusID " .
			"{$where} LIMIT {$limit}", $whereParams
		);

		$renderData['paginator'] = Integration::getPaginator($page, $renderData['total'], $params);

		return $renderData;
	}

	public function genWEBVTT($subtitles) {
		$line = 0;
		$sub = "WEBVTT\r\n";
		foreach ($subtitles as $value) {
			$line++;
			$sub .= "{$line}\r\n" .
				"{$value['start']} --> {$value['end']}\r\n" .
				self::subCleanText($value['translated_text']) . "\r\n\r\n";
		}
		
		return $sub;
	}

	public function genSRT($subtitles) {
		$line = 0;
		$sub = "";
		foreach ($subtitles as $value) {
			$line++;
			$sub .= "{$line}\r\n" .
				"{$value['start']} --> {$value['end']}\r\n" .
				self::subCleanText($value['translated_text']) . "\r\n";
		}
		
		return $sub;
	}

	public function getSubs($subID, $limitAt = false) {
		$limit = '';
		if ($limitAt)
			$limit = "LIMIT {$limitAt}";

		return Integration::queryFetchAll(
			'SELECT subcontID, start, end, original_text, translated_text, done ' .
			'FROM {{PREFIX}}arika_subtitle_content ' .
			"WHERE subID = %d AND is_bk = 0 ORDER BY start ASC {$limit}", array($subID)
		);
	}

	public function loadSubData($id) {
		$sql = 'SELECT title, season, episode, language, original_language, comment, framerate, status ' .
			'FROM {{PREFIX}}arika_subtitles WHERE subID = %d';
		return Integration::queryRow($sql, (int)$id);
	}
}