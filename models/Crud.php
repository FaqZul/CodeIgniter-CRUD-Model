<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright 	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @version 		development
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model {

	protected $delete_record;

	public function __construct() {
		parent::__construct();
		$this->delete_record = (is_bool($this->config->item('delete_record'))) ? $this->config->item('delete_record'): TRUE;
	}

	public function createData($table, $data) {
		$data[$table . '_create_date'] = date('Y-m-d H:i:s');
		$data[$table . '_create_ip'] = $this->input->ip_address();
		$this->db->insert($table, $data);
		return $this->db->error();
	}

	public function readData($select, $from, $where, $joinTable, $groupBy, $order, $orderBy, $limit = NULL) {
		$this->db->select('SQL_CALC_FOUND_ROWS ' . $select, FALSE);
		$this->db->from($from);
		if (count($joinTable > 0)) {
			foreach ($joinTable as $join) {
				$this->db->join($join['table'], $join['condition'], ( ! isset($join['type'])) ? 'LEFT': $join['type']);
			}
		}
		$this->db->where($where);
		if ($this->delete_record === FALSE) { $this->db->where($from . '_delete_date', NULL); }
		if (trim($groupBy) !== '') { $this->db->group_by($groupBy); }
		$this->db->order_by($order, $orderBy);
		if (is_array($limit)) {
			$this->db->limit((isset($limit['limit'])) ? $limit['limit']: 10, (isset($limit['offset'])) ? $limit['offset']: 0);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function updateData($table, $data, $where) {
		$data[$table . '_update_date'] = date('Y-m-d H:i:s');
		$data[$table . '_update_ip'] = $this->input->ip_address();
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->error();
	}

	public function deleteData($table, $where) {
		if ($this->delete_record === FALSE) {
			$data[$table . '_delete_date'] = date('Y-m-d H:i:s');
			$data[$table . '_delete_ip'] = $this->input->ip_address();
			$this->db->where($where);
			$this->db->update($table, $data);
		}
		else {
			$this->db->where($where);
			$this->db->delete($table);
		}
		return $this->db->error();
	}

}
