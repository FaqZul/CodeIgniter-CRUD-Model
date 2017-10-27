# CodeIgniter-CRUD-Model
Create one model for all CodeIgniter controllers.

## Getting Started
### Composer
```sh
composer require faqzul/codeigniter-crud-model
```
### Manually
* Download the [latest version](https://github.com/FaqZul/CodeIgniter-CRUD-Model/releases).
* Unzip and copy `models/Crud.php` to `application/models` folder.

## Prerequisites
* PHP version 5.6 or newer is recommended.<br>
It should work on 5.4.8 as well, but we strongly advise you NOT to run such old versions of PHP, because of potential security and performance issues, as well as missing features.
* [CodeIgniter 3.x](https://www.codeigniter.com/download)

## Configuration
* Change the following line in the `application/config/autoload.php` file.
```php
$autoload['model'] = array();
↓
$autoload['model'] = array('Crud');
```
* Add the following line in the `application/config/config.php` file.
```php
/*
|--------------------------------------------------------------------------
| Configuration Package FaqZul/CodeIgniter-CRUD-Model
|--------------------------------------------------------------------------
| Delete Record (Soft Delete)
|--------------------------------------------------------------------------
| Data will be deleted permanently if the value is TRUE;
| To save Your data but not to display, set it to FALSE & add the following fields in each table:
| 	[TABLENAME]_delete_date	datetime 	DEFAULT NULL;
| 	[TABLENAME]_delete_ip	varchar(15)	DEFAULT NULL;
*/
$config['delete_record'] = TRUE;
/*
|--------------------------------------------------------------------------
| Log Query
|--------------------------------------------------------------------------
| If the value is TRUE, run the following query in Your database.
| CREATE TABLE `log` (
|   `log_id` int(11) NOT NULL AUTO_INCREMENT,
|   `log_ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
|   `log_query` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
|   `log_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '127.0.0.1',
|   `log_datetime` datetime NOT NULL,
|   PRIMARY KEY (`log_id`) USING BTREE
| ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
*/
$config['log_query'] = FALSE;
/*
|--------------------------------------------------------------------------
| History Transaction
|--------------------------------------------------------------------------
| If the value is TRUE, add the following fields:
| 1. For inserting data:
|	[TABLENAME]_create_date	datetime 	DEFAULT NULL;
|	[TABLENAME]_create_ip	varchar(15)	DEFAULT NULL;
| 2. For updating data:
|	[TABLENAME]_update_date	datetime 	DEFAULT NULL;
|	[TABLENAME]_update_ip	varchar(15)	DEFAULT NULL;
*/
$config['track_trans'] = FALSE;
```
* If You use composer, don't forget to change the following lines in the `application/config/config.php` file.
```php
$config['composer_autoload'] = FALSE;
↓
$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
```

## Usage
### createData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function add() {
		$data = array(
			'first_name' => 'Muhammad',
			'middle_name' => 'Faqih',
			'last_name' => 'Zulfikar'
		);
		$a = $this->Crud->createData('employee', $data, TRUE);
		if (trim($a['message']) === '') {
			// Success inserting data.
			$profile = array(
				'employee_id' => $a['id'],	// it's the same $this->db->insert_id().
				'link' => 'https://github.com/FaqZul'
			);
			$this->Crud->createData('profile', $profile);	// Without callback.
			$id = $this->session->flash_data('insert_id');	// Without callback, You can also get insert_id as well.
			redirect("profile?id=$id");
		}
		else {
			// Fail inserting data.
		}
	}

}
```
### readData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function list($page = 0) {
		$a = $this->Crud->readData('*', 'users')->result();
		// This method returns the query result as an array of objects, or an empty array on failure. Typically you’ll use this in a foreach loop.
		// Produces: SELECT * FROM users

		$where = array('username !=' => 'FaqZul');
		$b = $this->Crud->readData('*', 'users', $where)->row();
		// This method returns a single result row. If your query has more than one row, it returns only the first row. The result is returned as an object.
		// Executes: SELECT * FROM users WHERE username != 'FaqZul'

		$join = array('user_profiles' => 'users.id = user_profiles.user_id');
		$c = $this->Crud->readData('*', 'users', $where, $join, '', 'users.id DESC', array(10, $page * 10))->result_array();
		// This method returns the query result as a pure array, or an empty array when no result is produced. Typically you’ll use this in a foreach loop.
		// Executes: SELECT * FROM users JOIN user_profiles ON users.id = user_profiles.user_id WHERE username != 'FaqZul' ORDER BY users.id DESC LIMIT 10
	}

}
```
### updateData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function edit($id = 0) {
		$data = array(
			'first_name' => 'Muhammad',
			'middle_name' => 'Faqih',
			'last_name' => 'Zulfikar'
		);
		$a = $this->Crud->updateData('employee', $data, array('id' => $id), TRUE);
		if (trim($a['message']) === '') {
			// Success updating data.
		}
		else {
			// Fail updating data.
		}
	}

}
```
### deleteData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function delete($id = 0) {
		$a = $this->Crud->deleteData('employee', array('id' => $id), TRUE);
		if (trim($a['message']) === '') {
			// Success deleting data.
		}
		else {
			// Fail deleting data.
		}
	}

}
```

## Class Reference
> createData($table, $data [, $callback = FALSE ])
- Parameters:
	- $table (string) - Table name.
	- $data (array) - An associative array of field/value pairs.
- Returns:
	- code (int).
	- id (int). - [The insert ID number when performing database inserts](https://www.codeigniter.com/user_guide/database/helpers.html?highlight=insert_id).
	- message (string).
- Return Type: array or boolean.
> readData($select, $from [, $where = NULL [, $joinTable = NULL [, $groupBy = NULL [, $order = NULL [, $orderBy = NULL [, $limit = NULL ] ] ] ] ] ])
- Parameters:
	- $select (string) - The SELECT portion of a query.
	- $from (mixed) - Table name(s); array or string.
	- $where (mixed) - The WHERE clause; array or string.
	- $joinTable (array) - Multidimensional array.
		1. Key `table` - Table name to join.
		2. Key `relation` - The JOIN ON condition.
		3. Key `type` - The JOIN type.
	- $groupBy (mixed) - Field(s) to group by; array or string.
	- $order (string) - Field to order by.
	- $orderBy (string) - The order requested - ASC, DESC or random.
	- $limit (array) - Associative array.
		1. Key `limit` - Number of rows to limit the result to.
		2. Key `offset` - Number of rows to skip.
- Returns: Array containing the fetched rows.
- Return Type: array.
> updateData($table, $data, $where)
- Parameters:
	- $table (string) - Table name.
	- $data (array) - An associative array of field/value pairs.
	- $where (mixed) - The WHERE clause; array or string.
- Returns:
	- code (int).
	- message (string).
- Return Type: array.
> deleteData($table, $where)
- Parameters:
	- $table (string) - Table name.
	- $where (mixed) - The WHERE clause; array or string.
- Returns:
	- code (int).
	- message (string).
- Return Type: array.

## Contributing
Please read [CONTRIBUTING.md](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/2.0.0/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning
We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/FaqZul/CodeIgniter-CRUD-Model/tags).

## Authors
* **Muhammad Faqih Zulfikar** - *Developer*<br>
See also the list of [contributors](https://github.com/FaqZul/CodeIgniter-CRUD-Model/contributors) who participated in this project.

## License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/2.0.0/LICENSE) file for details.