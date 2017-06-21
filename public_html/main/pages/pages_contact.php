<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {
        // On défini le template
        Globale::$Tpl->set_filenames(['pages_contact' => 'contact.tpl']);

        if( Fonction::check_variable( Http::request('send', 'post'))) {

            $filter_def = [
                'nom' => FILTER_SANITIZE_STRING, // Pas d'option ni de flag, on peut préciser directement le filtre
                'prenom' => FILTER_UNSAFE_RAW, // Filtre "on ne change rien". Sert juste à avoir l'entrée "passwd" dans le résultat.
                'email' => [
                    'filter' => FILTER_VALIDATE_EMAIL,
                    'flags' => FILTER_NULL_ON_FAILURE
                ],
                'tel' => FILTER_UNSAFE_RAW, // Pas besoin de valider l'email2, on va juste le comparer à email.
                'message' => FILTER_SANITIZE_STRING
            ];

            $res_x = filter_input_array( INPUT_POST, $filter_def);

            if( isset($_FILES)) {
                echo '<pre>';
                print_r( $_FILES);
                echo '</pre>';
                $up= new Upload();
                $up->upload();
            }

            $mail = new Mail();
            $mail->IsMail();
            $mail->From = 'webmaster@elixir-coiffure.com';
            $mail->FromName = "elixir-coiffure.com";
            $mail->AddAddress(EMAIL_SITE);
            $mail->AddReplyTo( $res_x[ 'email']);
            $mail->WordWrap = 50;
            $mail->IsHTML(true);
            $mail->AddAttachment('./upload/' . $_FILES[ 'piece_jointe'][ 'name']);
            $mail->Subject = utf8_decode( 'Nouvelle question sur le site Elixir-Coiffure');
            $msg= 'Bonjour,<br><br>Vous avez reçu une nouvelle question prevenant du site Elixir-Coiffure<br><br>'
                . 'Nom : ' . $res_x[ 'nom'] . '<br>'
                . 'Prénom : ' . $res_x[ 'prenom'] . '<br>'
                . 'Email : ' . $res_x[ 'email'] . '<br>'
                . 'Téléphone : ' . $res_x[ 'tel'] . '<br>'
                . 'Message : ' . $res_x [ 'message'] . '<br>';
            $mail->Body = utf8_decode( $msg);
            $result = $mail->send();

            if( $result) {
                Globale::$Tpl->create_block('is_posted');
            }
        } else {
            Globale::$Tpl->create_block('form');
        }

        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_contact');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
