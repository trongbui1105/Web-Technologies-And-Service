<?php
require "../bootstrap.php";

use CT275\Lab3\Contact;

if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && isset($_POST['id'])
    && ($contact = Contact::find($_POST['id'])) !== null
) {
    $contact->delete();
}
redirect('/list-contacts.php');
