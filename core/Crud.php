<?php
/**
 * @author		Muhammad Faqih Zulfikar
 * @copyright	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
 * @license		https://opensource.org/licenses/MIT 	MIT License
 * @link		https://www.facebook.com/DorkSQLi
 * @package		FaqZul/CodeIgniter-CRUD-Model
 * @subpackage	Core
 * @version		3.1.0
 */
namespace FaqZul\CodeIgniter\CRUD\Model;
defined('BASEPATH') or exit('No direct script access allowed');
require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'helpers', 'crud_helper.php'));

class Crud extends \CI_Model {

	/**
	 * Last error
	 *
	 * @var array
	 */
	protected $_error = array('code' => 0, 'message' => '');

	/**
	 * Database Connectivity Settings
	 * The $dbgroup variable lets you choose which connection group to make active.
	 * By default there is only one group (the 'default' group).
	 *
	 * @var string
	 */
	protected $dbgroup = 'default';

	/**
	 * Insert ID
	 *
	 * @var int
	 */
	protected $insert_id_val = 0;

	/**
	 * Insert IDs
	 *
	 * @var array
	 */
	protected $insert_ids_val = array(0);

	/**
	 * Delete Record (Soft Delete)
	 * Data will be deleted permanently if the value is TRUE.
	 * To save Your data but not to display, set it to FALSE & add the following fields in each table:
	 * 	[TABLENAME]_delete_date	datetime 	DEFAULT NULL;
	 * 	[TABLENAME]_delete_ip	varchar(15)	DEFAULT NULL;
	 *
	 * @var bool
	 */
	protected $delete_record = TRUE;

	/**
	 * The insert ID number when performing database inserts.
	 * If using the PDO driver with PostgreSQL, or using the Interbase driver, $this->crud->insert_id() function requires a $insert_id_key parameter, which specifies the appropriate sequence to check for the insert id.
	 *
	 * @var string
	 */
	protected $insert_id_key = NULL;

	/**
	 * Save the last query that was run.
	 * If the value is TRUE, run the following query in Your database:
	 * CREATE TABLE `log` (
	 * 	`log_id` int(11) NOT NULL AUTO_INCREMENT,
	 * 	`log_ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
	 * 	`log_query` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	 * 	`log_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
	 * 	`log_datetime` datetime NOT NULL,
	 * 	PRIMARY KEY (`log_id`) USING BTREE
	 * ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
	 *
	 * @var bool
	 */
	protected $log_query = FALSE;

	/**
	 * History Transaction
	 * If the value is TRUE, add the following fields in each table:
	 * 1. For inserting data:
	 * 	[TABLENAME]_create_date	datetime 	DEFAULT NULL;
	 * 	[TABLENAME]_create_ip	varchar(15)	DEFAULT NULL;
	 * 2. For updating data:
	 * 	[TABLENAME]_update_date	datetime 	DEFAULT NULL;
	 * 	[TABLENAME]_update_ip	varchar(15)	DEFAULT NULL;
	 *
	 * @var bool
	 */
	protected $track_trans = FALSE;

	protected $joins = array(
		'left', 'right', 'outer', 'inner', 'left_outer', 'right_outer',
		'_esc', 'left_esc', 'right_esc', 'outer_esc', 'inner_esc', 'left_outer_esc', 'right_outer_esc'
	);

	protected $wheres = array(
		'where', 'or_where', 'or_where_in', 'or_where_not_in', 'where_in', 'where_not_in', 'like', 'or_like', 'not_like', 'or_not_like',
		'where_esc', 'or_where_esc', 'or_where_in_esc', 'or_where_not_in_esc', 'where_in_esc', 'where_not_in_esc', 'like_esc', 'or_like_esc', 'not_like_esc', 'or_not_like_esc'
	);

	/**
	 * Class Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		if (file_exists(APPPATH . 'config/crud.php') AND $this->config->load('crud', TRUE)) { $this->initialize($this->config->item('crud')); }
		$this->load->database($this->dbgroup);
	}

	/**
	 * Initialize Preferences
	 *
	 * @param	array	$config
	 * @return	Crud
	 */
	public function initialize(array $config = array()) {
		foreach ($config as $key => $val) {
			if (isset($this->$key)) {
				$this->$key = $val;
				if ($key === 'dbgroup') {
					$this->db->close();
					$this->load->database($this->dbgroup);
				}
			}
		}
		return $this;
	}

