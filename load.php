<?php
if (isset($_POST['insert'])) {
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "spin";
    $conn = mysqli_connect($host, $user, $password, $database);
    if ($conn->connect_error) {
        die("connection failed: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("insert into user (firstname, lastname, user_id, bio, privacy, dob, username, password, email, profile_photo) values
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssssss", $firstname, $lastname, $user_id, $bio, $privacy, $dob, $username, $password, $email, $profile_photo);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    // Generate a user_id automatically
    $stmt2 = $conn->prepare("select count(*) from user where user_id like ?");
    $stmt2->bind_param("s", $current_date);
    $actual_date = date("Ymd");
    $current_date = $actual_date . "%";
    // $query = "select count(*) from user where user_id like '" . $current_date . "%'";
    // $result = mysqli_query($conn, $query);
    // $row = mysqli_fetch_array($result);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $row = $result->fetch_assoc();
    $user_id = $actual_date . str_pad(($row['count(*)'] + 1), 4, "0", STR_PAD_LEFT);
    // echo "generated user_id is ".$user_id."<br>";
    //
    $bio = $_POST['bio'];
    $privacy = $_POST['privacy'];
    $dob = $_POST['dob'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $profile_photo = file_get_contents($_FILES["profile_photo"]["tmp_name"]);
    $stmt->execute();
    echo "Added details succesfully!";
    // to print all the images in the database
    // $query="select * from user";
    // $result=mysqli_query($conn, $query);
    // while ($row= mysqli_fetch_array($result) ) {
    //     echo '<img src="data:image/jpeg;charset=utf8;base64,'.base64_encode($row['profile_photo']).'" />';
    //     echo '<br>';
    // }
    mysqli_stmt_close($stmt2);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
<html>

<head>
    <meta charset="utf-8">
    <title>load data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>
    <h1>Trial form</h1>
    <form action="load.php" method="post" , enctype="multipart/form-data">
        <label for="firstname">Enter your first name</label>
        <input type="text" id="firstname" name="firstname" required>
        <hr>
        <label for="lastname">Enter your last name</label>
        <input type="text" id="lastname" name="lastname" required>
        <hr>
        <!-- <label for="user_id">Enter the user id</label>
            <input type="text" id="user_id" name="user_id">
            <hr> -->
        <label for="bio">Enter your bio</label>
        <textarea name="bio" id="bio" cols="30" rows="5" placeholder="Start typing here..." style="vertical-align: middle;"></textarea>
        <hr>
        <label>select the privacy type</label>
        <br>
        <label for="private">private</label>
        <input type="radio" name="privacy" id="private" value="private" checked>
        <br>
        <label for="open">open</label>
        <input type="radio" name="privacy" id="open" value="open">
        <hr>
        <label for="dob">Select your date of birth</label>
        <input type="date" id="dob" name="dob" required>
        <hr>
        <label for="username">Enter your username</label>
        <input type="text" id="username" name="username" required>
        <hr>
        <label for="password">Enter your password</label>
        <input type="password" id="password" name="password" required>
        <hr>
        <label for="email">Enter your email</label>
        <input type="email" id="email" name="email" required>
        <hr>
        <label for="profile_photo">Select the image file</label>
        <input type="file" name="profile_photo" id="profile_photo">
        <hr>
        <input type="submit" value="Insert details" name="insert" id="insert">
    </form>
</body>
<script>
    $(document).ready(function() {
        $("#insert").click(function() {
            var image_name = $("#profile_photo").val();
            if (image_name == '') {
                alert('Please select an image!');
                return false;
            }
            var extension = $("#profile_photo").val().split('.')[1].toLowerCase();
            if (jQuery.inArray(extension, ["gif", "png", "jpg", "jpeg"]) == -1) {
                alert('File format not supported!');
                return false;
            }
        });
    });
</script>

</html>