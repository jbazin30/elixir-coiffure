<?php
$_result_tpl .= '<section class="grid-2" id="js_fiche_client">
    <div>
        <p>
            <label for="cli_num">N° :</label>
            <input type="text" name="cli_num" id="cli_num" value="' . ((isset($this->data['pages_client']['vars']['CLI_NUM'])) ? $this->data['pages_client']['vars']['CLI_NUM'] : $this->data['parent']['vars']['CLI_NUM'])  . '">
        </p>
        <p>
            <label for="cli_sexe">Sexe :</label>
            ' . ((isset($this->data['pages_client']['vars']['LST_SEXE'])) ? $this->data['pages_client']['vars']['LST_SEXE'] : $this->data['parent']['vars']['LST_SEXE'])  . '
        </p>
        <p class="cli_nom">
            <label for="cli_nom">Nom :</label>
            <input type="text" name="cli_nom" id="cli_nom" value="' . ((isset($this->data['pages_client']['vars']['CLI_NOM'])) ? $this->data['pages_client']['vars']['CLI_NOM'] : $this->data['parent']['vars']['CLI_NOM'])  . '">
        </p>
        <p class="cli_pname">
            <label for="cli_prenom">Prénom :</label>
            <input type="text" name="cli_prenom" id="cli_prenom" value="' . ((isset($this->data['pages_client']['vars']['CLI_PNOM'])) ? $this->data['pages_client']['vars']['CLI_PNOM'] : $this->data['parent']['vars']['CLI_PNOM'])  . '">
        </p>
        <p>
            <label for="cli_adr">Adresse :</label>
            <input type="text" name="cli_adr" id="cli_adr" value="' . ((isset($this->data['pages_client']['vars']['CLI_ADR'])) ? $this->data['pages_client']['vars']['CLI_ADR'] : $this->data['parent']['vars']['CLI_ADR'])  . '">
        </p>
        <p>
            <label for="cli_cp">Code postal :</label>
            <input type="text" name="cli_cp" id="cli_cp" value="' . ((isset($this->data['pages_client']['vars']['CLI_CP'])) ? $this->data['pages_client']['vars']['CLI_CP'] : $this->data['parent']['vars']['CLI_CP'])  . '">
        </p>
        <p>
            <label for="cli_ville">Ville :</label>
            <input type="text" name="cli_ville" id="cli_ville" value="' . ((isset($this->data['pages_client']['vars']['CLI_VILLE'])) ? $this->data['pages_client']['vars']['CLI_VILLE'] : $this->data['parent']['vars']['CLI_VILLE'])  . '">
        </p>
        <p>
            <label for="cli_tel">Téléphone :</label>
            <input type="text" name="cli_tel" id="cli_tel" value="' . ((isset($this->data['pages_client']['vars']['CLI_TEL'])) ? $this->data['pages_client']['vars']['CLI_TEL'] : $this->data['parent']['vars']['CLI_TEL'])  . '">
        </p>
        <p>
            <label for="cli_mobile">Mobile :</label>
            <input type="text" name="cli_mobile" id="cli_mobile" value="' . ((isset($this->data['pages_client']['vars']['CLI_MOBILE'])) ? $this->data['pages_client']['vars']['CLI_MOBILE'] : $this->data['parent']['vars']['CLI_MOBILE'])  . '">
        </p>
        <p>
            <label for="cli_email">Email :</label>
            <input type="text" name="cli_email" id="cli_email" value="' . ((isset($this->data['pages_client']['vars']['CLI_EMAIL'])) ? $this->data['pages_client']['vars']['CLI_EMAIL'] : $this->data['parent']['vars']['CLI_EMAIL'])  . '">
        </p>
        <p>
            <label for="cli_naiss">Date de naissance :</label>
            <input type="text" name="cli_naiss" id="cli_naiss" class="datepicker" value="' . ((isset($this->data['pages_client']['vars']['CLI_NAIS'])) ? $this->data['pages_client']['vars']['CLI_NAIS'] : $this->data['parent']['vars']['CLI_NAIS'])  . '">
        </p>
        <p>
            <label for="cli_genre">Genre :</label>
            ' . ((isset($this->data['pages_client']['vars']['LST_GEN'])) ? $this->data['pages_client']['vars']['LST_GEN'] : $this->data['parent']['vars']['LST_GEN'])  . '
        </p>
        <p>
            <button type="button" class="js_btn_edit_client">Modifier</button>
            <button class="js_back_search">Retour</button>
            <!--<button type="button" class="js_btn_del_client">Supprimer</button>-->
        </p>
    </div>
    <div>
        <div class="t_center">
            <button class="js_btn_add_svc" data-fam-num="1">Couleur</button>
            <button class="js_btn_add_svc" data-fam-num="2">Mèches</button>
            <button class="js_btn_add_svc" data-fam-num="3">Forme</button>
        </div>
        <div class="mtm">
            ';
if ( isset ($this->switch['is_lst_svc']))
{
$_result_tpl .=  '
            <table class="footable">
                <tr>
                    <th>Date</th>
                    <th>Prestation</th>
                    <th>Suppression</th>
                </tr>
                ';
$count_i_0 =  count($this->data['pages_client']['blocks']['lst_svc']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '                <tr>
                    <td class="js_get_svc" data-svc-num="' . $this->data['pages_client']['blocks']['lst_svc'][$i_0]['SVC_NUM'] . '">' . $this->data['pages_client']['blocks']['lst_svc'][$i_0]['SVC_DATE'] . '</td>
                    <td class="js_get_svc" data-svc-num="' . $this->data['pages_client']['blocks']['lst_svc'][$i_0]['SVC_NUM'] . '">' . $this->data['pages_client']['blocks']['lst_svc'][$i_0]['FAM_LIBEL'] . '</td>
                    <td><button class="ui-button ui-widget italic" onclick="O.Ajax.delService(' . $this->data['pages_client']['blocks']['lst_svc'][$i_0]['SVC_NUM'] . ')">supprimer</button></td>
                </tr>
                ';
}
$_result_tpl .=  '            </table>
            ';
}
$_result_tpl .=  '
        </div>
    </div>
    <div>
        <p>Liste des factures</p>
        ';
if ( isset ($this->switch['is_lst_fac']))
{
$_result_tpl .=  '
        <table class="footable">
            <tr>
                <th>N°</th>
                <th>Date</th>
                <th></th>
            </tr>
            ';
$count_i_0 =  count($this->data['pages_client']['blocks']['lst_fac']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '            <tr>
                <td>' . $this->data['pages_client']['blocks']['lst_fac'][$i_0]['FAC_NUM'] . '</td>
                <td>' . $this->data['pages_client']['blocks']['lst_fac'][$i_0]['FAC_DATE'] . '</td>
                <td><a href="?p=facture&f=' . $this->data['pages_client']['blocks']['lst_fac'][$i_0]['FAC_NUM'] . '&amp;c=' . ((isset($this->data['pages_client']['vars']['CLI_NUM'])) ? $this->data['pages_client']['vars']['CLI_NUM'] : $this->data['parent']['vars']['CLI_NUM'])  . '">Voir</a></td>
            </tr>
            ';
}
$_result_tpl .=  '        </table>
        ';
}
else
{
$_result_tpl .=  '
        Aucunes factures pour le moment
        ';
}
$_result_tpl .=  '
    </div>
</section>
';
?>