<?php
/**
* Arika Subtitle Engine
* by DSR! 
* https://github.com/xchwarze/Arika
*/
//la idea es ir cambiando como se hacen estas funciones para pasar de plataforma el plugin
class Integration
{
	/**
	 * Is current user a mod or admin
	 *
	 * @return boolean
	 */
	public static function isMod() {
		/*
		global $user;
		return (isset($user->roles) && is_array($user->roles) && in_array('administrator', $user->roles))
		*/
		return is_super_admin();
	}

	/**
	 * Is a logged user?
	 *
	 * @return boolean
	 */
	public static function isLogged() {
		return (get_current_user_id() != 0);
	}

	/**
	 * Get current user info
	 *
	 * @return array|boolean
	 */
	public static function getUserData() {
		$user = wp_get_current_user();
		if ($user->ID == 0) {
			return false;
		}

		$ret['userID'] = $user->ID;
		$ret['username'] = $user->user_login;
		$ret['roles'] = $user->roles;
		return $ret;
	}

	/**
	 * Get current url
	 *
	 * @return string
	 */
	public static function getCurrentURL($direct = false) {
		global $wp;

		$params = '/wp-content/plugins/ex-arika/ajax.php';
		if (!$direct)
			$params = add_query_arg(array(), $wp->request);
		
		return home_url($params) . ($direct ? '' : '/');
	}

	/**
	 * Get base url
	 *
	 * @return string
	 */
	public static function getBaseURL() {
		return get_site_url();
	}

	/**
	 * Get paginator
	 *
	 * @return string
	 */
	public static function getPaginator($current, $total, $params) {
		return paginate_links(array(
			'base'      => self::getCurrentURL() . $params,
			//'format'    => '',
			/*'base'  => '%_%',
			'format'  => '?paged=%#%',*/
			'show_all'  => true,
			'current'   => $current,
			'total'     => $total,
			'end_size'  => 3,
			'mid_size'  => 3
		));
	}

	/**
	 * Change variables in query (ex {{PREFIX}})
	 *
	 * @param string $string sql query to translate 
	 * @return string
	 */
	public static function translateSQL($string) {
		global $wpdb;

		$search = array('{{PREFIX}}');
		$replace = array($wpdb->get_blog_prefix());
		return str_replace($search, $replace, $string);
	}

	/**
	 * Prepare query
	 *
	 * @param string $query sql query to parse
	 * @param array $params params to secure inject to sql
	 * @param bool $translate change variables (ex {{PREFIX}})
	 * @return string
	 */
	public static function prepare($query, $params, $translate) {
		if ($translate)
			$query = self::translateSQL($query);

		if ($params) {
			global $wpdb;
			$query = $wpdb->prepare($query, $params);
		}

		return $query;	
	}

	/**
	 * Exec query
	 *
	 * @param string $query sql to exec
	 * @param array $params params to secure inject to sql
	 * @param bool $translate change variables (ex {{PREFIX}})
	 * @return integer|boolean
	 */
	public static function query($query, $params = false, $translate = true) {
		global $wpdb;
		$query = self::prepare($query, $params, $translate);		
		return $wpdb->query($query);
	}

	/**
	 * Exec query and get only a var value
	 *
	 * @param string $query sql to exec
	 * @param array $params params to secure inject to sql
	 * @param bool $translate change variables (ex {{PREFIX}})
	 * @return value|NULL 
	 */
	public static function queryVar($query, $params = false, $translate = true) {
		global $wpdb;
		$query = self::prepare($query, $params, $translate);
		return $wpdb->get_var($query);
	}

	/**
	 * Exec query and get only a row
	 *
	 * @param string $query sql to exec
	 * @param array $params params to secure inject to sql
	 * @param bool $translate change variables (ex {{PREFIX}})
	 * @return array|NULL 
	 */
	public static function queryRow($query, $params = false, $translate = true) {
		global $wpdb;
		$query = self::prepare($query, $params, $translate);
		return $wpdb->get_row($query, ARRAY_A);
	}

	/**
	 * Exec query and fetch all results
	 *
	 * @param string $query sql to exec
	 * @param array $params params to secure inject to sql
	 * @param bool $translate change variables (ex {{PREFIX}})
	 * @return array
	 */
	public static function queryFetchAll($query, $params = false, $translate = true) {
		global $wpdb;
		$query = self::prepare($query, $params, $translate);
		
		/*TODO: Agregar uso de cache
		update_term_cache($terms);
		if ( empty($terms) ) {
			wp_cache_add( $cache_key, array(), 'terms', DAY_IN_SECONDS );*/

		return $wpdb->get_results($query, ARRAY_A);
	}
}