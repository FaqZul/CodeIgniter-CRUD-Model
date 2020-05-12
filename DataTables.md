# CodeIgniter-CRUD-Model
Create one model for all CodeIgniter controllers, or You can extends this class in Your model class.

## DataTables
For implementation, you can see an example at https://datatables.net/examples/server_side/simple.html and I have created a function to generate a DataTables at [dtResponsive.js](https://gist.github.com/FaqZul/9e02d14dcae49c2590d6b020128903f1).<br>
The source code inspired from [IgnitedDatatables](https://github.com/IgnitedDatatables/Ignited-Datatables)

### Features
1. Support for query grouping.
2. Support for table join.
3. Able to define custom columns, and filters.
4. Editable custom variables with callback function support.

### Example
```html
<!DOCTYPE html>
<html>
	<head>
		<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/datatables.min.css" rel="stylesheet" type="text/css" />
		<title>Example DataTables</title>
	</head>
	<body>
		<div class="container">
			<table class="table table-bordered table-hover" id="exampleDataTables">
				<thead>
					<tr>
						<th>Code</th>
						<th>Name</th>
						<th>Price</th>
						<th>Stock</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
		</div>
		<script src="assets/js/jquery.min.js" type="text/javascript"></script>
		<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="assets/js/datatables.min.js" type="text/javascript"></script>
		<script src="assets/js/dtResponsive.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				dtResponsive('#exampleDataTables', {
					ajax: '<?php echo site_url('welcome/indexdt'); ?>',
					columnDefs: [
						{ className: 'text-right', searchable: false, targets: [2, 3] },
						{ className: 'text-center', orderable: false, searchable: false, targets: 4 }],
					processing: true,
					serverSide: true });
			});
		</script>
	</body>
</html>
```
```php
class Welcome extends CI_Controller {

	public function __construct() { parent::__construct(); }

	public function index() { $this->load->view('vWelcome'); }

	public function indexdt() {
		$this->load->helper('url');
		$this->load->model('crud');
		$this->crud->dtColumnUpd('price', '$1', 'number_format(price, 2)');
		$this->crud->dtColumnUpd('stock', '$1', 'number_format(stock, 2)');
		$this->crud->dtColumnAdd('action', '<a href="' . site_url('welcome/detail/') . '$1"><i aria-hidden="true" class="fa fa-search"></i></a>', 'code');
		$dt = $this->crud->readDataTable('code, name, SUM(price) AS price, SUM(stock) AS stock', 'pos', '', '', 'code, name');
		echo json_encode($dt);
	}

}
```