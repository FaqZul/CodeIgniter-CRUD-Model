<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @package		FaqZul/CodeIgniter-CRUD-Model
* @subpackage	Helpers
* @version 		3.2.1
*/
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Checks if a value exists in an array
 *
 * @param	mixed	$needle
 * @param	array	$haystack
 * @return	bool
 */
if ( ! function_exists('in_array_assoc')) {
	function in_array_assoc($needle, $haystack, $strict = FALSE) {
		$return = FALSE;
		foreach ($haystack as $k => $v) {
			if (in_array($k, $needle, $strict)) {
				$return = TRUE;
				break;
			}
		}
		return $return;
	}
}

/**
 * Finds whether a variable is an associative arrays
 *
 * @param	array	$arr
 * @return	bool
 */
if ( ! function_exists('is_array_assoc')) {
	function is_array_assoc($arr = array()) { return is_array($arr) && array_diff_key($arr, array_keys(array_keys($arr))); }
}

/**
 * Finds whether a variable is an multidimensional arrays
 *
 * @param	array	$arr
 * @return	bool
 */
if ( ! function_exists('is_array_multi')) {
	function is_array_multi($arr = array()) {
		rsort($arr);
		return isset($arr[0]) && is_array($arr[0]);
	}
}
