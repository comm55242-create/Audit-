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
<?php include 'PEAR/config.php' ?>
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
    <!--<link href='bower_components/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print'>
    <link href='bower_components/chosen/chosen.min.css' rel='stylesheet'>
    <link href='bower_components/colorbox/example3/colorbox.css' rel='stylesheet'>
	<link href='bower_components/bootstrap-tour/build/css/bootstrap-tour.min.css' rel='stylesheet'>
    <link href='bower_components/responsive-tables/responsive-tables.css' rel='stylesheet'>
    <link href='css/jquery.noty.css' rel='stylesheet'>-->
    <link href='css/noty_theme_default.css' rel='stylesheet'>
    <link href='css/elfinder.min.css' rel='stylesheet'>
    <link href='css/elfinder.theme.css' rel='stylesheet'>
    <!--<link href='css/jquery.iphone.toggle.css' rel='stylesheet'>-->
    <link href='css/uploadify.css' rel='stylesheet'>
    <link href='css/animate.min.css' rel='stylesheet'>
	<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.css">
			<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
			<link rel="stylesheet" type="text/css" href="resources/syntax/shCore.css">
			<!--<link rel="stylesheet" type="text/css" href="resources/demo.css">-->
			
            <style type="text/css" class="init">

			</style>

    <!-- jQuery -->
<script type="text/javascript" language="javascript" src="bower_components/jquery/jquery.min.js"></script>		
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- The fav icon -->
    <link rel="shortcut icon" href="img/0b0757f.png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        
        #global-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .loader-logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: float-logo 3s ease-in-out infinite;
        }
        .loader-wordmark {
            width: 220px;
            object-fit: contain;
            margin-bottom: 0.75rem;
            filter: drop-shadow(0 0 15px rgba(79, 70, 229, 0.15));
        }
        .loader-text {
            color: #4f46e5;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .loader-glow-bar {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4f46e5, #9333ea, transparent);
            border-radius: 50%;
            animation: pulse-glow-bar 1.5s ease-in-out infinite;
            box-shadow: 0 0 10px 1px rgba(79, 70, 229, 0.5);
        }
        @keyframes float-logo {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulse-glow-bar {
            0% { width: 60%; opacity: 0.5; }
            50% { width: 120%; opacity: 1; }
            100% { width: 60%; opacity: 0.5; }
        }
    </style>
</head>

<body>
    <!-- Global Loading Screen -->
    <div id="global-page-loader">
        <div class="loader-logo-wrapper">
            <img src="img/logo_wordmark.png" class="loader-wordmark" alt="Melcom Logo">
            <div class="loader-text">PLEASE WAIT...</div>
            <div class="loader-glow-bar"></div>
        </div>
    </div>
    
<?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
    <!-- topbar starts -->
    <div class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container-fluid">
        <div class="navbar-inner">
            <button type="button" class="navbar-toggle pull-left animated flip">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"> 
                <span>Audit System</span></a>
<!---------->
<?php $sql = oci_parse($conn, "SELECT COUNT(*) AS STOCK_CNT FROM ZS_VW_AUDIT_PENDING");
	oci_execute($sql);
	oci_fetch($sql);
	$count_num = oci_result($sql,'STOCK_CNT')
?>
<!---------->
            <!-- user dropdown starts -->
            <div class="btn-group pull-right">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span> <?php echo strtoupper($_SESSION['username'])?></span> <span class='label label-danger'><?php echo $count_num ?></span></button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span>
                <span class ="sr-only">Toggle Dropdown</span>
				</button>
                <ul class="dropdown-menu">
                  <li><a href="#" class="btn-setting">Details</a></li>
                    <li class="divider"></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
            <!-- user dropdown ends -->
            <!-- theme selector starts -->
            <!-- theme selector ends -->
        </div></div>
    </div>
    <!-- topbar ends -->
    
<?php } ?>
<div class="ch-container">
    <div class="row">
        <?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
<div class="col-sm-2 col-lg-2">
            <!--<div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">

                    </div>
                   <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="list-group-item"><strong>Main Menu</strong></li>
                        <li><a class="ajax-link" href="index.php"><i class="glyphicon glyphicon-barcode"></i><span> &nbsp;&nbsp;Audit Activity</span></a>
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

 if($ytu['ADMIN'] == "1" || $_SESSION['staff_id'] === 'ADMIN'){ ?><li><a class="ajax-link" href="department.php"><i class="glyphicon glyphicon-cog"></i><span> &nbsp;&nbsp;Shops</span></a>
                        </li>
                        
                        <li><a class="ajax-link" href="new_user.php"><i class="glyphicon glyphicon-briefcase"></i><span> &nbsp;&nbsp;User Manager</span></a>
                        </li>
                       <?php }else{?>
                        <?php }?>
                        <li class="list-group-item list-group-item-success"><a href="logout.php" class="list-group-item list-group-item-success"><i class="glyphicon glyphicon-log-in"></i><span> &nbsp;&nbsp;Logout</span></a>
                        </li>
                    </ul>
                
                </div>-->
           
<!--_____-->
<div class="panel panel-success">
<div class="pannel-heading center"><h4 class="pannel-title"> Main Menu</h4></div>
<div class="list-group">
	<a class="ajax-link list-group-item" href="index.php"> Audit Activity</a>
</div>
<?php
$oot = $_SESSION['staff_id'];
$add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$oot}'");
oci_execute($add);
$ytu = oci_fetch_array($add);
if($ytu['ADMIN'] == "1" || $_SESSION['staff_id'] === 'ADMIN'){ 
?>
<!--
<div class="list-group">
    <a class="ajax-link list-group-item" href="stockinit.php"> Stock Initialization</a>
</div>
-->
<?php } ?>
</div>

 </div>
        <!--/span-->
        <!-- left menu ends -->
        <div id="content" class="col-lg-10 col-sm-10">
             <!-- content starts -->
            <?php } ?>
