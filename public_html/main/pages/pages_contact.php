<?php

/**
 * Affichage de la page d'index
 */
class Frame_child {

    public function __construct() {
        // On défini le template
        Globale::$Tpl->set_filenames(['pages_contact' => 'contact.tpl']);

		if( Fonction::check_variable(Http::request('send', 'post'))) {
			

			$mail = new Mail();
			$mail->IsMail();
			$mail->From = 'webmaster@e-grouptfe.com';
			$mail->FromName = "it.e-grouptfe.com";
			$mail->AddAddress(EMAIL_SITE);
			$mail->AddAddress($mail_ach[0]);
			$mail->AddReplyTo($email);
			$mail->WordWrap = 50;
			$mail->IsHTML(true);
			$mail->AddAttachment('./main/offres/offre_' . str_replace(' ', '', $compagny) . '_' . $tab_num_cmd[0] . '_' . date('d-m-Y-H-i') . '.csv');
			$mail->Subject = utf8_decode(Header::$lang->lang('new_commande'));
			$mail->Body = utf8_decode(sprintf(Header::$lang->lang('t_mail_new_commande'), $fname, $lname, $compagny));
			$result = $mail->send();

			Globale::$Tpl->create_block('is_posted');
		}

        // On parse le header
        Globale::$header->parse_header();

        // On parse le template
        Globale::$Tpl->pparse('pages_contact');

        // Affiche le pied de page
        Globale::$footer->afficheFooter();
    }

}
