<?php

$_result_tpl .= '<button onclick="window.location.href = \'?p=client&cli=' . ((isset($this->data['pages_facture']['vars']['CLI_NUM'])) ? $this->data['pages_facture']['vars']['CLI_NUM'] : $this->data['parent']['vars']['CLI_NUM']) . '\'">Retour</button>
<div class="scrollable">
    <table class="footable mts">
        <thead>
            <tr>
                <th>Préstations</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            ';
$count_i_0 = count($this->data['pages_facture']['blocks']['lst_fac']);
for ($i_0 = 0; $i_0 < $count_i_0; $i_0++) {
    $_result_tpl .= '            <tr>
                <td>' . $this->data['pages_facture']['blocks']['lst_fac'][$i_0]['PRES_LIBEL'] . '</td>
                <td>' . $this->data['pages_facture']['blocks']['lst_fac'][$i_0]['FAC_PRICE_APPLIED'] . '</td>
            </tr>
            ';
}
$_result_tpl .= '        </tbody>
    </table>
</div>
';
?>