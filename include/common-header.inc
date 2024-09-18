<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8">
   <title>B4S :: Big4Social</title>
   <meta name="description" content="Software useful to help in manage employees access in a company."/>
   <link rel="shortcut icon" href="include/images/favicon.ico" type="image/x-icon"/>
   <link href="include/default.css" rel="stylesheet" type="text/css"/>
   <link href="include/tablesorter.css" rel="stylesheet" type="text/css"/>
   <script type="text/javascript" src="include/js/jquery-1.6.2.min.js"></script>
   <script type="text/javascript" src="include/js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript" id="js">$(document).ready(function() {
   // call the tablesorter plugin
   $("table").tablesorter({
      // sort on the first column and third column, order asc
      sortList: [[0,0]]
   });
});</script>

</head>
<body>
<div id="wrapper">
<!-- start header -->
<div id="header">
   <div id="logo">
      <img src="include/images/switch.png" alt="switch.png" />
      <h1>B4S :: B4Social</h1>
   </div>
   <div id="menu">
     <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="#">Stats</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li><a href="about.php">About</a></li>
        <?php (isset($_SESSION['logged']) && $_SESSION['logged']) ? 
            print "<li><a href='logout.php'>Sair</a></li>" : 
            print "<li><a href='login.php'>Login</a></li>" 
         ?>
     </ul>
   </div>
</div>
<!-- end header -->
<div id="page"> <!-- start page -->
