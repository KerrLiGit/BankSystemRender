<main>
	<div class="main-container">
        <div class="air"></div>
		<div class="column1">
			<div class="form">
				<a class="anchor" id="edit_client"></a>
				<div class="form-name">
					<p>Редактировать профиль клиента</p>
				</div>
				<form action="/client/edit" method="POST">
					<input type="hidden" name="client_uuid" value="<?php echo $data['client']['client_uuid'] ?>">
					<label>Фамилия, имя, отчество</label>
					<label>
						<input type="text" required="required" name="name" placeholder="Иванов Иван Иванович"
							value="<?php echo $data['client']['name'] ?>">
					</label>

					<label>Телефон</label>
					<label>
						<input type="tel" required="required" name="phone" placeholder="+78005553535"
							value="<?php echo $data['client']['phone'] ?>">
						</label>

					<label>Номер паспорта</label>
					<label>
						<input pattern="^[0-9]{4} [0-9]{6}$" required="required" name="passport"
							placeholder="Серия и номер" value="<?php echo $data['client']['passport'] ?>">
					</label>

					<label>Кем выдан</label>
					<label>
						<input type="text" name="passgiven" placeholder="Название подразделения"
							value="<?php echo $data['client']['passgiven'] ?>">
					</label>

					<label>Код подразделения</label>
					<label>
						<input pattern="^([0-9]{3}\-[0-9]{3}|)$" name="passcode" placeholder="000-000"
							value="<?php echo $data['client']['passcode'] ?>">
					</label>

					<label>Дата выдачи</label>
					<label>
						<input type="date" name="passdate"
							value="<?php echo $data['client']['passdate'] ?>">
						</label>

					<label>Пол</label>
					<label>
						<input pattern="^[МЖ]$" name="sex" placeholder="М/Ж"
							value="<?php echo $data['client']['sex'] ?>">
					</label>

					<label>Дата рождения</label>
					<label>
						<input type="date" name="birthdate"
							value="<?php echo $data['client']['birthdate'] ?>">
					</label>

					<label>Место рождения</label>
					<label>
						<input type="text" name="birthplace" placeholder="Регион, город"
							value="<?php echo $data['client']['birthplace'] ?>">
						</label>

					<label>Адрес регистрации</label>
					<label>
						<input type="text" name="reg" placeholder="Индекс, регион, город, улица, дом, квартира"
							value="<?php echo $data['client']['registration'] ?>">
					</label>

					<label>Адрес проживания</label>
					<label>
						<input type="text" name="address" placeholder="Индекс, регион, город, улица, дом, квартира"
							value="<?php echo $data['client']['address'] ?>">
						</label>

					<label>Электронная почта</label>
					<label>
						<input type="email" name="email" placeholder="example@email.com"
							value="<?php echo $data['client']['email'] ?>">
					</label>

					<input class="button" type="submit" value="Сохранить"
						title="Сохранить изменения персональных данных клиента">
					<label class="report"><?php echo $data['message']['edit'] ?></label>
				</form>
			</div>

			<!-- -------------------- СЧЕТА -------------------- -->
   			<div class="form">
				<a class="anchor" id="create_account"></a>
				<div class="form-name"><p>Открыть новый счет</p></div>
				<form action="/client/create_account" method="POST">
					<div class="form-content">
                        <input type="hidden" name="client_uuid" value="<?php echo $data['client']['client_uuid'] ?>">
						<label>Выберите валюту нового счёта</label>
						<?php
                        $cnt = 0;
                        foreach ($data['account_list']['currency'] as $currency) {
                            echo '<div class="radio-currency"><input class="radio" type="radio" name="currency"' .
                                'value="' . $currency["code"] . '"' . ($cnt == 0 ? 'checked=1' : '') . '><label>' .
                                $currency["isocode"] . " (" . $currency["name"] . ')</label></div>';
                            $cnt++;
                        }
						?>
					</div>
					<div><input class="button" type="submit" value="Открыть" title="Открыть новый счёт"></div>
					<label class="report"><?php echo $data['message']['create_account'] ?></label>
				</form>
			</div>

			<div class="form">
				<a class="anchor" id="close_account"></a>
				<div class="form-name"><p>Закрыть счет</p></div>
				<form action="/client/close_account" method="POST">
					<?php if (count($data['account_list']['close']) > 0) { /* есть счета 40817 с нулевым остатком */ ?>
					<div class="form-content">
						<label>Выберите счет</label>
						<label><div class="select-block"><select name="account_number" required>
							<option selected></option>
							<?php
                            foreach ($data['account_list']['close'] as $account) {
                                echo '<option value = "' . $account['account_number'] .
                                    '">Счет №' . $account['account_number'] . ': остаток 0 ' . $account["isocode"];
                            }
                            ?>
						</select></div></label>
					</div>
					<div>
						<input class="button" type="submit" value="Закрыть" title="Закрыть выбранный счёт">
					</div>
					<label class="report"><?php echo $data['message']['close_account']; ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет счетов с нулевым остатком</label>
					<?php } ?>
				</form>
			</div>

			<div class="form">
				<a class="anchor" id="push_account"></a>
				<div class="form-name"><p>Пополнить счет</p></div>
				<form action="/client/push_account" method="POST">
					<?php if (count($data['account_list']['all']) > 0) { /* есть счета 40817 */ ?>
					<div class="form-content">
						<label>Выберите счет</label>
						<label><div class="select-block"><select name="credit_account_number" required>
							<option selected></option>
							<?php
							foreach ($data['account_list']['all'] as $account) {
							    echo '<option value = "' . $account['account_number'] .
								    '">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
                            ?>
						</select></div></label>
					</div>
					<div>
						<label>Сумма пополнения
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Пополнить" title="Пополнить выбранный счёт">
					</div>
					<label class="report"><?php echo $data['message']['push_account'] ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет открытых счетов</label>
					<?php } ?>
				</form>
			</div>

			<div class="form">
				<a class="anchor" id="pop_account"></a>
				<div class="form-name"><p>Снять средства со счета</p></div>
				<form action="/client/pop_account" method="POST">
					<?php if (count($data['account_list']['all']) > 0) { /* есть счета 40817 */  ?>
					<div class="form-content">
						<label>Выберите счет</label>
						<label><div class="select-block"><select name="debit_account_number" required>
							<option selected></option>
							<?php
                            foreach ($data['account_list']['all'] as $account) {
							    echo '<option value = "' . $account['account_number'] .
								    '">Счет №' . $account['account_number'] . ': остаток ' .
								    sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
                            ?>
						</select></div></label>
					</div>
					<div>
						<label>Сумма снятия
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Снять" title="Снять средства с выбранного счёта">
					</div>
					<label class="report"><?php echo $data['message']['pop_account'] ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет открытых счетов</label>
					<?php } ?>
				</form>
			</div>
        </div>
        <div class="column2">
			<div class="form">
				<a class="anchor" id="transaction_in"></a>
				<div class="form-name"><p>Перевод средств между своими счетами</p></div>
				<form action="/client/transaction_in" method="POST">
					<?php if (count($data['account_list']['all']) > 0) { /* есть счета 40817 */  ?>
					<div class="form-content"><label>Счет отправки перевода</label>
						<label><div class="select-block"><select name="debit_account_number" required>
							<option selected></option>
							<?php
							foreach ($data['account_list']['all'] as $account) {
								echo '<option value = "' . $account['account_number'] .
									'">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
							?>
						</select></div></label>
					</div>
					<div class="form-content"><label>Счет приема перевода</label>
						<label><div class="select-block"><select name="credit_account_number" required>
							<option selected></option>
                            <?php
							foreach ($data['account_list']['all'] as $account) {
								echo '<option value = "' . $account['account_number'] .
									'">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
							?>
						</select></div></label>
					</div>
					<div>
						<label>Сумма перевода
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Перевести" title="Перевести со счёта на счёт">
					</div>
					<label class="report"><?php echo $data['message']['transaction_in'] ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет открытых счетов</label>
					<?php } ?>
				</form>
			</div>

			<div class="form">
				<a class="anchor" id="transaction_out"></a>
				<div class="form-name"><p>Перевод средств другому клиенту</p></div>
				<form action="/client/transaction_out" method="POST">
					<?php if (count($data['account_list']['all']) > 0) { /* есть счета 40817 */  ?>
					<div class="form-content"><label>Счет отправки перевода</label>
						<label><div class="select-block"><select name="debit_account_number" required>
							<option selected></option>
							<?php
							foreach ($data['account_list']['all'] as $account) {
								echo '<option value = "' . $account['account_number'] .
									'">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
							?>
						</select></div></label>
					</div>
					<div>
						<label>Перевод клиенту по номеру телефона:
                            <input type="tel" name="credit_phone" required placeholder="Номер телефона">
                        </label>
					</div>
					<div>
						<label>Сумма перевода
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Перевести"
                               title="Перевести средства на выбранный счёт получателя">
					</div>
					<label class="report"><?php echo $data['message']['transaction_out'] ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет открытых счетов</label>
					<?php } ?>
				</form>
			</div>

			<!-- -------------------- ВКЛАДЫ -------------------- -->
			<div class="form">
				<a class="anchor" id="open_deposit"></a>
				<div class="form-name"><p>Открытие вклада</p></div>
				<form action="/client/open_deposit" method="POST">
					<?php if (count($data['account_list']['all']) > 0) { /* есть счета 40817 */  ?>
					<div class="form-content"><label>Вид вклада</label>
						<label><div class="select-block"><select name="type" required>
							<option selected></option>
							<?php
                            foreach ($data['deposit_list']['term'] as $term) {
								echo '<option value="' . $term['type'] . '">' . $term['description'] . '</option>';
							}
                            ?>
						</select></div></label>
					</div>
					<div class="form-content"><label>Средства для вклада со счета</label>
						<label><div class="select-block"><select name="debit_account_number" required>
							<option selected></option>
                            <?php
							foreach ($data['account_list']['all'] as $account) {
								echo '<option value = "' . $account['account_number'] .
									'">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
							?>
						</select></div></label>
                    </div>
                    <div>
						<label>Сумма вклада
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Открыть" title="Открыть выбранный вид вклада">
					</div>
					<label class="report"><?php echo $data['message']['open_deposit'] ?></label>
					<?php } else { /* нет подходящих счетов */ ?>
					<label class="information">Нет открытых счетов</label>
					<?php } ?>
				</form>
			</div>

			<div class="form">
				<a class="anchor" id="close_deposit"></a>
				<div class="form-name"><p>Закрытие вклада</p></div>
				<form action="/client/close_deposit" method="POST">
					<?php if (count($data['deposit_list']['all']) > 0) {  /* есть действующие вклады */ ?>
					<div class="form-content"><label>Выберите вклад</label>
						<label><div class="select-block"><select name="deposit_id" required>
							<option selected></option>
							<?php
                            foreach ($data['deposit_list']['all'] as $deposit) {
								echo '<option value = "' . $deposit['deposit_id'] . '">' . $deposit['description'] .
								', сумма ' . $deposit['balance'] . " " . $deposit['isocode'] .
								', окончание ' . $deposit['end_date'] . '</option>';
							}
                            ?>
						</select></div></label>
					</div>
					<div class="form-content"><label>Средства перечислятся</label>
						<label><div class="select-block"><select name="account_number" required>
							<option selected></option>
							<?php
							foreach ($data['account_list']['all'] as $account) {
								echo '<option value = "' . $account['account_number'] .
									'">Счет №' . $account['account_number'] . ': остаток ' .
									sprintf("%.2f", $account['balance']) . ' ' . $account["isocode"];
							}
                            ?>
						</select></div></label>
					</div>
                    <div>
						<input class="button" type="submit" value="Закрыть" title="Закрыть выбранный вклад">
					</div>
					<label class="report"><?php echo $data['message']['close_deposit'] ?></label>
					<?php } else { /* нет действующих вкладов */?>
					<label class="information">Нет действующих вкладов</label>
					<?php } ?>
				</form>

			</div>


			<!-- -------------------- КРЕДИТЫ -------------------- -->
			<div class="form">
				<a class="anchor" id="open_credit"></a>
				<div class="form-name"><p>Выдача кредита</p></div>
				<form action="/client/open_credit" method="POST">
					<div class="form-content"><label>Вид кредита</label>
						<label><div class="select-block"><select name="type" required>
							<option selected></option>
							<?php
                            foreach ($data['credit_list']['term'] as $term) {
                                echo '<option value = "' . $term['type'] . '">' . $term['description'] . ' (' .
                                    $term['month_cnt'] . ' мес., '. $term['rate'] . '% годовых)' . '</option>';
                            }
                            ?>
						</select></div></label>
					</div>
                    <div>
						<label>Сумма кредита
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sum" required placeholder="100.00">
                        </label>
					</div>
					<div>
						<input class="button" type="submit" value="Выдача" title="Выдача кредита по выбранным условиям">
					</div>
					<label class="report"><?php echo $data['message']['open_credit'] ?></label>
				</form>
			</div>

            <div class="form">
                <a class="anchor" id="graph_credit"></a>
                <div class="form-name"><p>Просмотр состояния и графика погашения по кредиту</p></div>
                <form action="/graph" method="POST">
					<?php if (count($data['credit_list']['all']) > 0) {  /* есть действующие кредиты */ ?>
                        <div class="form-content"><label>Кредит</label>
                            <label><div class="select-block"><select name="credit_id" required>
                                <option selected></option>
								<?php
								foreach ($data['credit_list']['all'] as $credit) {
									echo '<option value="' . $credit['credit_id'] .
									'">Кредит №' . $credit['credit_id'] . ' ' . $credit['description'] . ', ' .
									$credit['balance'] . ' ' . $credit['isocode'] . ', ' .
									sprintf("%.2f", $credit['rate']) . '%</option>';
								}
                                ?>
                            </select></div></label>
                        </div>
                        <div>
                            <input class="button" type="submit" value="Состояние" title="Просмотр состояния кредита">
                        </div>
                        <label class="report"><?php echo $data['message']['graph_credit'] ?></label>
					<?php } else { /* нет действующих кредитов */?>
                        <label class="information">Нет действующих кредитов</label>
					<?php } ?>
                </form>
            </div>

			<div class="form">
				<a class="anchor" id="close_credit"></a>
				<div class="form-name"><p>Закрытие кредита</p></div>
				<form action="client/close_credit" method="POST">
					<?php if (count($data['credit_list']['all']) > 0) {  /* есть действующие кредиты */ ?>
					<div class="form-content"><label>Кредит</labelp>
						<label><div class="select-block"><select name="credit_id" required>
							<option selected></option>
							<?php
							foreach ($data['credit_list']['all'] as $credit) {
								echo '<option value="' . $credit['credit_id'] .
									'">Кредит №' . $credit['credit_id'] . ' ' . $credit['description'] . ', ' .
									$credit['balance'] . ' ' . $credit['isocode'] . ', ' .
									sprintf("%.2f", $credit['rate']) . '%</option>';
							}
							?>
						</select></div></label>
					</div>
					<div>
						<input class="button" type="submit" value="Закрыть" title="Закрытие кредита">
					</div>
					<label class="report"><?php echo $data['message']['close_credit'] ?></label>
					<?php } else { /* нет действующих кредитов */?>
					<label class="information">Нет действующих кредитов</label>
					<?php } ?>
				</form>
			</div>


		</div>
		<div class="air"></div>

	</div>
</main>