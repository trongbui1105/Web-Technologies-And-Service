<?php
require "../bootstrap.php";

use CT275\Lab4\Book;
use CT275\Lab4\Author;

$id = isset($_REQUEST['id']) ?
    filter_var($_REQUEST['id'], FILTER_VALIDATE_INT) : false;
if (!isset($id) || ($editedBook = Book::find($id)) === null) {
    header('location:/books.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = false;
    if (
        $editedBook->author['first_name'] == $_POST['first_name']
        && $editedBook->author['last_name'] == $_POST['last_name']
        && $editedBook->author['email'] == $_POST['email']
    ) {
        $editedBook->update($_POST);
        $query = $editedBook->save();
    } else {
        if (Book::where('author_id', $editedBook['author_id'])->count() > 1) {
            $checkAuthorExist = Author::where('first_name', '=', $_POST['first_name'])
                ->where('last_name', '=', $_POST['last_name'])
                ->where('email', '=', $_POST['email'])
                ->first();

            //if author exist, update author_id for book
            if (is_null($checkAuthorExist)) {
                // Create new Author for update book
                $newAuthor = new Author($_POST);
                $newAuthor->save();

                $editedBook->update($_POST);
                $editedBook->author_id = $newAuthor['id'];
                $query = $editedBook->save();
            } else {
                $editedBook->update($_POST);
                $editedBook->author_id = $checkAuthorExist['id'];

                $query = $editedBook->save();
            }
        } else {
            $editedBook->update($_POST);
            $editedBook->author->update($_POST);

            $query = $editedBook->save() && $editedBook->author->save();
        }
    }
    if ($query) {
        // Cập nhật dữ liệu thành công
        header('location:/books.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Book</title>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/sticky-footer.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="/books.php">
                    Books
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>
            </div>
    </nav>

    <!-- Main Page Content -->
    <div class="container">
        <section id="inner" class="inner-section section">
            <div class="container">

                <!-- SECTION HEADING -->
                <h2 class="section-heading text-center wow fadeIn" data-wow-duration="1s">Books</h2>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <p class="wow fadeIn" data-wow-duration="2s">Update information of book here.</p>
                    </div>
                </div>

                <div class="inner-wrapper row">
                    <div class="col-md-12">

                        <form name="frm" id="frm" action="" method="post" class="col-md-6 col-md-offset-3">

                            <input type="hidden" name="id" value="<?= htmlspecialchars($editedBook['id']) ?>">

                            <!-- Title -->
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" maxlen="255" id="title" placeholder="Enter Title" value="<?= htmlspecialchars($editedBook['title']) ?>" />
                            </div>

                            <!-- Num Of Pages -->
                            <div class="form-group">
                                <label for="pages_count">Number Of Pages</label>
                                <input type="text" name="pages_count" class="form-control" maxlen="255" id="pages_count" placeholder="Enter Number of Pages" value="<?= htmlspecialchars($editedBook['pages_count']) ?>" />
                            </div>

                            <!-- Price -->
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" class="form-control" maxlen="255" id="price" placeholder="Enter Price" value="<?= htmlspecialchars($editedBook['price']) ?>" />
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Enter Description"><?= htmlspecialchars($editedBook['description']) ?></textarea>
                            </div>

                            <!-- First name of Author -->
                            <div class="form-group">
                                <label for="first_name">Author's First Name:</label>
                                <input name="first_name" id="first_name" class="form-control" placeholder="Enter Author's First Name" value="<?= htmlspecialchars($editedBook->author['first_name']) ?>" />
                            </div>

                            <!-- Last name of Author -->
                            <div class="form-group">
                                <label for="last_name">Author's Last Name:</label>
                                <input name="last_name" id="last_name" class="form-control" placeholder="Enter Author's Last Name" value="<?= htmlspecialchars($editedBook->author['last_name']) ?>" />
                            </div>

                            <!-- Email of Author -->
                            <div class="form-group">
                                <label for="email">Author's Email:</label>
                                <input name="email" id="email" class="form-control" placeholder="Enter Author's Email" value="<?= htmlspecialchars($editedBook->author['email']) ?>" />
                            </div>

                            <!-- Submit -->
                            <button type="submit" name="submit" id="submit" class="btn btn-primary">Update Contact</button>
                        </form>

                    </div>
                </div>

            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="text-muted">Copyright &copy; 2016 Web Development Course</p>
        </div>
    </footer>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="/js/wow.min.js"></script>
    <script>
        $(document).ready(function() {
            new WOW().init();
        });
    </script>
</body>

</html>