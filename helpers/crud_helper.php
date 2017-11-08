<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @package		FaqZul/CodeIgniter-CRUD-Model
* @subpackage	Helpers
* @version 		3.0.0
*/
defined('BASEPATH') or exit('No direct script access allowed');

if ( ! function_exists('is_array_assoc')) {
	function is_array_assoc($var) { return is_array($var) && array_diff_key($var, array_keys(array_keys($var))); }
}
