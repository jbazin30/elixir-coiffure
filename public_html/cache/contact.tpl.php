<?php

$_result_tpl .= '</div>
</div>
<!-- Main -->
<div id="main-wrapper">
	<div class="container">
		<div class="grid">
			<div class="grid_2">
				<div class="t_center">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3603.5678319601793!2d3.4091420349182244!3d43.42636562426752!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0000000000000000%3A0x9723c92a89b0a7b2!2sElixir+Coiffure!5e1!3m2!1sfr!2sfr!4v1462697758449" width="400" height="300" frameborder="0" class="border" allowfullscreen></iframe>
				</div>
				<div>
					<p>
						<span class="t_bold">Elixir Coiffure</span><br>
						32 Avenue de Pézenas<br>
						34120 - Nézignan L\'Evêque<br>
						Tél. : 04 99 41 14 66<br>
					</p>
					<p>
						Mardi 9 h 00 - 12 h 00<br>
						Mercredi au vendredi 9 h 00 - 12 h 00 / 13 h 30 - 18 h 00<br>
						Samedi 9 h 00 - 12 h 00 / 13 h 30 - 17 h 00<br>
					</p>
					<p class="t_bold">
						Sur rendez-vous en dehors de ces horaires
					</p>
				</div>
			</div>
		</div>
		<div class="mtl">
			<!-- revoir largeur formulaire -->
			<form name="contact" action="contact.html" method="POST" class="w70 center">
				<p><label>Nom : </label><input type="text" name="nom" value="" placeholder="Nom" /></p>
				<p><label>Prénom : </label><input type="text" name="prenom" value="" placeholder="Prénom" /></p>
				<p><label>E-mail : </label><input type="text" name="email" value="" placeholder="Email" /></p>
				<p><label>Téléphone : </label><input type="text" name="tel" value="" placeholder="Téléphone" /></p>
				<p><label>Pièce jointe : </label><input type="file" name="piece_jointe" /></p><!-- permettre l\'ajout de X fichiers -->
				<p><label>Message : </label><textarea name="message" rows="4" cols="20"></textarea></p>
				<p><input type="submit" value="Envoyer" name="send" /></p>
			</form>
		</div>
	</div>
</div>

';
?>
