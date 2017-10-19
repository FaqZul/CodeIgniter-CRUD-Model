<?php
/**
* @author 		Muhammad Faqih Zulfikar
* @copyright	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
* @license 		https://opensource.org/licenses/MIT 	MIT License
* @link 		https://www.facebook.com/DorkSQLi
* @version 		1.0.0
*/
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model {

	public function __construct() { parent::__construct(); }

	public function createData($table, $data) {
		$this->db->insert($table, $data);
		return $this->db->error();
	}

	public function readData($select, $from, $where, $joinTable, $groupBy, $order, $orderBy) {
		$this->db->select('SQL_CALC_FOUND_ROWS ' . $select, FALSE);
		$this->db->from($from);
		$this->db->where($where);
		if (count($joinTable > 0)) {
			foreach ($joinTable as $join) { $this->db->join($join['table'], $join['relation'], 'LEFT'); }
		}
		if ($groupBy !== '') {$this->db->group_by($groupBy); }
		$this->db->order_by($order, $orderBy);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function updateData($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->error();
	}

	public function deleteData($table, $where) {
		$this->db->where($where);
		$this->db->delete($table);
		return $this->db->error();
	}

}
