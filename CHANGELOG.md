# CodeIgniter-CRUD-Model Change Log
All notable changes to this project will be documented in this file.<br>
The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.0] - 2017-12-06
### Added
- Support insert_batch in createData() method.
- Support a insert_ids() method to getting collection insert id from insert_batch.
- Support queries `JOIN` more specific. Options are: `left, right, outer, inner, left_outer, right_outer`. To escape the query add `_esc`.
	- Default JOIN (escape FALSE) `array('table' => 'condition')`
	- Default JOIN (escape TRUE) `array('_esc' => array('table' => 'condition'))`
	- LEFT JOIN (escape FALSE) `array('left' => array('table' => 'condition'))`
	- LEFT JOIN (escape TRUE) `array('left_esc' => array('table' => 'condition'))`
- Support queries `WHERE` more specific. Options are: `or_where, or_where_in, or_where_not_in, where_in, where_not_in, like, or_like, not_like, or_not_like`. To escape the query add `_esc`.
	- Default WHERE (escape FALSE) `array('field' => 'value')`
	- Default WHERE (escape TRUE) `array('where_esc' => array('field' => 'value'))`
	- OR WHERE (escape FALSE) `array('or_where' => array('field' => 'value'))`
	- OR WHERE (escape TRUE) `array('or_where_esc' => array('field' => 'value'))`

## [3.0.0] - 2017-11-08
### Added
- Support extends to the model class.
- Support a error() method.
- Support a insert_id() method.
- Save the last query that was run.
	> **:information_source: Note**<br />
	> Disabling the **save_queries** setting in your database configuration will render this function useless.
- Parameter `$callback` in createData(), updateData(), deleteData() method.
### Changed
- History transaction can be optional with method initialize().
- All configurations move to method initialize().
- Return method of createData(), updateData(), deleteData(). If the `$callback` parameter is TRUE return `$this->crud->error()` method (array), FALSE return boolean (success or fail).
- Parameter `$joinTable` in readData() method becomes associative array of table/condition pairs.
- Parameter `$groupBy` in readData() method with type array or string.
- Parameter `$orderBy` in readData() method merged with parameter `$order`.
- Parameter `$limit` in readData() method becomes `array(10, 20)`. Key 0 for limit, Key 1 for offset.
- Return method of readData() to [CI_DB_result](https://www.codeigniter.com/user_guide/database/results.html).
### Removed
- Parameter `$joinTable['type']` in readData() method.
- Parameter `$order` in readData() method.

## [2.0.0] - 2017-10-25
### Added
- Soft delete.
- History Create, Update, Delete data.
- Query `LIMIT`.
- Query `JOIN` with some type. Options are: `left, right, outer, inner, left outer, and right outer`.

## [1.0.0] - 2017-10-20
* First release.