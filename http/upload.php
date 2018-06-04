<?php
require('config.php');

$errors = [];

$target_file = $target_dir . basename($_FILES["image"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image

if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        //echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        array_push($errors, "File is not an image.");
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        array_push($errors, "The image already exists (name is taken)");
        $uploadOk = 0;
    }
    if ($_FILES["image"]["size"] > 500000) {
        array_push($errors, "Sorry, your file is too large.");
        $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "jpeg" ) {
        array_push($errors, "Sorry, only JPG and JPEG files are allowed.");
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $success = "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";

            $conn = new mysqli($localhost, $dbuser, $dbpass, $dbname);
            mysqli_set_charset($conn, "utf8");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                array_push($errors, $conn->connect_error);
            } else {
                $sql = "INSERT INTO images (path, posLat, posLng, title, description, date)
                            VALUES ('".$target_file."',
                                    '".$_POST["pos-lat"]."',
                                    '".$_POST["pos-lng"]."',
                                    '".$_POST["title"]."',
                                    '".$_POST["description"]."',
                                    '".date("Y-m-d")."')";
                                    
                if ($conn->query($sql) === TRUE) {

                } else {
                    array_push($errors, $conn->error);
                }
            } 

        } else {
            array_push($errors, "Sorry, there was an error uploading your image.");
        }
    }

    $data = [
        'success' => $success,
        'errors' => $errors
    ];
    echo json_encode($data);
}
?>
