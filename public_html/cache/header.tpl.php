<?php
$_result_tpl .= '<!DOCTYPE html >
<html>
    <head>
        <title>' . ((isset($this->data['header']['vars']['TITLE'])) ? $this->data['header']['vars']['TITLE'] : $this->data['parent']['vars']['TITLE'])  . '</title>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="./templates/images/favicon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link href="./templates/knacss.css" rel="stylesheet" type="text/css">
        <link href="./main/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
        <link href="./templates/footable.core.min.css" rel="stylesheet" type="text/css">
        <!--<link href="./templates/footable.metro.min.css" rel="stylesheet" type="text/css">-->
        <script src="./main/js/jquery-ui/external/jquery/jquery.js"></script>
        <script src="./main/js/jquery-ui/jquery-ui.min.js"></script>
        <script src="./main/js/function.js"></script>
        <script src="./main/js/dialog.js"></script>
        <script src="./main/js/ajax.js"></script>
        <script src="./main/js/jquery-ui/datepicker-fr.js"></script>
        <script src="./main/js/footable/footable.js"></script>
        <script src="./main/js/footable/footable.filter.js"></script>
        <!--<script src="./main/js/footable/footable.paginate.js"></script>-->
        <script src="./main/js/footable/footable.sort.js"></script>
        <script>
            // <![CDATA[
            $(function () {

            });
            // ]]>
        </script>
        ' . ((isset($this->data['header']['vars']['META'])) ? $this->data['header']['vars']['META'] : $this->data['parent']['vars']['META'])  . '
    </head>
    <body>
        <header>
            <img src="./templates/images/logo_elixir_blanc.png"><br>
            <p class="pt5">' . ((isset($this->data['header']['vars']['DATE'])) ? $this->data['header']['vars']['DATE'] : $this->data['parent']['vars']['DATE'])  . '</p>
        </header>
        <main>

';
?>