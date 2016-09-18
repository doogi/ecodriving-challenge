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
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyChrZ5bHmlxc8rbcBplhIS95NS1r5MFN_o"></script>
    <script src="js/jquery-3.1.0.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/moment.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/bootstrap-datetimepicker.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/underscore-min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/main.js" type="text/javascript" charset="utf-8"></script>

</head>
<body class="normal">
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

<div class=" container summary">


    <div class="row">
        <div class="container">
            <h3>Search route</h3>
        </div>
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker6'>
                    <input type='text' class="form-control" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker7'>
                    <input type='text' class="form-control" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker6').datetimepicker({
                defaultDate: "2016-09-01",
            });
            $('#datetimepicker7').datetimepicker({
                useCurrent: false,
                defaultDate: "2016-09-17"
            });
            $("#datetimepicker6").on("dp.change", function (e) {
//                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function (e) {
//                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>

    <div class="panel-group " id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">

        </div>
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading" role="tab" id="headingTwo">-->
<!--                <h4 class="panel-title">-->
<!--                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">-->
<!--                        Collapsible Group Item #2-->
<!--                    </a>-->
<!--                </h4>-->
<!--            </div>-->
<!--            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">-->
<!--                <div class="panel-body">-->
<!--                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="panel panel-default">-->
<!--            <div class="panel-heading" role="tab" id="headingThree">-->
<!--                <h4 class="panel-title">-->
<!--                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">-->
<!--                        Collapsible Group Item #3-->
<!--                    </a>-->
<!--                </h4>-->
<!--            </div>-->
<!--            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">-->
<!--                <div class="panel-body">-->
<!--                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>

</div>

<script id="single-result" type="text/html">
    <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <span><%= track.date %></span>  <%= track.from %> - <%= track.to %>
            </a>
        </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in " role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
            <div id="map" class="map"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>
            <h3>Summary</h3>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Total time:</td>
                        <td><%= summary.time %></td>
                    </tr>
                    <tr>
                        <td>Distance</td>
                        <td><%= summary.distance %></td>
                    </tr>
                </tbody>
            </table>
            <h3>Points</h3>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Added</td>
                        <td>+<%= points.added %> <span class="btn btn-default">Details</span></td>
                    </tr>
                    <tr>
                        <td>Removed</td>
                        <td><%= points.removed %> <span class="btn btn-default">Details</span></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td><%= points.total %></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</script>

<script type="text/javascript">

</script>

</body>