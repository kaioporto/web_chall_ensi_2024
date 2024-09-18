<?php
include 'include/common-header.inc';
include "lib/database_pdo.php";

$username =  $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	global $db;
	if(empty(trim($_POST["username"]))){
		$username_err = "<p id='warning'>Digite um nome de usuário.</p>\n";
	}else{
		$where = array("username" => $_POST['username']);
		$user = $db->select_columns("users", array("id"), $where);
		if(!empty($user)){
			$username_err = "<p id='warning'>Este usuário já está sendo usado.</p>\n";
		} else {
			$user = $_POST['username'];
		}
	}	

	if(empty(trim($_POST["email"]))){
		$email_err = "<p id='warning'>Digite um nome de usuário.</p>\n";
	} 
	else{
		$email = $_POST['email'];
	}

	if(empty(trim($_POST['password']))){
		$password_err = "<p id='warning'>Digite uma senha.</p>\n";
	} elseif(strlen($_POST["password"]) < 8){
		$password_err = "<p id='warning'>Digite uma senha de no mínimo 8 caracteres.</p>\n";
	} else{
		$password = $_POST['password'];
	}

	if(empty($_POST["confirm_password"])){
		$confirm_password_err = "<p id='warning'>Confirme sua senha!</p>\n";
	} elseif($password != $_POST['confirm_password']){
			$confirm_password_err = "<p id='warning'>As senhas não casam!</p>\n";
	}
	if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$params = array("username" => $user, "password" => $hashed_password, "email" => $email);
		if($db->insert("users", $params)){
			header("location: login.php");
		}else{
			print "<p id='warning'>Algo deu errado. Tente novamente mais tarde.<\p>\n";
		}
	}
}
?>
<h2>Criar conta</h2>
<p>Preencha o formulário abaixo pra criar uma conta</p>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="login-form">
	<div class="form-group" <?php echo (empty($username_err)) ? 'id=\'warning\'' : ''; ?> >
		<label>User</label><br>
		<input type="text" name="username" value="<?php echo $username; ?>"><br>
		<span class="help-block"><?php echo $username_err; ?></span>
	</div>
	<div class="form-group" <?php echo (empty($email_err)) ? 'id=\'warning\'' : ''; ?> >
		<label>E-mail</label><br>
		<input type="email" name="email" value="<?php echo $email; ?>"><br>
		<span class="help-block"><?php echo $email_err; ?></span>
	</div>
	<div class="form-group" <?php echo (empty($password_err)) ? 'id=\'warning\'' : ''; ?> >
		<label>Password</label><br>
		<input type="password" name="password" value="<?php echo $password; ?>"><br>
		<span class="help-block"><?php echo $password_err; ?></span>
	</div>

	<div class="form-group" <?php echo (empty($confirm_password_err)) ? 'id=\'warning\'' : ''; ?> >
		<label>Confirm password</label><br>
		<input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>"><br><br>
		<span class="help-block"><?php echo $confirm_password_err; ?></span>
	</div>
	<input type="submit" name="register" value="Registrar"><br>
	<p>Already have an account? <a href="login.php">Login here</a>.</p>
</form>

<?php
include 'include/common-footer.inc';
?>
