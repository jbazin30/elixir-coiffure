<?php

$_result_tpl .= '<section class="flex-container-v" id="index">
    <div>
        <a href="?p=search">
			<img src="./templates/images/fiche_client.png"><br>
			Fichier clients
		</a>
        <a href="" class="js_add_client">
			<img src="./templates/images/ajout_client.png"><br>
			Nouveau client
		</a>
    </div>
	<div>
		<div class="b_white ba round">
			<div class="col bb b_white pa5">
				<a href="?p=search&amp;a=f">
					<img src="./templates/images/facturation.png" class="logo_facturation">
					Facturation
				</a>
			</div>
			';
if (isset($this->switch['is_lst_fac_prov'])) {
    $_result_tpl .= '
			<div class="pa5">
				';
    $count_i_0 = count($this->data['pages_index']['blocks']['lst_fac']);
    for ($i_0 = 0; $i_0 < $count_i_0; $i_0++) {
        $_result_tpl .= '
				<p><a href="?p=facturation&amp;f=' . $this->data['pages_index']['blocks']['lst_fac'][$i_0]['FAC_NUM'] . '">' . $this->data['pages_index']['blocks']['lst_fac'][$i_0]['FAC_CLI_NOM'] . ' - ' . $this->data['pages_index']['blocks']['lst_fac'][$i_0]['FAC_CLI_PRENOM'] . '</a></p>
				';
    }
    $_result_tpl .= '
			</div>
			';
}
$_result_tpl .= '
		</div>
	</div>
	<div>
		<a href="">
			<img src="./templates/images/stock.png"><br>
			Stock
		</a>
		<a href="?p=depense">
			<img src="./templates/images/depenses.png"><br>
			Dépenses
		</a>
		<a href="">
			<img src="./templates/images/caisse.png"><br>
			Caisse
		</a>
	</div>
</section>
';
?>