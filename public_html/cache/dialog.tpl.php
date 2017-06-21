<?php

$_result_tpl .= '';
if (isset($this->switch['dialog_client'])) {
    $_result_tpl .= '
<div id="js_add_service" title="Ajouter une technique">
    <p class="client"><span class="name"></span> <span class="pname"></span> - le <span class="date">' . ((isset($this->data['dialog']['vars']['DATE'])) ? $this->data['dialog']['vars']['DATE'] : $this->data['parent']['vars']['DATE']) . '</span></p>
    <form id="js_frm_service">
        <fieldset>
            <p>Technique : <span class="tech"></span></p>
            <textarea name="details" id="details" class="text ui-widget-content ui-corner-all" rows="20"></textarea>
        </fieldset>
    </form>
</div>
<div id="js_get_service" title="Consulter une technique">
    <p class="client"><span class="name"></span> <span class="pname"></span> - le <span class="date">' . ((isset($this->data['dialog']['vars']['DATE'])) ? $this->data['dialog']['vars']['DATE'] : $this->data['parent']['vars']['DATE']) . '</span></p>
    <form id="js_frm_service">
        <fieldset>
            <p>Technique : <span class="tech"></span></p>
            <textarea name="details" id="details" class="text ui-widget-content ui-corner-all"></textarea>
        </fieldset>
    </form>
</div>
';
}
$_result_tpl .= '

';
if (isset($this->switch['dialog_index'])) {
    $_result_tpl .= '
<div id="js_add_client" title="Ajouter un client">
    <form autocomplete="off">
        <fieldset>
            <p><label for="sexe">Sexe :</label>
                ' . ((isset($this->data['dialog']['vars']['LST_SEXE'])) ? $this->data['dialog']['vars']['LST_SEXE'] : $this->data['parent']['vars']['LST_SEXE']) . '</p>
            <p><label for="cli_nom">Nom :</label><input type="text" id="cli_nom"></p>
            <p><label for="cli_prenom">Prénom :</label><input type="text" id="cli_prenom"></p>
            <p><label for="cli_adr">Adresse :</label><input type="text" id="cli_adr"></p>
            <p><label for="cli_cp">Code postal :</label><input type="text" id="cli_cp"></p>
            <p><label for="cli_ville">Ville :</label><input type="text" id="cli_ville"></p>
            <p><label for="cli_tel">Téléphone :</label><input type="text" id="cli_tel"></p>
            <p><label for="cli_mobile">Portable :</label><input type="text" id="cli_mobile"></p>
            <p><label for="cli_email">E-mail :</label><input type="text" id="cli_email"></p>
            <p><label for="cli_naiss">Date de naissance :</label><input type="text" class="datepicker" id="cli_naiss"></p>
            <p><label for="cli_genre">Genre :</label>
                ' . ((isset($this->data['dialog']['vars']['LST_GEN'])) ? $this->data['dialog']['vars']['LST_GEN'] : $this->data['parent']['vars']['LST_GEN']) . '</p>
        </fieldset>
    </form>
</div>
';
}
$_result_tpl .= '

';
if (isset($this->switch['dialog_facturation'])) {
    $_result_tpl .= '
<div id="js_add_pres" title="Ajouter une prestation">
    <form>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody id="prestations">
            </tbody>
        </table>
    </form>
</div>
';
}
$_result_tpl .= '

';
if (isset($this->switch['dialog_get_waiting_fac'])) {
    $_result_tpl .= '
<div id="js_add_waiting_fac" title="Associer une facture">
    <form>
        <table>
            <thead>
                <tr>
                    <th>N° facture</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                </tr>
            </thead>
            <tbody id="factures">
            </tbody>
        </table>
    </form>
</div>
';
}
$_result_tpl .= '

';
?>
