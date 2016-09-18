<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Signin Template for Bootstrap</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/hover-min.css">
    <link href="css/signin.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">

    <script src="js/jquery-3.1.0.min.js"></script>

</head>
<body>
<header class="head">
    <div class="container">
        <a class="logo">
            <img src="img/eco-driving-challenge-small.png"/>
        </a>
        <nav>
            <ul>
                <li>
                    <a class="hvr-sweep-to-top" href="/">HOME</a>
                </li>
                <li>
                    <a class="hvr-sweep-to-top" href="ranking.php">RANKING</a>
                </li>
                <li>
                    <a class="hvr-sweep-to-top" href="results.php">MY RESULTS</a>
                </li>
                <li>
                    <a class="hvr-sweep-to-top" href="/">SIGN IN&nbsp;&nbsp;<i class="fa fa-sign-in" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a class="hvr-sweep-to-top" href="/">SIGN UP&nbsp;&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
                </li>
            </ul>
        </nav>
    </div>
</header>
<div class="video">
    <video width="100%" autoplay poster="" muted loop="loop" >
        <!--<source src="" type="video/webm">-->
        <source src="video/video.mp4" type="video/mp4">
        <!--<source src="" type="video/ogg">-->
    </video>
    <div class="pattern"></div>
</div>


<div class="login container">
    <form class="form-inline">

        <div class="form-group">
            <label for="exampleInputName2">E-mail</label>
            <input type="email" class="form-control" id="exampleInputName2" placeholder="Jane Doe" value="driver@ecodrivingchallenge.com">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail2">Password</label>
            <input type="password" class="form-control" id="exampleInputEmail2" value="driver">
        </div>
        <button type="submit" class="btn btn-default">SIGN IN</button>
    </form>
</div>
</body>