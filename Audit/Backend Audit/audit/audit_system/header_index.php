<?php require_once("includes/session.php");
?>
<?php
ob_start();
?>
<?php
require_once("includes/connection.php");	
?>
<?php require_once("includes/functions.php");?>
<?php
	//include_once("includes/form_functions.php");?>
<?php include 'config.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
        ===
        This comment should NOT be removed.

        Charisma v2.0.0

        Copyright 2012-2014 Muhammad Usman
        Licensed under the Apache License v2.0
        http://www.apache.org/licenses/LICENSE-2.0

        http://usman.it
        http://twitter.com/halalit_usman
        ===
    -->
    <meta charset="utf-8">
    <title>Audit System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Charisma, a fully featured, responsive, HTML5, Bootstrap admin template.">
    <meta name="author" content="Muhammad Usman">

    <!-- The styles -->
    <link id="bs-css" href="css/bootstrap-cerulean.min.css" rel="stylesheet">

    <link href="css/charisma-app.css" rel="stylesheet">
    <link href='bower_components/fullcalendar/fullcalendar.css' rel='stylesheet'>
    <link href='bower_components/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print'>
    <link href='bower_components/chosen/chosen.min.css' rel='stylesheet'>
    <link href='bower_components/colorbox/example3/colorbox.css' rel='stylesheet'>
    <link href='bower_components/responsive-tables/responsive-tables.css' rel='stylesheet'>
    <link href='bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css' rel='stylesheet'>
    <link href='css/jquery.noty.css' rel='stylesheet'>
    <link href='css/noty_theme_default.css' rel='stylesheet'>
    <link href='css/elfinder.min.css' rel='stylesheet'>
    <link href='css/elfinder.theme.css' rel='stylesheet'>
    <link href='css/jquery.iphone.toggle.css' rel='stylesheet'>
    <link href='css/uploadify.css' rel='stylesheet'>
    <link href='css/animate.min.css' rel='stylesheet'>
	<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.css">
			<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
			<link rel="stylesheet" type="text/css" href="resources/syntax/shCore.css">
			<!--<link rel="stylesheet" type="text/css" href="resources/demo.css">-->
			
            <style type="text/css">
                a, button, input, [contenteditable=true] { 
                    color: inherit;
                    outline: none;
                }
                a:hover, a:active, a:focus,
                button:hover, button:active, button:focus,
                input:hover, input:active, input:focus {
                    color: inherit;
                    text-decoration: none;
                    outline: none !important;
                }
                #result td {
                    padding-left: 20px;
                    padding-right: 20px;
                }
            </style>
			
    <!-- Angular -->
<script src="js/angular.min.js"></script>
<script src="js/audit_activity.js"></script>			

    <!-- jQuery -->
<script type="text/javascript" language="javascript" src="bower_components/jquery/jquery.min.js"></script>		
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- The fav icon -->
    <link rel="shortcut icon" href="img/0b0757f.png">

</head>

<body ng-app="auditActivity">
<?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
    <!-- topbar starts -->
    <div class="navbar navbar-default" role="navigation">

        <div class="navbar-inner">
            <button type="button" class="navbar-toggle pull-left animated flip">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"> 
                <span>Audit System</span></a>

            <!-- user dropdown starts -->
            <div class="btn-group pull-right">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i><span class="hidden-sm hidden-xs"> <?php echo $_SESSION['username']?></span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#" class="btn btn-info btn-setting">Details</a></li>
                    <li class="divider"></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
            <!-- user dropdown ends -->

            <!-- theme selector starts -->
           
            <!-- theme selector ends -->

            

        </div>
    </div>
    <!-- topbar ends -->
    
<?php } ?>
<div class="ch-container">
    <div class="row">
        <?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
<div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">

                    </div>
                   <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header">Main</li>
                        <li><a class="ajax-link" href="index.php"><i class="glyphicon glyphicon-home"></i><span> Audit Activity</span></a>
                        </li>
                        <?php
$oot = $_SESSION['staff_id'];
$add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$oot}'");
if (!$add) {
				$e = oci_error($conn);
				trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			
			$r = oci_execute($add);
$ytu = oci_fetch_array($add);

 if($ytu['ADMIN'] == "1"){ ?><li><a class="ajax-link" href="department.php"><i class="glyphicon glyphicon-list-alt"></i><span> Shops</span></a>
                        </li>
                        
                        <li><a class="ajax-link" href="new_user.php"><i class="glyphicon glyphicon-user"></i><span> User Manager</span></a>
                        </li>
                       <?php }else{?>
                        <?php }?>
                        <li><a href="logout.php"><i class="glyphicon glyphicon-lock"></i><span> Logout</span></a>
                        </li>
                    </ul>
                
                </div>
            </div>
        </div>
        <!--/span-->
        <!-- left menu ends -->

        
            
                              

        <div id="content" class="col-lg-10 col-sm-10">
            <!-- content starts -->
            <?php } ?>
