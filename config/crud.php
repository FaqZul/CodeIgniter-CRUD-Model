<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Configuration Package FaqZul/CodeIgniter-CRUD-Model
|--------------------------------------------------------------------------
*/

/**
 * @author		Muhammad Faqih Zulfikar
 * @copyright	Copyright (c) 2017 FaqZul (https://github.com/FaqZul/CodeIgniter-CRUD-Model)
 * @license		https://opensource.org/licenses/MIT 	MIT License
 * @link		https://www.facebook.com/DorkSQLi
 * @package		FaqZul/CodeIgniter-CRUD-Model
 * @subpackage	Config
 * @version		3.2.0
 */

/*
|--------------------------------------------------------------------------
| Delete Record (Soft Delete)
|--------------------------------------------------------------------------
| Data will be deleted permanently if the value is TRUE;
| To save Your data but not to display, set it to FALSE & add the following fields in each table:
| 	$TableName_delete_date	datetime 	DEFAULT NULL;
| 	$TableName_delete_ip	varchar(15)	DEFAULT NULL;
*/
$config['delete_record'] = TRUE;

/*
|--------------------------------------------------------------------------
| Database Connectivity Settings
|--------------------------------------------------------------------------
| The $dbgroup variable lets you choose which connection group to make active.
| By default there is only one group (the 'default' group).
*/
$config['dbgroup'] = 'default';

/*
|--------------------------------------------------------------------------
| Sequence Name
|--------------------------------------------------------------------------
| The insert ID number when performing database inserts.
| If using the PDO driver with PostgreSQL, or using the Interbase driver, $this->crud->insert_id() function requires a $insert_id_key parameter, which specifies the appropriate sequence to check for the insert id.
*/
$config['insert_id_key'] = NULL;

/*
|--------------------------------------------------------------------------
| Log Query
|--------------------------------------------------------------------------
| If the value is TRUE, run the following query in Your database.
| CREATE TABLE `log` (
| 	`log_id` int(11) NOT NULL AUTO_INCREMENT,
| 	`log_ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
| 	`log_query` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
| 	`log_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
| 	`log_datetime` datetime NOT NULL,
| 	PRIMARY KEY (`log_id`) USING BTREE
| ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
*/
$config['log_query'] = FALSE;

/*
|--------------------------------------------------------------------------
| History Transaction
|--------------------------------------------------------------------------
| If the value is TRUE, add the following fields:
| 1. For inserting data:
|	$TABLE_create_date	datetime 	DEFAULT NULL;
|	$TABLE_create_ip	varchar(15)	DEFAULT NULL;
| 2. For updating data:
|	$TABLE_update_date	datetime 	DEFAULT NULL;
|	$TABLE_update_ip	varchar(15)	DEFAULT NULL;
*/
$config['track_trans'] = FALSE;
