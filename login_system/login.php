<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href=
"https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="../navbar/navbar.css">
    <title>Login Page</title>
</head>

<body>
    <nav>
        <div class="nav-logo">VILLA</div>
        
        <ul class="nav-links">
            <li><a href="../index.php">Domov</a></li>
            <li><a href="http://localhost/Kaka/create_property/read.php">Akomodácie</a></li>
            <li><a href="#kontakt">Kontakt</a></li>
            <li><a href="#rezervacia">Rezervácia</a> </li>
            <li><a href="http://localhost/Kaka/login_system/login.php" class="active">Prihlásenie</a></li>
        </ul>
    </nav>
    <form action="validate.php" method="post">
        <div class="login-box">
            <h1>Prihlásenie</h1>

            <div class="textbox">
                <i class="fa fa-user" aria-hidden="true"></i>
                <input type="text" placeholder="Username"
                         name="username" value="">
            </div>

            <div class="textbox">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input type="password" placeholder="Password"
                         name="password" value="">
            </div>

            <input class="button" type="submit"
                     name="login" value="Sign In">
        </div>
    </form>
</body>

</html>