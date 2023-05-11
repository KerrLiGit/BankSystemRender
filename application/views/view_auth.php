<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/auth.css">
	<link rel="stylesheet" href="/css/main-container.css">
	<link rel="icon" type="image/png" href="/images/favicon.png">
	<title>Авторизация</title>
</head>
<body>
<main>
	<div class="main-container">
		<div class="column" style="width:40%">
			<div class="form">
				<div class="form-name"><p>OBS Bank System</p></div>
				<form action= "/auth/signin" method="POST">
 					<input type="hidden" name="query" value="sign_in">
					<label>Логин</label>
					<label>
						<input type="text" name="login" placeholder="Введите логин" required>
					</label>
					<label>Пароль</label>
					<label>
						<input type="password" name="pass" placeholder="Введите пароль" required>
					</label>
					<input class="button" type="submit" value="Вход в систему">
					<label class="report"><?php echo $data['message']['auth'] ?></label>
				</form>
			</div>
		</div>
	</div>

</main>
<script src="js/script.js"></script>
</body>
</html>