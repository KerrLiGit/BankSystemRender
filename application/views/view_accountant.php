<main>
	<div class="client-info"><p>Текущая дата: <?php echo $data['current_date'] ?></p></div>
	<div class="main-container">
		<div class="air"></div>
		<div class="column1">
			<div class="form">
				<a class="anchor" id="change_operdate"></a>
				<div class="form-name"><p>Закрытие текущего рабочего дня и открытие следующего</p></div>
				<form action="/accountant/change_operdate" method="POST">
					<label>Дата нового открытого дня</label>
					<label><input type="date" name="date"></label>
					<input class="button" type="submit" value="Установить"
                           title="Установить указанный день как текущий">
					<label class="report"><?php echo $data['message']['change_operdate'] ?></label>
				</form>
			</div>
			<div class="form">
				<a class="anchor" id="transaction_acc"></a>
				<div class="form-name"><p>Перевод средств между кассами и счетами банка</p></div>
				<form action="/accountant/transaction_acc" method="POST">
					<div class="form-content"><p>Счет отправки перевода (дебета)</p>
						<label><div class="select-block"><select name="debit_account_number" required>
									<option selected></option>
									<?php
									foreach ($data['bank_account'] as $account) {
										echo '<option value="' . $account['account_number'] . '">Счет №' .
											$account['account_number'] . ': ' . $account['balance'] . ' ' .
                                            $account['isocode'] . ', ' . $account['description'] . ' (' .
                                            $account['typemark'] . ')' . '</option>';
									}
                                    ?>
								</select></div></label>
					</div>
					<div class="form-content"><p>Счет приема перевода (кредита)</p>
						<label><div class="select-block"><select name="credit_account_number" required>
									<option selected></option>
									<?php
									foreach ($data['bank_account'] as $account) {
										echo '<option value="' . $account['account_number'] . '">Счет №' .
											$account['account_number'] . ': ' . $account['balance'] . ' ' .
											$account['isocode'] . ', ' . $account['description'] . ' (' .
											$account['typemark'] . ')' . '</option>';
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
						<input class="button" type="submit" value="Перевести"
                               title="Перевести указанную сумму между счетами банка">
					</div>
					<label class="report"><?php echo $data['message']['transaction_acc'] ?></label>
				</form>
			</div>
		</div>
		<div class="column2">
			<div class="form">
				<a class="anchor" id="change_currency_cost"></a>
				<div class="form-name"><p>Обновить курс валют</p></div>
				<form action="/accountant/change_currency_cost" method="POST">
					<div class="form-content">
						<p>Валюта</p>
						<?php
						$cnt = 0;
                        foreach ($data['currency'] as $currency) {
                            echo '<div class="radio-currency"><input class="radio" type="radio" name="currency"' .
                                'value="' . $currency["code"] . '"' . ($cnt == 0 ? 'checked=1' : '') . '><label>' .
                                $currency["isocode"] . " (" . $currency["name"] . ')</label></div>';
                            $cnt++;
                        }
						?>
					</div>
					<div>
                        <label>Стоимость покупки в рублях
                            <input pattern="^\d+(\.\d{1,2}|)$" name="buy_sum" required placeholder="100.00">
                        </label>
                    </div>
					<div>
                        <label>
                            Стоимость, установленная ЦБ в рублях
                            <input pattern="^\d+(\.\d{1,2}|)$" name="cost_sum" required placeholder="100.00">
                        </label>
                    </div>
					<div>
                        <label>
                            Стоимость продажи в рублях
                            <input pattern="^\d+(\.\d{1,2}|)$" name="sell_sum" required placeholder="100.00">
                        </label>
                    </div>
					<div><input class="button" type="submit" value="Обновить" title="Обновить текущий курс валют"></div>
					<label class="report"><?php echo $data['message']['change_currency_cost'] ?></label>
				</form>
			</div>
		</div>
		<div class="air"></div>
	</div>
</main>
<script src="js/script.js"></script>