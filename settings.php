<?php
include "session.php";
include "include/common-header.inc";
include "lib/database_pdo.php";

global $db;
$where = array("id" => $_SESSION['id']);
$user = $db->select_all("users", "*", $where, $limit = 1);

$username_err = $email_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
  if(empty(trim($_POST["username"]))){
    $username_err = "<p id='warning'>Digite um nome de usuário.</p>\n";
  }

  if(empty(trim($_POST['email']))){
    $email_err = "<p id='warning'>Digite um email.</p>\n";
  } 
  
  if(empty($username_err) && empty($email_err)){
    $params = array("username" => $_POST["username"], "email" => $_POST["email"]);
    $where = array("id" => $user["id"]);
    $return = $db->update("users", $params, $where);
    if($return){
      header("location: index.php");
      #echo $return;
    }
    else{
      print "<p id='warning'> Algo deu errado. Tente novamente mais tarde.\n";
    }
  }
}

?>

<div class="text">
  <h1>User settings</h1>
</div> 

<?php 
$username = $user["username"];
$email = $user["email"];

//dica
if(!$user["admin"]){
  print "<!-- admin = 0 -->";
}else {
  print "<!-- admin = 1 -->";
}

?>
<div id="page">
  <h3>Alterar dados</h3> 
  <form id="update_form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <div class="form-group" <?php echo (empty($username_err)) ? 'id=\'warning\'' : ''; ?> >
      <label>Username</label><br>
      <input type="text" name="username" value="<?php echo $username; ?>"><br><br>
      <span class="help-block"><?php echo $username_err; ?></span>
    </div>
    <div class="form-group" <?php echo (empty($email_err)) ? 'id=\'warning\'' : ''; ?> >
      <label>Email</label><br>
      <input type="email" name="email" value="<?php echo $email; ?>"><br>
      <span class="help-block"><?php echo $email_err; ?></span>
    </div>
    <div class="form-group">
      <br>
      <input value="Enviar" type="submit">
    </div>
  </form>
</div>


<?php
include "include/common-footer.inc";
?>