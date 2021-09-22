<?php

require "../bootstrap.php";

use Gregwar\Captcha\PhraseBuilder;
use CT275\Lab4\Book;

error_reporting(E_ERROR | E_PARSE);

$countBook = 0;
$avgPrice = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['phrase']) &&  PhraseBuilder::comparePhrases($_SESSION['phrase'], $_POST['phrase'])) {
        $value = $_POST['search'];

        $books = Book::whereHas('author', function ($query) use ($value) {
            $query->where('first_name', '=', $value)
                ->orWhere('last_name', '=', $value)
                ->orWhereRaw(
                    "CONCAT(first_name, ' ', last_name) LIKE '%" . $value . "%'"
                );
        })
            ->orWhere('title', 'LIKE', '%' . $value . '%')
            ->get();

        $countBook = $books->count();
        $avgPrice = round($books->avg('price'), 2);
    } else {
        header('location:/books.php');
    }
}


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
        width: 33%;
        margin-right: 15px;
    }

    #refresh {
        margin-top: 20px;
    }
</style>
<div class="container">
    <title>Search Books</title>
    <div class="row">
        <div class="col-md-5">
            <input type="submit" onclick="location.href='/books.php'" id="refresh" name='refresh' value="Refresh Books List" class="btn btn-success" />
        </div>
        <h2 class="col-md-7">
            List of books
        </h2>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h3>
                Number of Books: <span class="text-info"> <?php echo $countBook; ?> </span>
            </h3>
            <h3>
                Average Price: <span class="text-info"> <?php echo $avgPrice; ?> </span>
            </h3>
        </div>
        <div class="text-right col-md-6">
            <form action="/search-books.php" method="POST">
                <div class="form-group has-feedback has-search col-md-8 pull-right">
                    <span class="glyphicon glyphicon-search form-control-feedback" style="margin-right: 10px;"></span>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search">
                </div>
                <br><br><br>
                <img src="<?php
                            require "captcha.php";
                            echo $builder->inline(); ?>" />
                <span>
                    <input type="text" id="phrase" name="phrase" class="form-control" />
                </span>
                <br><br>
                <div class="col-md-9 center-block">
                    <input type="submit" id="searchBtn" name='search-book' value="Search" class="btn btn-primary" />
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