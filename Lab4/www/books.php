<?php
require "../bootstrap.php";
?>
<?php

use CT275\Lab4\Book;
use CT275\Lab4\Author;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$author = new Author($_POST);
	$checkAuthorExist = Author::where('first_name', '=', $_POST['first_name'])
		->where('last_name', '=', $_POST['last_name'])
		->where('email', '=', $_POST['email'])
		->first();

	$book = new Book($_POST);
	if (is_null($checkAuthorExist)) {
		$author->save();
		$author->books()->save($book);
	} else {
		$checkAuthorExist->books()->save($book);
	}
}

$books = Book::all();
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="/css/sticky-footer.css" rel="stylesheet">
<link href="/css/font-awesome.min.css" rel="stylesheet">
<link href="/css/animate.css" rel="stylesheet">
<style>
	table,
	th,
	td {
		border: 1px solid black;
	}

	label {
		display: inline-block;
		width: 150px;
		margin: 0px;
	}

	.form-control {
		width: 15%;
		display: inline-block;
	}

	#search {
		width: 100%;
	}

	#phrase {
		width: 50%;
	}
</style>
<div class="container">
	<title>Book</title>
	<h2>Add new book</h2>
	<form method="POST">
		<label>Title:</label> <input class="form-control" type="text" name="title" /> <br><br>
		<label>Num of Pages:</label> <input class="form-control" type="number" name="pages_count" /> <br><br>
		<label>Price:</label> <input class="form-control" type="number" name="price" /> <br><br>
		<label>Description:</label> <input class="form-control" type="text" name="description" /> <br><br>
		<label>Author's First Name:</label> <input class="form-control" type="text" name="first_name" /> <br><br>
		<label>Author's Last Name:</label> <input class="form-control" type="text" name="last_name" /> <br><br>
		<label>Author Email:</label> <input class="form-control" type="text" name="email" /> <br><br>
		<input type="submit" class="btn btn-success" name="submit" value="Save" />
	</form>
	<hr>
	<h2 class="text-center">
		List of books
	</h2>
	<div class="row">
		<div class="col-md-6">
			<h3>
				Number of Books: <span class="text-info"> <?php echo Book::count() ?> </span>
			</h3>
			<h3>
				Average Price: <span class="text-info"> <?php echo round(Book::avg('price'), 2) ?> </span>
			</h3>
		</div>
		<div class="text-right col-md-6">
			<form action="/search-books.php" method="POST">
				<div class="form-group has-feedback has-search col-md-8 pull-right">
					<span class="glyphicon glyphicon-search form-control-feedback" style="margin-right: 10px;"></span>
					<input type="text" name="search" id="search" class="form-control" placeholder="Search">
				</div>
				<br><br><br>
				<div class="form-group has-feedback has-search col-md-8 pull-right">
					<img src="<?php require "captcha.php";
								echo $builder->inline(); ?>" />
					&nbsp;
					&nbsp;
					<span>
						<input type="text" id="phrase" name="phrase" class="form-control" />
					</span>
				</div>
				<br><br>
				<div class="col-md-9 center-block">
					<input type="submit" name='search-book' value="Search" class="btn btn-primary" />
				</div>
			</form>
		</div>
	</div>


	<table style="width:100%" id="book" class="table table-bordered table-responsive table-striped">
		<thead>
			<tr>
				<th>Title</th>
				<th>Num of Pages</th>
				<th>Price</th>
				<th>Description</th>
				<th>Author</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($books as $book) {
				echo "<tr>";
				echo "<td>" . $book->title . "</td>";
				echo "<td>" . $book->pages_count . "</td>";
				echo "<td>" . $book->price . "</td>";
				echo "<td>" . $book->description . "</td>";
				echo "<td>" . $book->author->first_name . " " . $book->author->last_name
					. " (" . $book->author->email . ")" . "</td>";
				$form_action = '
				<a href="/edit-book.php?id=' . $book->id . '" class="btn btn-xs btn-warning">
					<i alt="Edit" class="fa fa-pencil"> Edit</i>
				</a>
				<form class="delete" action="/del-book.php" method="POST" style="display: inline;">
					<input type="hidden" name="id" value="' . $book->id . '">
					<button type="submit" class="btn btn-xs btn-danger" name="delete-book">
						<i alt="Delete" class="fa fa-trash"> Delete</i>
					</button>
				</form>
			';
				echo "<td>" . $form_action . "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>

	<div id="delete-confirm" class="modal fade" role="dialog">"
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Confirmation</h4>
				</div>
				<div class="modal-body">Do you want to delete this contact?</div>
				<div class="modal-footer">
					<button type="button" data-dismiss="modal" class="btn btn-danger" id="delete">Delete</button>
					<button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="/js/wow.min.js"></script>
<script>
	$(document).ready(function() {
		new WOW().init();
		$('#book').DataTable({
			searching: false,
			lengthMenu: [5, 10, 20, "All"]
		});
	});
</script>

<script>
	$(document).ready(function() {
		new WOW().init();
		$('#book').DataTable();
		$('button[name="delete-book"]').on('click', function(e) {
			var $form = $(this).closest('form');
			e.preventDefault();
			$('#delete-confirm').modal({
					backdrop: 'static',
					keyboard: false
				})
				.one('click', '#delete', function() {
					$form.trigger('submit');
				});
		});
	});
</script>