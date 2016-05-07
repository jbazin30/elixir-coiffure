<?php
$_result_tpl .= '<section id="js_facturation">
    ';
$count_i_0 =  count($this->data['pages_facturation']['blocks']['lst_fam']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '    <div class="inbl mbl mtm">
        <button data-fam-num="' . $this->data['pages_facturation']['blocks']['lst_fam'][$i_0]['FAM_NUM'] . '" class="js_btn_get_pres">' . $this->data['pages_facturation']['blocks']['lst_fam'][$i_0]['FAM_LIBEL'] . '</button>
    </div>
    ';
}
$_result_tpl .=  '
    ';
if ( isset ($this->switch['show_fac']))
{
$_result_tpl .=  '
    <form action="">
        <p data-fac-num="' . ((isset($this->data['pages_facturation']['vars']['FAC_NUM'])) ? $this->data['pages_facturation']['vars']['FAC_NUM'] : $this->data['parent']['vars']['FAC_NUM'])  . '">Facture n° ' . ((isset($this->data['pages_facturation']['vars']['FAC_NUM'])) ? $this->data['pages_facturation']['vars']['FAC_NUM'] : $this->data['parent']['vars']['FAC_NUM'])  . ' <button class="js_btn_del_fac mlm">Supprimer la facture</button></p>
        <p>Nom : ' . ((isset($this->data['pages_facturation']['vars']['CLI_NOM'])) ? $this->data['pages_facturation']['vars']['CLI_NOM'] : $this->data['parent']['vars']['CLI_NOM'])  . '</p>
        <p>Prénom : ' . ((isset($this->data['pages_facturation']['vars']['CLI_PRENOM'])) ? $this->data['pages_facturation']['vars']['CLI_PRENOM'] : $this->data['parent']['vars']['CLI_PRENOM'])  . '</p>
        <table class="mts">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Remise</th>
                    <th>Prix remisé</th>
                    <th>Suppression</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td class="t_right">Total :</td>
                    <td class="total_base">' . ((isset($this->data['pages_facturation']['vars']['TOTAL_BASE'])) ? $this->data['pages_facturation']['vars']['TOTAL_BASE'] : $this->data['parent']['vars']['TOTAL_BASE'])  . '</td>
                    <td></td>
                    <td class="total_applied">' . ((isset($this->data['pages_facturation']['vars']['TOTAL_APPLIED'])) ? $this->data['pages_facturation']['vars']['TOTAL_APPLIED'] : $this->data['parent']['vars']['TOTAL_APPLIED'])  . '</td>
                    <td></td>
                </tr>
            </tfoot>
            <tbody>
                ';
if ( isset ($this->switch['is_lg_fac']))
{
$_result_tpl .=  '
                ';
$count_i_0 =  count($this->data['pages_facturation']['blocks']['lst_ligne_fac']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '                <tr data-pres-num="' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['PRES_NUM'] . '" data-fac-num-lg="' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['FAC_NUM'] . '">
                    <td class="pres_libel">' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['PRES_LIBEL'] . '</td>
                    <td class="pres_prix_base">' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['PRES_PRIX_BASE'] . '</td>
                    <td>
                        <input type="text" value="' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['PRES_REMISE'] . '" class="js_pres_remise">
                    </td>
                    <td><input type="text" value="' . $this->data['pages_facturation']['blocks']['lst_ligne_fac'][$i_0]['PRES_PRIX_APPLIED'] . '" class="pres_prix_applied" readonly="readonly"></td>
                    <td><button class="js_btn_del_fac_pres">ok</button></td>
                </tr>
                ';
}
$_result_tpl .=  '                ';
}
$_result_tpl .=  '
            </tbody>
        </table>
        <div>
			';
if ( isset ($this->switch['is_included_fac']))
{
$_result_tpl .=  '
			<p>Factures associées :
				';
$count_i_0 =  count($this->data['pages_facturation']['blocks']['lst_fac_included']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '				<button class="js_del_fac_included" data-fac-included="' . $this->data['pages_facturation']['blocks']['lst_fac_included'][$i_0]['FAC_NUM'] . '"> ' . $this->data['pages_facturation']['blocks']['lst_fac_included'][$i_0]['FAC_NUM'] . ' (supprimer)</button>
				';
}
$_result_tpl .=  '			</p>
			';
}
$_result_tpl .=  '
            <p><button class="js_add_waiting_fac">Associer une autre facture</button></p>
        </div>
        <div id="paiement">
            <h1>Paiement</h1>
            <label for="cb"><img src="./templates/images/cb.png" alt=""/></label> <input type="text" name="cb" id="cb"><br>
            <label for="cheque"><img src="./templates/images/logo_cheque.jpg" alt=""/></label> <input type="text" name="cheque" id="cheque"><br>
            <label for="espece"><img src="./templates/images/especes.png" alt=""/></label> <input type="text" name="espece" id="espece"> <span class="rendu"></span>
            <p>Total paiement : <span class="total_pai"></span></p>
        </div>
        <p><button class="js_btn_validate_fac">Valider la facture</button></p>
    </form>
    ';
}
$_result_tpl .=  '
</section>
';
?>