<?php
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    include("connection_database.php");
    $email = $_POST["email"];
    $password = $_POST['password'];

    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    $image = GUID();
    $imageName = "".$image.".jpg";
    $image = "uploads/".$image.".jpg";
    if (move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
        echo "Файл корректен и был успешно загружен.\n";
    } else {
        echo "Возможная атака с помощью файловой загрузки!\n";
    }

    
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $error="Занадто слабкий пароль";
    }else{
        $error="";

        $sql = "SELECT id FROM `tbl_users` AS u WHERE u.email=? LIMIT 1";
        $stmt= $dbh->prepare($sql);
        $stmt->execute([$email]);
        if($row=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error = "Данний юзер вже зареєстрований";
        }
//        while ($row = $stmt->fetch()) {
//            if($row['email']==$email){
//                $error="Данний юзер вже зареєстрований";
//            }
//            //echo $row['email']."<br />\n";
//        }
        if($error=="")
        {
            $sql = "INSERT INTO `tbl_users` (`email`, `password`, `image`) VALUES (?, ?, ?);";
            $stmt= $dbh->prepare($sql);
            $stmt->execute([$email, $password, $imageName]);
            echo "<script>console.log('wwdwdw')</script>";
            header("Location:  index.php");
            exit();
        }
        //echo "<script>alert('POST JS".$email."'); </script>";
    }
}
else{
    $email="";
    $password="";
    $error="";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<?php include("navbar.php");?>
<div class="container">
    <div class="row">
        <h1 class="col-12 text-center">Реєстрація</h1>
    </div>
    <div class="row">
        <form class="col-12 " action="register.php" method="post" enctype="multipart/form-data">
            <label class="offset-3 col-6 " style="color: red"><?php echo $error ?></label>
            <div class="offset-3 col-6 form-group">
                <label for="email">Електронна пошта</label>
                <input required type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>" aria-describedby="emailHelp">
            </div>
            <div class="offset-3 col-6 form-group">
                <label for="password">Пароль</label>
                <input required type="password" value="<?php echo $password ?>" class="form-control" id="password" name="password">
            </div>
            <div class="offset-3 form-group">
                <input type="file" class="form-control-file" name="image" id="image">
            </div>
            <div class="offset-3 form-group form-check">
                <input required type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Я буду ходити в магазин в масці</label>
            </div>
            <button type="submit" class="offset-8 btn btn-primary">Реєстрація</button>
        </form>
    </div>
</div>

<?php include_once "scripts.php"?>
</body>
</html>
