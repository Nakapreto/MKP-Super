<?php if (!defined('ABSPATH')) {
	die('You are not authorized to access this');
}

class UrlSPLHelper {

	public function remove_trailing_slash_spl($url) {
		// Remove a barra no final da URL usando rtrim()
		$clean_url = rtrim($url, '/');

		// Retorna a URL sem a barra no final
		return $clean_url;
	}


}
