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
| Delete Record
|--------------------------------------------------------------------------
| Data will be deleted permanently if the value is TRUE;
| To save Your data but not to display, set it to FALSE & add the following fields in each table:
| 	$TableName_delete_date	datetime 	DEFAULT NULL;
| 	$TableName_delete_ip	varchar(15)	DEFAULT NULL;
*/
$config['delete_record'] = FALSE;
```
* If You use composer, also change the following line in the `application/config/config.php` file.
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
		$a = $this->Crud->createData('employee', $data);
		if (trim($a['message']) === '') {
			// Success inserting data.
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
		$where = array('id !=' => NULL);
		$join = array(
			array('table' => 'user_profiles', 'relation' => 'user_profiles.user_id = users.id', 'type' => 'LEFT')
		);
		$data = $this->crud->readData('*', 'users', $where, $join, '', 'id', 'DESC', array('limit' => 10, 'offset' => $page * 10));
		var_dump($data);
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
		$a = $this->Crud->updateData('employee', $data, array('id' => $id));
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
		$a = $this->Crud->deleteData('employee', array('id' => $id));
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
> createData($table, $data)
- Parameters:
	- $table (string) - Table name.
	- $data (array) - An associative array of field/value pairs.
- Returns:
	- code (int).
	- message (string).
- Return Type: array.
> readData($select, $from, $where, $joinTable, $groupBy, $order, $orderBy [, $limit = NULL])
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
Please read [CONTRIBUTING.md](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/1.0.0/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning
We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/FaqZul/CodeIgniter-CRUD-Model/tags).

## Authors
* **Muhammad Faqih Zulfikar** - *Developer*<br>
See also the list of [contributors](https://github.com/FaqZul/CodeIgniter-CRUD-Model/contributors) who participated in this project.

## License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/1.0.0/LICENSE) file for details.