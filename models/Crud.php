<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @package		FaqZul/CodeIgniter-CRUD-Model
* @subpackage	Models
* @version 		development
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model {

	private $_error = array('code' => 0, 'message' => '');
	private $insert_id_key = NULL;
	private $insert_id_val = 0;
	protected $delete_record = TRUE;
	protected $log_query = FALSE;
	protected $track_trans = FALSE;

	public function __construct() {
		parent::__construct();
		$this->load->helper('crud');
		if ($this->config->load('crud', TRUE)) { $this->initialize($this->config->item('crud')); }
	}

	public function initialize(array $config = array()) {
		foreach ($config as $key => $val) {
			if (isset($this->$key)) { $this->$key = $val; }
		}
		return $this;
	}

	public function createData($table, $data, $callback = FALSE) {
		if (is_array_assoc($data) AND $this->track_trans === TRUE) {
			$data[$table . '_create_date'] = date('Y-m-d H:i:s');
			$data[$table . '_create_ip'] = $this->input->ip_address();
		}
		$this->db->insert($table, $data);
		$this->set_error($this->db->error());
		$this->set_insert_id($this->db->insert_id($this->insert_id_key));
		if ($this->log_query === TRUE) { $this->log($this->db->last_query()); }
		if ($callback) {
			$error = $this->error();
			$error['insert_id'] = $this->insert_id();
			return $error;
		}
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	public function readData($select, $from, $wheres = NULL, $joinTable = NULL, $groupBy = NULL, $orderBy = NULL, $limit = NULL) {
		$this->db->select($select, FALSE);
		$this->db->from($from);
		if (is_array_assoc($joinTable)) {
			foreach ($joinTable as $k => $v) { $this->db->join($k, $v); }
		}
		if (is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		if ($this->delete_record === FALSE) { $this->db->where($from . '_delete_date', NULL); }
		if (is_array($groupBy)) { $this->db->group_by($groupBy); }
		else if (is_string($groupBy) AND trim($groupBy) !== '') { $this->db->group_by($groupBy); }
		if (is_string($orderBy) AND trim($orderBy) !== '') { $this->db->order_by($orderBy); }
		if (is_array($limit) AND count($limit) <= 2) {
			if (is_numeric($limit[0]) AND ! empty($limit[1]) AND is_numeric($limit[1])) { $this->db->limit($limit[0], $limit[1]); }
			else if (is_numeric($limit[0])) { $this->db->limit($limit[0]); }
		}
		$query = $this->db->get();
		$this->set_error($this->db->error());
		$this->set_insert_id(0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		return ($this->error_message() !== '') ? FALSE: $query;
	}

	public function updateData($table, $data, $wheres, $callback = FALSE) {
		if (is_array_assoc($data) AND $this->track_trans === TRUE) {
			$data[$table . '_update_date'] = date('Y-m-d H:i:s');
			$data[$table . '_update_ip'] = $this->input->ip_address();
		}
		if (is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		$this->db->update($table, $data);
		$this->set_error($this->db->error());
		$this->set_insert_id(0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $this->error(); }
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	public function deleteData($table, $wheres, $callback = FALSE) {
		if (is_array_assoc($data) AND $this->delete_record === FALSE) {
			$data[$table . '_delete_date'] = date('Y-m-d H:i:s');
			$data[$table . '_delete_ip'] = $this->input->ip_address();
		}
		if (is_array_assoc($wheres)) { $this->db->where($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		($this->delete_record === FALSE) ? $this->db->update($table, $data): $this->db->delete($table);
		$this->set_error($this->db->error());
		$this->set_insert_id(0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $this->error(); }
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	public function error() { return $this->_error; }

	public function error_code() { return $this->_error['code']; }

	public function error_message() { return $this->_error['message']; }

	public function insert_id() { return $this->insert_id_val; }

	private function set_insert_id($var = 0) {
		$this->insert_id_val = (int) $var;
		return $this; 
	}

	protected function log($data = '') { $this->db->insert('log', array('log_ip' => $this->input->ip_address(), 'log_query' => $data, 'log_url' => ( ! isset($_SERVER['REDIRECT_URL'])) ? base_url(): $_SERVER['REDIRECT_URL'], 'log_datetime' => date('Y-m-d H:i:s'))); }

	protected function set_error($var = array()) {
		if (is_array_assoc($var) AND isset($var['code']) AND isset($var['message'])) {
			$this->_error['code'] = (int) $var['code'];
			$this->_error['message'] = trim($var['message']);
		}
		return $this;
	}

}
