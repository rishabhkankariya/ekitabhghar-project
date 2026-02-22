<?php
session_start();

if (isset($_POST['book_url'])) {
    $_SESSION['download_link'] = $_POST['book_url'];
    echo "success";
} else {
    echo "error";
}
?>
