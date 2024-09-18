<?php
include "session.php";
include 'include/common-header.inc';
include 'lib/database_pdo.php';

global $db;
$where = array("id" => $_SESSION['id']);
$user = $db->select_all("users", "*", $where, $limit = 1);

?>

<div class="text"> <!-- text start -->
   <h1>Statistics</h1>

</div> <!-- text end -->

<?php
include 'include/common-footer.inc';
?>
