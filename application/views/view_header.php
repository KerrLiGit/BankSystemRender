<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/base.css">
	<link rel="stylesheet" href="/css/navbar.css">
	<link rel="stylesheet" href="/css/main-container.css">
	<link rel="stylesheet" href="/css/graph_credit.css">
	<link rel="icon" type="image/png" href="/images/favicon.png">
	<title>Оператор<?php echo " - " . $data['user']['name'] ?></title>
</head>
<body>
<header class="header">
	<div class="header-container">
		<div class="header-menu">
			<div class="subbutton" onclick="document.location.href='/operator'">Оператор</div>
			<div class="subbutton" onclick="document.location.href='/accountant'">Бухгалтер</div>
		</div>
		<div class="role-name">
		<span>
			Сотрудник: <?php echo $data['user']['name'] . ', ' . $data['user']['role_description'] ?><br>
		</span>
		<span>
		<?php if ($data['client']['name'] != '')
			echo 'Работа с клиентом: ' . $data['client']['name'] . ', паспорт ' . $data['client']['passport'] ?>
		</span>
		</div>
		<div class="exit-button subbutton">
			<div onclick="document.location.href='/auth/signout'">Выход</div>
		</div>
	</div>
</header>

<?php include 'application/views/' . $content_view ?>

</body>
</html>