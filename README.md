# CodeIgniter-CRUD-Model
Create one model for all CodeIgniter controllers, or You can extends this class in Your model class.

## Getting Started
### Composer
```sh
git@FaqZul:/var/www/CodeIgniter$ composer require faqzul/codeigniter-crud-model
```

## Prerequisites
* PHP version 5.6 or newer is recommended.<br>
It should work on 5.4.8 as well, but we strongly advise you NOT to run such old versions of PHP, because of potential security and performance issues, as well as missing features.
* [CodeIgniter 3.x](https://www.codeigniter.com/download)

## Configuration
* Change the following line in the `application/config/autoload.php` file for use in Your controller class.
```php
$autoload['packages'] = array();
↓
$autoload['packages'] = array(FCPATH . 'vendor/faqzul/codeigniter-crud-model');
```
* Change the following line in the `application/config/config.php` file for extends in Your model class.
```php
$config['composer_autoload'] = FALSE;
↓
$config['composer_autoload'] = FCPATH . 'vendor/autoload.php';
```
### Setting CRUD Preferences
There are 4 different preferences available to suit Your needs. You can set it up manually as described here, or automatically via the preferences stored in Your configuration file, described below:

Preferences are set by passing an array of preference values to the crud initialize method. Here is an example of how You might set some preferences:
```php
$this->load->model('crud');

$config['delete_record'] = TRUE;
$config['log_query'] = FALSE;
$config['track_trans'] = FALSE;

$this->crud->initialize($config);
```
> **:information_source: Note**<br />
> Most of the preferences have default values that will be used if You do not set them.
### Setting CRUD Preferences in a Config File
If You prefer not to set preferences using the above method, You can instead put them into a [config file](https://github.com/FaqZul/CodeIgniter-CRUD-Model/tree/3.2.0/config). Simply create a new file called the [crud.php](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/3.2.0/config/crud.php), add the $config array in that file. Then save the file at [config/crud.php](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/3.2.0/config/crud.php) and it will be used automatically. You will NOT need to use the `$this->crud->initialize()` method if You save Your preferences in a config file.
### CRUD Preferences
Here is a list of all the options that can be set when using the crud class.

| Preferences | Default Value | Options | Description |
|-------------|---------------|---------|-------------|
| dbgroup | default | None | Lets You choose which connection group to make active |
| delete_record | TRUE | TRUE or FALSE<br />(boolean). | TRUE: Your data will be deleted permanently.<br />FALSE: Your data able to be recovered (un-deleted). |
| insert_id_key | NULL | None | If using the PDO driver with PostgreSQL, or using the Interbase driver, this preference which specifies the appropriate sequence to check for the insert id. |
| log_query | FALSE | TRUE or FALSE<br />(boolean). | Save the last query that was run. |
| track_trans | FALSE | TRUE or FALSE<br />(boolean). | To know the data (when and who) created or updated. |

## Usage
### createData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function add() {
		$data = array(
			'user_email' => 'faqzul@gmail.com',
			'user_name' => 'FaqZul'
		);
		$a = $this->crud->createData('users', $data, TRUE);
		if ($a['message'] === '') {
			// Success inserting data.
			$profile = array(
				'user_id' => $a['insert_id'],			// it's the same $this->crud->insert_id().
				'link' => 'https://github.com/FaqZul'
			);
			$this->crud->createData('user_profiles', $profile);	// Without callback.
			$id = $this->crud->insert_id();				// Without callback, You can also get insert_id as well.
			redirect("profile?id=$id");
		}
		else {
			// Fail inserting data.
			echo var_dump($a);
		}
	}

	/* Example for insert batch */
	public function insert_batch() {
		$datas = array();
		for ($i = 0; $i < 100; $i++) {
			$data["user_email"] = "faqzul$i@gmail.com";
			$data["user_name"] = "FaqZul$i";
			array_push($datas, $data);
		}
		$a = $this->crud->createData('users', $datas);
		/* For get insert_id in every data, please use method insert_ids() */
		echo var_dump($this->crud->insert_ids());
	}

}
```
> **:information_source: Note**<br />
> To use $this->crud->insert_id() or $this->crud->insert_ids() if using the PDO driver with PostgreSQL, or using the Interbase driver, requires preference `insert_id_key` which specifies the appropriate sequence to check for the insert id.
```php
$config['insert_id_key'] = 'SequenceName';
$this->crud->initialize($config);
```
### readData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function list($page = 0) {
		$a = $this->crud->readData('*', 'users')->result();
		// This method returns the query result as an array of objects, or an empty array on failure. Typically you’ll use this in a foreach loop.
		// Executes: SELECT * FROM users

		$where = array('username' => 'FaqZul');
		$b = $this->crud->readData('*', 'users', $where)->row();
		// This method returns a single result row. If your query has more than one row, it returns only the first row. The result is returned as an object.
		// Executes: SELECT * FROM users WHERE username = 'FaqZul'

		$join = array('user_profiles' => 'users.id = user_profiles.user_id');
		$where = array('username !=' => 'FaqZul');
		$c = $this->crud->readData('*', 'users', $where, $join, '', 'users.id DESC', array(10, $page * 10))->result_array();
		// This method returns the query result as a pure array, or an empty array when no result is produced. Typically you’ll use this in a foreach loop.
		// Executes: SELECT * FROM users JOIN user_profiles ON users.id = user_profiles.user_id WHERE username != 'FaqZul' ORDER BY users.id DESC LIMIT 10
	}

	public function search($q = '') {
		/* You can use more specific LIKE queries. */
		$like = array(array('like' => array('user_name' => $q)), 'or_like' => array('user_email' => $q));
		/* Or You can use it. */
		$like = array('or_like' => array('user_name' => $q, 'user_email' => $q));
		// Executes: WHERE user_name LIKE '%$q%' OR user_email LIKE '%$q%'
		$a = $this->crud->readData('*', 'users', $like)->result_array();
		echo json_encode($a);
	}

	public function user() {
		/* Only show users who are guests or owners. */
		$where = ['or_where' => [['role' => 'guest'], ['role' => 'owner']]];
		$a = $this->crud->readData('*', 'users', $where)->result_array();
		echo json_encode($a);
	}

	// See: https://codeigniter.com/user_guide/database/query_builder.html#query-grouping
	public function search_group() {
		$where = ['a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd'];
		$this->crud->group_set([0 => 'group_start', 1 => 'or_group_start']);
		$this->crud->group_end([2 => 2]);
		echo $this->crud->readDataQuery('*', 'mytable', $where);
		// Print: SELECT * FROM "mytable" WHERE ( "a" = 'a' OR ( "b" = 'b' AND "c" = 'c' ) ) AND "d" = 'd'

		// Search username administrator or superadmin in role admin
		$where = ['role' => 'admin', 'or_where' => [['username' => 'administrator'], ['username' => 'superadmin']]];
		echo $this->crud->group_set([1 => 'group_start'])->group_end([1 => 1])->readDataQuery('*', 'user', $where);
		// Print: SELECT * FROM "user" WHERE "role" = 'admin' AND ( "username" = 'administrator' OR "username" = 'superadmin' )
	}

}
```
> **:information_source: Note**<br />
> Now, you can use queries JOIN, LIKE, WHERE more specific. [See ChangeLog](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/3.2.0/CHANGELOG.md)<br />
> If preference `delete_record` FALSE, automatically add `WHERE TABLENAME_delete_date IS NULL` in Your query.
### updateData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function edit($id = 0) {
		$data = array('link' => 'https://github.com/FaqZul/CodeIgniter-CRUD-Model');
		echo ($this->crud->updateData('user_profiles', $data, array('user_id' => $id))) ? 'Success updating data.': $this->crud->error_message();
	}

}
```
### deleteData
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function delete($id = 0) {
		if ($this->crud->deleteData('users', array('id' => $id))) {
			// Success deleting data.
			$this->crud->deleteData('user_profiles', array('user_id' => $id));
		}
		else {
			// Fail deleting data.
			echo var_dump($this->crud->error());
		}
	}

}
```
### extends
```php
class Blog_model extends Crud {

	public function __construct() {
		parent::__construct();
		// You can initialize crud in here.
		// $this->initialize($config);
	}

	public function get_entries() {
		$query = $this->readData('*', 'entries');
		return $query->result_array();
	}

	public function insert_entry() {
		$insert = array(
			'title' => $_POST['title'], // please read the below note.
			'content' => $_POST['content'],
			'date' => time()
		);
		$this->createData('entries', $insert);
	}

	public function update_entry() {
		$update = array(
			'title' => $_POST['title'], // please read the below note.
			'content' => $_POST['content'],
			'date' => time()
		);
		$this->updateData('entries', $update, array('id' => $_POST['id']));
	}

}
```
> **:information_source: Note**<br />
> For the sake of simplicity in this example we’re using `$_POST` directly. This is generally bad practice, and a more common approach would be to use the [Input Library](https://www.codeigniter.com/userguide3/libraries/input.html) `$this->input->post('title')`.

## Class Reference
> createData($table, $data [, $callback = FALSE ])
- Parameters:
	- $table (string) - Table name.
	- $data (array) - An associative array of field/value pairs.
- Returns:
	- `$callback = FALSE` TRUE on success, FALSE on failure.
	- `$callback = TRUE`
		- code (int) - SQL error code.
		- insert_id (int) - [The insert ID number when performing database inserts](https://www.codeigniter.com/user_guide/database/helpers.html?highlight=insert_id).
		- insert_ids (array) - Insert ID from insert_batch.
		- message (string) - SQL error message.
- Return Type: mixed.
> readData($select, $from [, $where = NULL [, $joinTable = NULL [, $groupBy = NULL [, $orderBy = NULL [, $limit = NULL ] ] ] ] ])
- Parameters:
	- $select (string) - The SELECT portion of a query.
	- $from (mixed) - Table name(s); array or string.
	- $where (mixed) - The WHERE clause; array or string.
	- $joinTable (array) - An associative array of table/condition pairs.
	- $groupBy (mixed) - Field(s) to group by; array or string.
	- $orderBy (string) - Field to order by. The order requested - ASC, DESC or random.
	- $limit (array) - Adds LIMIT and OFFSET clauses to a query. `array(10, 20)`.
		1. Key `0` (int) - Number of rows to limit the result to.
		2. Key `1` (int) - Number of rows to skip.
- Returns: There are several ways to generate query results:
	- [Result Arrays](https://www.codeigniter.com/userguide3/database/results.html#result-arrays).
	- [Result Rows](https://www.codeigniter.com/userguide3/database/results.html#result-rows).
	- [Custom Result Objects](https://www.codeigniter.com/userguide3/database/results.html#custom-result-objects).
	- [Result Helper Methods](https://www.codeigniter.com/userguide3/database/results.html#result-helper-methods).
- Return Type: [CI_DB_result](https://www.codeigniter.com/user_guide/database/results.html).
> updateData($table, $data, $where [, $callback = FALSE ])
- Parameters:
	- $table (string) - Table name.
	- $data (array) - An associative array of field/value pairs.
	- $where (mixed) - The WHERE clause; array or string.
- Returns:
	- `$callback = FALSE` TRUE on success, FALSE on failure.
	- `$callback = TRUE` error() method.
- Return Type: mixed.
> deleteData($table, $where [, $callback = FALSE ])
- Parameters:
	- $table (string) - Table name.
	- $where (mixed) - The WHERE clause; array or string.
- Returns:
	- `$callback = FALSE` TRUE on success, FALSE on failure.
	- `$callback = TRUE` error() method.
- Return Type: mixed.
> error()
- Returns:
	- code (int) - SQL error code.
	- message (string) - SQL error message.
- Return Type: array.
> error_code()
- Returns: SQL error code.
- Return Type: int.
> error_message()
- Returns: SQL error message.
- Return Type: string.
> insert_id()
- Returns: [The insert ID number when performing database inserts](https://www.codeigniter.com/user_guide/database/helpers.html?highlight=insert_id).
- Return Type: int.
> insert_ids()
- Returns: [The insert ID number when performing database inserts](https://www.codeigniter.com/user_guide/database/helpers.html?highlight=insert_id).
- Return Type: array.

## Contributing
Please read [CONTRIBUTING.md](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/3.2.0/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning
We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/FaqZul/CodeIgniter-CRUD-Model/tags).

## Authors
* **Muhammad Faqih Zulfikar** - *Developer*<br>
See also the list of [contributors](https://github.com/FaqZul/CodeIgniter-CRUD-Model/contributors) who participated in this project.

## License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/FaqZul/CodeIgniter-CRUD-Model/blob/3.2.0/LICENSE) file for details.