<main>
	<div class="main-container">
		<div class="air"></div>
		<div class="column1">
			<div class="form">
				<div class="form-name"><p>Поиск клиента</p></div>
				<form action="/operator/find_by_passport" method="POST">
					<label>Номер паспорта</label>
					<label>
                        <input pattern="^[0-9]{4} [0-9]{6}$" name="passport" required="required" placeholder="Серия и номер">
                    </label>
					<input class="button" type="submit" value="Поиск" title="Начать поиск клиента по номеру паспорта">
					<label class="report"><?php echo $data['message']['client'] ?></label>
				</form>
			</div>
		</div>
		<div class="column2">
			<div class="form">
				<div class="form-name"><p>Создать профиль клиента</p></div>
				<form action="/operator/create_client" method="POST">
					<label>Фамилия, имя, отчество</label>
					<label>
                        <input type="text" required="required" name="name" placeholder="Иванов Иван Иванович">
                    </label>

					<label>Телефон</label>
					<label>
                        <input type="tel" required="required" name="phone" placeholder="Номер телефона">
                    </label>

					<label>Номер паспорта</label>
					<label>
                        <input pattern="^[0-9]{4} [0-9]{6}$" required="required" name="passport" placeholder="Серия и номер">
                    </label>

					<label>Кем выдан</label>
					<label>
                        <input type="text" name="passgiven" placeholder="Название подразделения">
                    </label>

					<label>Код подразделения</label>
					<label>
                        <input pattern="^([0-9]{3}\-[0-9]{3}|)$" name="passcode" placeholder="000-000">
                    </label>

					<label>Дата выдачи</label>
					<label><input type="date" name="passdate"></label>

					<label>Пол</label>
					<label><input pattern="^[МЖ]$" name="sex" placeholder="М/Ж"></label>

					<label>Дата рождения</label>
					<label><input type="date" name="birthdate"></label>

					<label>Место рождения</label>
					<label><input type="text" name="birthplace" placeholder="Регион, город"></label>

					<label>Адрес регистрации</label>
					<label>
                        <input type="text" name="registration" placeholder="Индекс, регион, город, улица, дом, квартира">
                    </label>

					<label>Адрес проживания</label>
					<label>
                        <input type="text" name="address" placeholder="Индекс, регион, город, улица, дом, квартира">
                    </label>

					<label>Электронная почта</label>
					<label><input type="email" name="email" placeholder="example@email.com"></label>

					<input class="button" type="submit" value="Создать" title="Создать новый профиль клиента">
					<label class="report"><?php echo $data['message']['create_client'] ?></label>
				</form>
			</div>
		</div>
		<div class="air"></div>
	</div>

</main>
<script src="js/script.js"></script>