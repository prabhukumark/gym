<?php
session_start();

if (isset($_POST['submit']) && isset($_FILES['input_file'])) {
    include "db_conn.php";
    echo "<pre>";
    print_r($_FILES['input_file']);
    echo "</pre>";
    $img_name = $_FILES['input_file']['name'];
    $img_size = $_FILES['input_file']['size'];
    $tmp_name = $_FILES['input_file']['tmp_name'];
    $error = $_FILES['input_file']['error'];

    if ($error === 0) {
        if ($img_size > 125000) {
            $em = "unknown error occurreduuuuu!";
            header("Location: up1.php?error=$em");
        } else {
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg", "jpeg", "png");
            if (in_array($img_ex_lc, $allowed_exs)) {
                $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $img_upload_path = 'uploads/' . $new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);
                error_log("Image uploaded successfully. Path: $img_upload_path");
                $sql = "INSERT INTO images(image_url) VALUES('$new_img_name')";
                mysqli_query($conn, $sql);

                $_SESSION['new_img_name'] = $new_img_name; // Moved inside the condition
                error_log("Image path stored in session: $new_img_name");
                header("Location: up1.php");
            } else {
                $em = "You can't upload files of this type";
                header("Location: up1.php?error=$em");
            }
        }
    } else {
        $em = "unknown error occurred!";
        header("Location: up1.php");
    }
} else {
    header("Location: up1.php");
}
?>
