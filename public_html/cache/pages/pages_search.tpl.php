<?php
$_result_tpl .= '<form action="#" method="post">
    <p class="t_center">
        Recherche : <input id="filter" type="text" class="mrs">
        <a href="#clear" class="clear-filter" title="clear filter">Effacer</a>
    </p>
</form>
';
if ( isset ($this->switch['in_search']))
{
$_result_tpl .=  '
<div class="scrollable">
    <table class="footable mts" data-filter="#filter" data-filter-text-only="true">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Ville</th>
                <th>Genre</th>
            </tr>
        </thead>
        <tbody>
            ';
$count_i_0 =  count($this->data['pages_search']['blocks']['lst_cli']);
for($i_0 = 0; $i_0 < $count_i_0; $i_0++)
{
$_result_tpl .=  '            <tr onclick="window.location.href = \'index.php?p=' . ((isset($this->data['pages_search']['vars']['PAGE'])) ? $this->data['pages_search']['vars']['PAGE'] : $this->data['parent']['vars']['PAGE'])  . '&amp;cli=' . $this->data['pages_search']['blocks']['lst_cli'][$i_0]['CLI_NUM'] . '\'">
                <td>' . $this->data['pages_search']['blocks']['lst_cli'][$i_0]['CLI_NOM'] . '</td>
                <td>' . $this->data['pages_search']['blocks']['lst_cli'][$i_0]['CLI_PNOM'] . '</td>
                <td>' . $this->data['pages_search']['blocks']['lst_cli'][$i_0]['CLI_VILLE'] . '</td>
                <td>' . $this->data['pages_search']['blocks']['lst_cli'][$i_0]['CLI_SEXE'] . '</td>
            </tr>
            ';
}
$_result_tpl .=  '        </tbody>
    </table>
</div>
';
}
$_result_tpl .=  '
';
?>