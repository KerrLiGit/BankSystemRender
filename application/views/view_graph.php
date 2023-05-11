<main>
	<div class="main-container">
		<div class="column">
            <!--<div class="form">-->
                <div class="form-name" style="width:100%"><p>Просмотр текущего состояния кредита</p></div>
			    <form action="/client#graph_credit" method="POST" style="width:100%">
                    <div class="info-container">
					<?php if (!$data['credit']) { ?>
                            <p class='infcp'>Информация по кредиту № <?php echo $data['credit_id'] ?> не найдена!</p>
					<?php } else { ?>
						    <p class='infcph'>Информация по кредиту № <?php echo $data['credit']['credit_id'] ?> от
                                <?php echo $data['credit']['open_date'] ?> по состоянию на
                                <?php echo $data['current_date'] ?>
                            </p>
					<?php } ?>
                        <p class='infcp'><br>
						    Наименование продукта: <?php echo $data['credit']['description'] ?><br>
						    Сумма кредита: <?php echo $data['credit']['credit_sum'] . ' ' .
                                $data['credit']['isocode'] ?><br>
						    Процентная ставка: <?php echo $data['credit']["rate"] ?>% годовых<br>
						    Штрафная ставка: <?php echo $data['credit']['ovd_rate'] ?> % годовых<br>
                            Дата последнего пересчета: <?php echo $data['tail_sum']['up_date'] ?><br>
                            Текущая сумма основного долга: <?php echo $data['tail_sum']['main_sum'] . ' ' .
								$data['credit']['isocode'] ?><br>
						    Текущая сумма начисленных процентов: <?php echo $data['tail_sum']['main_perc_sum'] .
                                ' + ' . $data['tail_sum']['main_perc_sum_after_update'] . ' ' .
                                $data['credit']['isocode'] ?><br>
						    Текущая сумма просроченного долга: <?php echo $data['tail_sum']['delay_sum'] . ' ' .
								$data['credit']['isocode'] ?><br>
						    Текущая сумма просроченных процентов: <?php echo $data['tail_sum']['delay_perc_sum'] .
								' + ' . $data['tail_sum']['delay_perc_sum_after_update'] . ' ' .
								$data['credit']['isocode'] ?><br>
                            Задолженность итоговая: <?php echo $data['tail_sum']['total'] . ' ' .
								$data['credit']['isocode'] ?><br>
						    Остаток на текущем счете <?php echo $data['tail_sum']['current_sum'] . ' ' .
								$data['credit']['isocode'] ?><br>
						    <?php if ($data['tail_sum']['current_sum'] < $data['tail_sum']['total']) { ?>
							    Для немедленного погашения необходимо внести на текущий счет:
                                <?php echo ($data['tail_sum']['total'] - $data['tail_sum']['current_sum']) . ' ' .
								$data['credit']['isocode'] ?><br>
                            <?php }	else { ?>
							    Средств на текущем счете достаточно для немедленного погашения<br>
							<?php } ?>
						</p>
						<?php if ($data['credit']) { ?>
                    </div>
                    <div class="table-container">
                        <div class="form-name" style="width:100%"><p>График погашений</p></div>
                        <table>
                            <tr>
                                <th>Дата платежа</th>
                                <th>Сумма осн. долга</th>
                                <th>Сумма процентов</th>
                                <th>Всего</th>
                                <th>Обработано</th>
                            </tr>
							<?php foreach ($data['graph'] as $row) { ?>
								<tr>
								<td><?php echo $row["payment_date"] ?></td>
								<td><?php echo $row['sum_main'] ?></td>
								<td><?php echo $row['sum_perc'] ?></td>
								<td><?php echo $row['sum_total'] ?></td>
								<td><?php echo ($row["processed"] ? "&check;" : "") ?></td>
								</tr>
							<?php } ?>
							    <tr>
							    <td>Итого:</td>
						    	<td><?php echo $data['total_graph']['sum_main'] ?></td>
							    <td><?php echo $data['total_graph']['sum_perc'] ?></td>
							    <td><?php echo $data['total_graph']['sum_total'] ?></td>
						    	<td></td>
						    	</tr>
                        </table>
                    </div>
					<?php } ?>
				    <input class="button" type="submit" value="Назад" title="Вернуться к работе с клиентом">
			    </form>
		    </div>

	</div>
</main>