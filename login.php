<?php
include 'include/common-header.inc';
require_once("lib/database_pdo.php");

$username = $password = "";
$username_err = $password_err = "";


if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(empty($_POST["username"])){
		$username_err = "<p id='warning'>Digite um nome de usuário.</p>\n";
	} else{
		$user = $_POST["username"];
	}

	if(empty($_POST["password"])){
		$password_err = "<p id='warning'>Digite uma senha.</p>\n";
	} else{
		$password = $_POST["password"];
	}
	if(empty($username_err) && empty($password_err)){
		$params = array("id", "username", "password");
		$where = array("username" => $user);
		global $db;
		$result = $db->select_columns("users", $params, $where);
		if(!empty($result) && count($result) == 3){
			$id = $result["id"];
			$username = $result["username"];
			$hashed_password = $result["password"];
			if(password_verify($password, $hashed_password)){
				session_start();
				$_SESSION['logged'] = true;
				$_SESSION['id'] = $id;
				$_SESSION['username'] = $user;

				header("location: index.php");
			} else{
				$password_err = "<p id='warning'>Dados inválidos!</p>";
			}
		} else{
			$password_err = "<p id='warning'>Opa! Algo deu errado! Tente novamente mais tarde. :(</p>";
		}
	}
}

?>
	<div class="wrapper">
		<h2>Login</h2>
		<p>Preencha os campos abaixo para logar no sistema.</p>
 		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group" <?php echo (!empty($username_err)) ? 'id=\'warning\'' : ''; ?>>
				<label>Username</label><br>
				<input type="text" name="username" class="form-control" value="<?php echo $username ?>"><br>
				<span id="warning"><?php echo $username_err; ?></span>
			</div><br>
			<div class="form-group" <?php echo (!empty($password_err)) ? 'id=\'warning\'' : ''; ?>>
				<label>Password</label><br>
				<input type="password" name="password" class="form-control"><br><br>
			</div>
			<div class="form-group">
				<input type="submit" value="Login"><br>
				<span id="warning"><?php echo $password_err; ?></span>
			</div><br>
			<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
		</form>
	</div>

<?php
include 'include/common-footer.inc';
?>
