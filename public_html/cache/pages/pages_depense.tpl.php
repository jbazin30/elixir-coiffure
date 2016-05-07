<?php

$_result_tpl .= '<section id="depenses">
	<form action="#" method="post">
		<p><label for="libel">Détails :</label> <input type="text" id="libel" name="libel" size="40"></p>
		<p><label for="date">Date :</label> <input type="text" id="date" name="date" size="10" class="datepicker"></p>
		<p><label for="prix">Prix :</label> <input type="text" id="prix" name="prix" size="8"></p>
		<button type="button" class="add_value mtm">Sauvegarder</button>
		<span class="return"></span>
	</form>
	';
if (isset($this->switch['is_lst_ach'])) {
    $_result_tpl .= '
	<table class="footable mtl">
		<thead>
			<tr>
				<th>N°</th>
				<th>Libellé</th>
				<th>Prix</th>
				<th>Date</th>
			</tr>
		</thead>
		';
    $count_i_0 = count($this->data['pages_depense']['blocks']['lst_ach']);
    for ($i_0 = 0; $i_0 < $count_i_0; $i_0++) {
        $_result_tpl .= '		<tr>
			<td>' . $this->data['pages_depense']['blocks']['lst_ach'][$i_0]['ACH_NUM'] . '</td>
			<td>' . $this->data['pages_depense']['blocks']['lst_ach'][$i_0]['ACH_LIBEL'] . '</td>
			<td>' . $this->data['pages_depense']['blocks']['lst_ach'][$i_0]['ACH_PRIX'] . '</td>
			<td>' . $this->data['pages_depense']['blocks']['lst_ach'][$i_0]['ACH_DATE'] . '</td>
		</tr>
		';
    }
    $_result_tpl .= '	</table>
	';
}
$_result_tpl .= '
</section>
';
?>