	/**
	 * Create Data
	 *
	 * @param	string	$table
	 * @param	array	$data
	 * @param	bool	$callback
	 * @return	mixed
	 */
	public function createData($table, $data, $callback = FALSE) {
		if (is_array_assoc($data)) {
			if ($this->track_trans === TRUE) {
				$data[$table . '_create_date'] = date('Y-m-d H:i:s');
				$data[$table . '_create_ip'] = $this->input->ip_address();
			}
			$this->db->insert($table, $data);
			$this->set_error($this->db->error());
			$this->set_insert_id($this->db->insert_id($this->insert_id_key));
		}
		else if (is_array_multi($data)) {
			if ($this->track_trans === TRUE) {
				for ($a = 0; $a < count($data); $a++) {
					$data[$a][$table . '_create_date'] = date('Y-m-d H:i:s');
					$data[$a][$table . '_create_ip'] = $this->input->ip_address();
				}
			}
			$this->db->insert_batch($table, $data);
			$this->set_error($this->db->error());
			$start = $this->db->insert_id($this->insert_id_key);
			$end = $start + $this->db->affected_rows();
			$this->set_insert_id($start);
			$this->set_insert_ids($start, $end);
		}
		if ($this->log_query === TRUE) { $this->log($this->db->last_query()); }
		if ($callback) {
			$error = $this->error();
			$error['insert_id'] = $this->insert_id();
			$error['insert_ids'] = $this->insert_ids();
			return $error;
		}
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	/**
	 * Read Data
	 *
	 * @param	string	$select
	 * @param	mixed	$from
	 * @param	mixed	$wheres
	 * @param	array	$joinTable
	 * @param	mixed	$groupBy
	 * @param	string	$orderBy
	 * @param	array	$limit
	 * @return	mixed
	 */
	public function readData($select, $from, $wheres = NULL, $joinTable = NULL, $groupBy = NULL, $orderBy = NULL, $limit = NULL) {
		$this->db->select($select, FALSE);
		$this->db->from($from);
		if (is_array_assoc($joinTable)) { $this->set_joins($joinTable); }
		if (is_array_assoc($wheres)) { $this->set_wheres($wheres); }
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
		$this->set_insert_ids(0, 0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		return ($this->error_message() !== '') ? FALSE: $query;
	}

	/**
	 * Update Data
	 *
	 * @param	string	$table
	 * @param	array	$data
	 * @param	mixed	$wheres
	 * @param	bool	$callback
	 * @return	mixed
	 */
	public function updateData($table, $data, $wheres, $callback = FALSE) {
		if (is_array_assoc($data) AND $this->track_trans === TRUE) {
			$data[$table . '_update_date'] = date('Y-m-d H:i:s');
			$data[$table . '_update_ip'] = $this->input->ip_address();
		}
		if (is_array_assoc($wheres)) { $this->set_wheres($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		$this->db->update($table, $data);
		$this->set_error($this->db->error());
		$this->set_insert_id(0);
		$this->set_insert_ids(0, 0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $this->error(); }
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	/**
	 * Delete Data
	 *
	 * @param	string	$table
	 * @param	mixed	$wheres
	 * @param	bool	$callback
	 * @return	mixed
	 */
	public function deleteData($table, $wheres, $callback = FALSE) {
		if ($this->delete_record === FALSE) {
			$data[$table . '_delete_date'] = date('Y-m-d H:i:s');
			$data[$table . '_delete_ip'] = $this->input->ip_address();
		}
		if (is_array_assoc($wheres)) { $this->set_wheres($wheres); }
		else if (is_string($wheres) AND trim($wheres) !== '') { $this->db->where($wheres); }
		($this->delete_record === FALSE) ? $this->db->update($table, $data): $this->db->delete($table);
		$this->set_error($this->db->error());
		$this->set_insert_id(0);
		$this->set_insert_ids(0, 0);
		if ($this->log_query) { $this->log($this->db->last_query()); }
		if ($callback) { return $this->error(); }
		else { return ($this->error_message() !== '') ? FALSE: TRUE; }
	}

	/**
	 * Error
	 *
	 * Returns an array containing code and message of the last
	 * database error that has occurred.
	 *
	 * @return array
	 */
	public function error() { return $this->_error; }

	/**
	 * The error message number
	 *
	 * @return int
	 */
	public function error_code() { return $this->_error['code']; }

	/**
	 * The error message string
	 *
	 * @return string
	 */
	public function error_message() { return $this->_error['message']; }

	/**
	 * Insert ID
	 *
	 * @return int
	 */
	public function insert_id() { return $this->insert_id_val; }

	/**
	 * Insert IDs
	 *
	 * @return array
	 */
	public function insert_ids() { return $this->insert_ids_val; }

	/**
	 * Log Queries
	 *
	 * @param	string	$var
	 * @return	Crud
	 */
	protected function log($var = '') { if (trim($var) !== '') { $this->db->insert('log', array('log_ip' => $this->input->ip_address(), 'log_query' => $var, 'log_url' => ( ! isset($_SERVER['REDIRECT_URL'])) ? base_url(): $_SERVER['REDIRECT_URL'], 'log_datetime' => date('Y-m-d H:i:s'))); } }

	/**
	 * Set Error
	 *
	 * @param	array	$var
	 * @return	Crud
	 */
	protected function set_error($var = array()) {
		if (is_array_assoc($var) AND isset($var['code']) AND isset($var['message'])) {
			$this->_error['code'] = (int) $var['code'];
			$this->_error['message'] = trim($var['message']);
		}
		return $this;
	}

	/**
	 * Set Insert ID
	 *
	 * @param	int 	$var
	 * @return	Crud
	 */
	protected function set_insert_id($var = 0) {
		$this->insert_id_val = (int) $var;
		return $this; 
	}

	/**
	 * Set Insert IDs
	 *
	 * @param	int 	$start
	 * @param	int 	$end
	 * @return	Crud
	 */
	protected function set_insert_ids($start = 0, $end = 0) {
		$a = array(0);
		for ($b = $start; $b < $end; $b++) { array_push($a, (int) $b); }
		if (count($a) > 1) { unset($a[0]); }
		$this->insert_ids_val = $a;
		return $this;
	}

	/**
	 * Adds a JOIN clause to a query
	 *
	 * @param	array 	$arr
	 * @return	void
	 */
	protected function set_joins($arr = array()) {
		if (in_array_assoc($this->joins, $arr)) {
			foreach ($arr as $keys => $vals) {
				if (in_array($keys, $this->joins)) {
					if (strpos($keys, 'esc') !== FALSE) {
						$key = str_replace('esc', '', $keys);
						if (is_array_assoc($vals)) {
							foreach ($vals as $k => $v) { $this->db->join($k, $v, $key, FALSE); }
						}
						else if (is_array_multi($vals)) {
							foreach ($vals as $val) {
								if (is_array_assoc($val)) {
									foreach ($val as $k => $v) { $this->db->join($k, $v, $key, FALSE); }
								}
							}
						}
					}
					else {
						if (is_array_assoc($vals)) {
							foreach ($vals as $k => $v) { $this->db->join($k, $v, $keys); }
						}
						else if (is_array_multi($vals)) {
							foreach ($vals as $val) {
								if (is_array_assoc($val)) {
									foreach ($val as $k => $v) { $this->db->join($k, $v, $keys); }
								}
							}
						}
					}
				}
				else { $this->db->join($keys, $vals); }
			}
		}
		else {
			foreach ($arr as $k => $v) { $this->db->join($k, $v); }
		}
	}

	/**
	 * Generates the WHERE portion of the query
	 *
	 * @param	array 	$arr
	 * @return	void
	 */
	protected function set_wheres($arr = array()) {
		if (in_array_assoc($this->wheres, $arr)) {
			foreach ($arr as $keys => $vals) {
				if (in_array($keys, $this->wheres)) {
					if (strpos($keys, '_esc') !== FALSE) {
						$key = str_replace('_esc', '', $keys);
						if (is_array_assoc($vals)) {
							foreach ($vals as $k => $v) { $this->db->$key($k, $v, FALSE); }
						}
						else if (is_array_multi($vals)) {
							foreach ($vals as $val) {
								if (is_array($val)) {
									foreach ($val as $k => $v) { $this->db->$key($k, $v, FALSE); }
								}
								else if (is_string($val)) { $this->db->$key($val, NULL, FALSE); }
							}
						}
						else if (is_string($vals)) { $this->db->$key($vals, NULL, FALSE); }
					}
					else {
						if (is_array_assoc($vals)) {
							foreach ($vals as $k => $v) { $this->db->$keys($k, $v); }
						}
						else if (is_array_multi($vals)) {
							foreach ($vals as $val) { $this->db->$keys($val); }
						}
						else if (is_string($vals)) { $this->db->$keys($vals); }
					}
				}
				else { $this->db->where($keys, $vals); }
			}
		}
		else { $this->db->where($arr); }
	}

}
