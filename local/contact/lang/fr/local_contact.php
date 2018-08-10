<?php
// This file is part of the Contact Form plugin for Moodle - http://moodle.org/
//
// Contact Form is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Contact Form is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Contact Form.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin for Moodle is used to send emails through a web form.
 *
 * @package    local_contact
 * @copyright  2016-2018 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Formulaire de contact';
$string['privacy:metadata'] = 'Le plugiciel Formulaire de contact ne conserve aucune donnée personnelle sur les utilisateurs.';
$string['globalhelp'] = 'Formulaire de contact est un plugiciel pour Moodle qui permet à votre site traiter les informations soumis par le biais de formulaires Web HTML à l\'adresse courriel de soutien du site.';
$string['configure'] = 'Configurer ce plugiciel';

$string['field-name'] = 'nom';
$string['field-email'] = 'courriel';
$string['field-subject'] = 'objet';
$string['field-message'] = 'message';

$string['confirmationmessage'] = 'Merci de nous contacter. Si nécessaire, nous serons en contact avec vous très bientôt.';
$string['confirmationsent'] = 'Un courriel a été envoyé à votre adresse à {$a}.';
$string['forbidden'] = 'Interdit';
$string['errorsendingtitle'] = 'Envoi de courriel échoué';
$string['errorsending'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez essayez de nouveau plus tard.';

$string['defaultsubject'] = 'Nouveau message';
$string['extrainfo'] = '<hr>
<p><strong>Informations supplémentaires de l\'utilisateur</strong></p>
<ul>
    <li><strong>Utilisateur du site&nbsp;:</strong> [userstatus]</li>
    <li><strong>Langue préférée&nbsp;:</strong> [lang]</li>
    <li><strong>De l\'adresse IP&nbsp;:</strong> [userip]</li>
    <li><strong>Navigateur Web&nbsp;:</strong> [http_user_agent]</li>
    <li><strong>Formulaire Web&nbsp;:</strong> <a href="[http_referer]">[http_referer]</a></li>
</ul>
';
$string['confirmationemail'] = '
<p>[fromname],</p>
<p>Merci de nous avoid contacter. Si nécessaire, nous serons en contact avec vous très bientôt.</p>
<p>Cordialement,</p>
<p>[supportname]<br>
[sitefullname]<br>
<a href="[siteurl]">[siteurl]</a></p>
';
$string['lockedout'] = 'VERROUILLÉ';
$string['notconfirmed'] = 'PAS CONFIRMÉ';

$string['recipient_list'] = 'List of available recipients';
$string['recipient_list_description'] = 'Vous pouvez configurer une liste de destinataires potentiels, dont chacun peut être utilisé dans un formulaire de contact pour spécifier le destinataire du courriel en utilisant un champ de texte caché, ou dans une liste déroulante pour permettre aux utilisateurs de sélectionner le destinataire sans divulguer l’adresse de courriel du  destinataire. Si la liste est vide, le courriel sera envoyé à une adresse configuré dans Moodle, soit l\'adresse courriel de soutien ou de l\'administrateur principal de Moodle.
Chaque ligne doit être composée d\'un alias unique, d\'une adresse courriel unique et d\'un nom, séparés par des barres verticales. Par exemple :
<pre>
soutient technique|soutient@exemple.com|Joe Fixit
webmaster|admin@exemple.com|Mr. Moodle
électrique|nikola.tesla@exemple.com|Nikola
histoire|charles.darwin@exemple.com|Mr Darwin
loi|issac.newton@exemple.com|Isaac Newton
mathématiques|galileo.galilei@exemple.com|Galileo
anglais|mark.twain@exemple.com|Mark Twain
physique|albert.einstein@exemple.com|Albert
science|thomas.edison@exemple.com|Mr Edison
philosophie|aristotle@exemple.com|Aristotle
</pre>';

$string['loginrequired'] = 'Connexion requise';
$string['loginrequired_description'] = 'Permettre seulement les utilisateurs connectés de soumettre les formulaires de contact. Les utilisateurs de compte invités ne sont pas considérés comme étant connectés.';

$string['nosubjectsitename'] = 'Nom du site dans l\'objet du courriel.';
$string['nosubjectsitename_description'] = 'N\'incluent pas le nom du site dans l\'objet du courriel.';

$string['recapchainfo'] = 'Utiliser ReCAPTCHA';
$string['recapchainfo_description'] = 'est présentement activé dans Moodle. Vous <strong>devez</strong>:<br>
<ul>
  <li>Assurez-vous que la balise {recaptcha} est incluse dans tous les formulaires traités par le formulaire de contact pour Moodle.</li>
  <li>Assurez-vous que le <a href="https://moodle.org/plugins/filter_filtercodes">plugiciel FilterCodes</a> est installé et activé.</li>
</ul>';

$string['norecaptcha'] = 'Aucun ReCAPTCHA';
$string['norecaptcha_description'] = 'N\'utilisez pas ReCAPTCHA avec des formulaires traités par le formulaire de contact.';
