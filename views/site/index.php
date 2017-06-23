<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Form | Home</title>
    <link rel="stylesheet" href=<?= URL . "/vendor/twbs/bootstrap/dist/css/bootstrap.min.css"   ?>>
    <link rel="stylesheet" href=<?= URL . "/web/css/styles.css" ?>>

</head>
<body>
<header>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href=<?= URL . "/" ?>>Test Form</a>
            </div>
            <div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href=<?= URL . "/logout" ?>>Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<div class="container">
    <div class="row">
        <div class="jumbotron">
            <h1>Hello, <?= $userData['name'] ?>!</h1>
            <p>Your email: <?= $userData['email'] ?></p>
        </div>
    </div>
</div>
</body>
</html>