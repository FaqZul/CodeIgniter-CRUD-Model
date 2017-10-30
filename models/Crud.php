<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @version 		3.0.0-dev
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model {

	protected $delete_record;
	protected $log_query;
	protected $track_trans;

	public function __construct() {
		parent::__construct();
		$this->delete_record = (is_bool($this->config->item('delete_record'))) ? $this->config->item('delete_record'): TRUE;
		$this->log_query = (is_bool($this->config->item('log_query'))) ? $this->config->item('log_query'): FALSE;
		$this->track_trans = (is_bool($this->config->item('track_trans'))) ? $this->config->item('track_trans'): FALSE;
	}

	public function createData($table, $data, $callback = FALSE) {
		if (is_array($data) AND count($data) > 0 AND $this->is_array_assoc($data) AND $this->track_trans) {
			$data[$table . '_create_date'] = date('Y-m-d H:i:s');
			$data[$table . '_create_ip'] = $this->input->ip_address();
		}
		$this->db->insert($table, $data);
		$error = $this->db->error();
		$insertID = $this->db->insert_id();
		$error['insert_id'] = $insertID;
		$this->session->set_flashdata('insert_id', $insertID);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $error; }
		else {
			if (trim($error['message']) !== '') {
				if ($this->input->is_ajax_request()) { $this->display_response($error, 500); }
				else { show_error($error['message'], 500, 'Error Code: ' . $error['code']); }
			}
			return TRUE;
		}
	}

	public function readData($select, $from, $wheres = NULL, $joinTable = NULL, $groupBy = NULL, $orderBy = NULL, $limit = NULL) {
		$this->db->select($select, FALSE);
		$this->db->from($from);
		if (is_array($joinTable) AND count($joinTable) > 0 AND $this->is_array_assoc($joinTable)) {
			foreach ($joinTable as $k => $v) { $this->db->join($k, $v); }
		}
		if (is_array($wheres) AND count($wheres) > 0 AND $this->is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		if ($this->delete_record === FALSE) { $this->db->where($from . '_delete_date', NULL); }
		if (is_array($groupBy) AND count($groupBy) > 0 AND ! $this->is_array_assoc($groupBy)) { $this->db->group_by($groupBy); }
		else if (is_string($groupBy) AND trim($groupBy) !== '') { $this->db->group_by($groupBy); }
		if (is_string($orderBy) AND trim($orderBy) !== '') { $this->db->order_by($orderBy); }
		if (is_array($limit) AND count($limit) <= 2 AND ! $this->is_array_assoc($limit)) {
			if (is_numeric($limit[0]) AND ! empty($limit[1]) AND is_numeric($limit[1])) { $this->db->limit($limit[0], $limit[1]); }
			else if (is_numeric($limit[0])) { $this->db->limit($limit[0]); }
		}
		$query = $this->db->get();
		$error = $this->db->error();
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if (trim($error['message']) !== '') {
			if ($this->input->is_ajax_request()) { $this->display_response($error, 500); }
			else { show_error($error['message'], 500, 'Error Code: ' . $error['code']); }
		}
		return $query;
	}

	public function updateData($table, $data, $wheres, $callback = FALSE) {
		if (is_array($data) AND count($data) > 0 AND $this->is_array_assoc($data) AND $this->track_trans) {
			$data[$table . '_update_date'] = date('Y-m-d H:i:s');
			$data[$table . '_update_ip'] = $this->input->ip_address();
		}
		if (is_array($wheres) AND count($wheres) > 0 AND $this->is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		$this->db->update($table, $data);
		$error = $this->db->error();
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $error; }
		else {
			if (trim($error['message']) !== '') {
				if ($this->input->is_ajax_request()) { $this->display_response($error, 500); }
				else { show_error($error['message'], 500, 'Error Code: ' . $error['code']); }
			}
			return TRUE;
		}
	}

	public function deleteData($table, $wheres, $callback = FALSE) {
		if (is_array($data) AND count($data) > 0 AND $this->is_array_assoc($data) AND $this->delete_record === FALSE) {
			$data[$table . '_delete_date'] = date('Y-m-d H:i:s');
			$data[$table . '_delete_ip'] = $this->input->ip_address();
		}
		if (is_array($wheres) AND count($wheres) > 0 AND $this->is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		($this->delete_record === FALSE) ? $this->db->update($table, $data): $this->db->delete($table);
		$error = $this->db->error();
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $error; }
		else {
			if (trim($error['message']) !== '') {
				if ($this->input->is_ajax_request()) { $this->display_response($error, 500); }
				else { show_error($error['message'], 500, 'Error Code: ' . $error['code']); }
			}
			return TRUE;
		}
	}

	private function is_array_assoc($array) { return array_keys($array) !== range(0, count($array) - 1); }

	private function display_response($data = NULL, $code = NULL, $continue = FALSE) {
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

	private function log($data = '') { $this->db->insert('log', array('log_ip' => $this->input->ip_address(), 'log_query' => $data, 'log_url' => ( ! isset($_SERVER['REDIRECT_URL'])) ? base_url(): $_SERVER['REDIRECT_URL'], 'log_datetime' => date('Y-m-d H:i:s'))); }

}
