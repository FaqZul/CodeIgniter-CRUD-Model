<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @package		FaqZul/CodeIgniter-CRUD-Model
* @subpackage	Helpers
* @version 		3.0.0-dev
*/
defined('BASEPATH') or exit('No direct script access allowed');

if ( ! function_exists('display_response')) {
	function display_response($data = NULL, $code = NULL, $continue = FALSE) {
		$CI =& get_instance();
		if ($code !== NULL) { $code = (int) $code; }
		$a = NULL;
		if ($data === NULL && $code === NULL) { $code = 404; }
		else if ($data !== NULL) {
			$CI->output->set_content_type('application/json', strtolower($CI->config->item('charset')));
			$a = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		}
		$code > 0 || $code = 200;
		$CI->output->set_status_header($code);
		$CI->output->set_output($a);
		if ($continue === FALSE) {
			$CI->output->_display();
			exit;
		}
	}
}

if ( ! function_exists('is_array_assoc')) {
	function is_array_assoc($array) { return array_keys($array) !== range(0, count($array) - 1); }
}
