<?php

/*
 * Lang: Français
 * Author: MTC
 * CMS Version: 0.6.0
 *
 */

$langname = 'Français';

$lang = array();

// General
$lang += array(
  'SEARCH_USER' => 'Rechercher un membre',
  'PREVIOUS' => 'Précédent',
  'NEXT' => 'Suivant',
  'PAGE_PREVIOUS' => 'Page précédente',
  'PAGE_NEXT' => 'Page suivante',
  'BACK_TO_TOP' => 'Retour en haut',
  'RESET_YOUR_PASSWORD' => 'Réinitialiser votre mot de passe',
  'CONTACT_US' => 'Nous contacter:',
  'LOADING' => 'Chargement..',
  'IGNORE' => 'Ignorer',
  'CLOSE' => 'Fermer',
  'BACK' => 'Retour',
  'MAN' => 'Homme',
  'WOMAN' => 'Femme',
  'OR' => 'ou',
  'BY' => 'par',
  'AT' => 'à',
);

// Specific
$lang += array(
  'GROUP_0' => 'Visiteur',
  'GROUP_1' => 'Membre',
  'GROUP_2' => 'VIP',
  'GROUP_3' => 'Modérateur',
  'GROUP_4' => 'Administrateur',
  'GROUP_9' => 'Fondateur',
  'PLUGIN' => 'Plugin',
);

// Error
$lang += array(
  'ERROR' => 'Erreur',
  'UNKNOW_ERROR' => 'Une erreur est survenue..',
  'INVALID_URL' => 'Lien invalide',
  'INVALID_TOKEN' => 'Une erreur de sécurité s\'est produite',
  'NO_PERMISSION' => 'Vous n\'avez pas la permission',
  'PAGE_403' => 'Accès refusé',
  'PAGE_404' => 'Page introuvable',
  'PAGE_500' => 'Une erreur est survenue',
  'PAGE_403_DESC' => 'Vous n\'avez pas la permission d\'accéder à cette page.',
  'PAGE_404_DESC' => 'La page que vous cherchez n\'existe pas, ou plus, ou a peut-être été déplacée.',
  'PAGE_500_DESC' => 'Une erreur s\'est produite lors de l\'affichage de la page...',
  'FATAL_ERROR' => 'Erreur fatale',
  'ERROR_MISSING_FILE' => 'Fichier manquant: %file%.',
  'ERROR_MISSING_PLUGIN' => 'Plugin manquant: %plugin%.',
  'ERROR_PLUGIN_VERSION' => 'Le plugin suivant n\'est pas compatible avec votre version du CMS: ',
  'ERROR_PLUGIN_NOT_FOUND' => 'Le plugin suivant n\'a pas été trouvé: ',
  'ERROR_PLUGIN_DEPENDENCIES' => 'Des plugins sont manquants pour que le plugin suivant fonctionne: ',
  'ERROR_EMPTY_PLUGIN' => 'Le plugin ne semble pas fonctionner..',
  'ERROR_NO_PLUGIN' => 'Aucun plugin défini pour cette page.',
  'ERROR_CANNOT_LOAD_PAGE' => 'Impossible de charger la page demandée..',
  'ERROR_SQLITE_MISSING' => 'SQlite n\'est pas implémenté sur votre hébergeur Web',
  'ERROR_SQLITE_INCORRECT' => 'Impossible d\'utiliser la base de données SQLite',
  'ERROR_SQLITE_MISSING_INSTALL_FILE' => 'Les fichiers d\'installation de SQLite sont manquants',
  'ERROR_SQLITE_DATABASE' => 'Impossible de se connecter à la base de données SQLite',
  'ERROR_MYSQL_MISSING' => 'MySQL n\'est pas implémenté sur votre hébergeur Web',
  'ERROR_MYSQL_INCORRECT' => 'Impossible d\'utiliser la base de données MySQL',
  'ERROR_MYSQL_MISSING_INSTALL_FILE' => 'Les fichiers d\'installation de SQLite sont manquants',
  'ERROR_MYSQL_DATABASE' => 'Impossible de se connecter à la base de données MySQL',
  'ERROR_CONFIG_DATABASE' => 'La base de données est mal configurée',
);

// Success
$lang += array(
    'MODIFICATION_SUCCESSFUL' => 'Les modifications ont été enregistrées !',
    'LOGIN_SUCCESSFUL' => 'Connexion réussie !',
    'LOGOUT_SUCCESSFUL' => 'Déconnexion réussie !',
    'REGISTRATION_SUCCESSFUL' => 'Votre inscription est terminée !',
    'RECOVERY_SUCCESSFUL' => 'Les instructions vous ont été envoyées par mail',
);

// Buttons
$lang += array(
  'BTN_SEARCH' => 'Rechercher',
  'BTN_CANCEL' => 'Annuler',
  'BTN_REGISTER' => 'S\'inscrire',
  'BTN_LOGIN' => 'Se connecter',
  'BTN_REMEMBER' => 'Rester connecté',
  'BTN_RESET' => 'Réinitialiser le mot de passe',
  'BTN_CHANGE_AVATAR' => 'Changer mon image de profil',
  'BTN_ADD' => 'Ajouter',
  'BTN_EDIT' => 'Modifier',
  'BTN_REMOVE' => 'Supprimer',
  'BTN_CLOSE' => 'Fermer',
  'BTN_SAVE' => 'Enregistrer',
  'BTN_CHOSE' => 'Choisir',
  'BTN_OPTIONS' => 'Options',
  'BTN_OK' => 'Valider',
  'BTN_NO' => 'Non',
  'BTN_YES' => 'Oui',
  'BTN_I_REMEMBER' => 'Finalement je m\'en souviens !',
  'BTN_FORGOT_PASSWORD' => 'Mot de passe oublié ?'
);

// Text editor
$lang += array(
  'TE_BOLD' => 'Gras',
  'TE_ITALIC' => 'Italique',
  'TE_UNDERLINE' => 'Souligné',
  'TE_BIGTITLE' => 'Titre principal',
  'TE_TITLE' => 'Titre',
  'TE_SUBTITLE' => 'Sous-titre',
  'TE_LINK' => 'Lien',
  'TE_PICTURE' => 'Image',
  'TE_VIDEO' => 'Vidéo Youtube',
  'TE_VIDEO_DESC' => 'Insère juste le code de la video.\nExemple:\n\n[youtube]9d8wWcJLnFI[/youtube]',
  'TE_ALIGN_LEFT' => 'Aligner a gauche',
  'TE_ALIGN_CENTER' => 'Aligner au centre',
  'TE_ALIGN_RIGHT' => 'Aligner a droite',
  'TE_ALIGN_JUSTIFY' => 'Justifier',
  'TE_UNORDERED_LIST' => 'Liste (points)',
  'TE_ORDERED_LIST' => 'Liste (chiffres)',
  'TE_LIST_ELEMENT' => 'Element de liste',
  'TE_RENDER' => 'Prévisualiser',
  'TE_MORE' => 'Plus d\'options'
);

// Alerts
$lang += array(
  'ALERT_ERROR' => 'Une erreur est survenue..',
  'ALERT_SUCCESS' => 'Opération effectuée avec succès !',
  'USERNAME_EMPTY' => 'Vous n\'avez pas entré de nom d\'utilisateur',
  'PASSWORD_EMPTY' => 'Vous n\'avez pas entré de mot de passe',
  'EMAIL_EMPTY' => 'Vous n\'avez pas entré d\'adresse email',
  'NO_ACCOUNT_MATCHING' => 'Ce nom d\'utilisateur n\'existe pas.',
  'CONFIRM_EMAIL_EDIT_USERNAME' => 'Vous devez confirmer votre adresse email pour modifier votre nom d\'utilisateur',
  'CONFIRM_EMAIL_EDIT_PASSWORD' => 'Vous devez confirmer votre adresse email pour modifier votre mot de passe',
  'RESEND_CONFIRMATION_EMAIL' => 'Renvoyer l\'email de confirmation',
  'PASSWORD_LENGHT' => 'Le mot de passe doit faire entre 5 et 32 caractères',
  'USERNAME_LENGHT' => 'Le nom d\'utilisateur doit faire entre 3 et 16 caractères',
  'EMAIL_INCORRECT' => 'L\'adresse email est incorrecte',
  'EMAIL_UNKNOW' => 'L\'adresse email est inconnue',
  'PASSWORD_NOT_THE_SAME' => 'Les mots de passe sont différents',
  'USERNAME_INCORRECT' => 'Le nom d\'utilisateur doit être alphanumérique',
  'ALREADY_USERNAME' => 'Ce nom d\'utilisateur est déjà utilisé',
  'ALREADY_EMAIL' => 'Cette adresse email est déjà utilisée',
  'EMAIL_NOT_THE_SAME' => 'Les adresses emails sont différentes',
  'USER_UNKNOWN' => 'Ce membre n\'existe pas',
  'PASSWORD_INCORRECT' => 'Mot de passe incorrect',
  'PLEASE_CONFIRM_MAIL' => 'Votre inscription est terminée, vous devez désormais confirmer votre adresse email !',
  'PLEASE_CONFIRM_NEW_MAIL' => 'Vous devez désormais confirmer votre adresse email !',
  'AVATAR' => 'Image de profil',
  'PROFIL_OF' => 'Profil de',
  'PICTURE_FILE_SIZE' => 'Votre image est trop volumineuse, maximum: 100 Ko',
  'PICTURE_SIZE' => 'Votre image est trop grande, maximum: 300x300',
  'WRONG_PICTURE_FORMAT' => 'Votre image doit être en format png, jpg ou jpeg',
  'NOT_A_PICTURE' => 'L\'image envoyée est incorrecte',
  'PICTURE_DOESNT_EXIST' => 'L\'image n\'existe pas'
);

// Date
$lang += array(
  'Monday'    => 'Lundi',
  'Tuesday'   => 'Mardi',
  'Wednesday' => 'Mercredi',
  'Thursday'  => 'Jeudi',
  'Friday'    => 'Vendredi',
  'Saturday'  => 'Samedi',
  'Sunday'    => 'Dimanche',
  'January'   => 'Janvier',
  'February'  => 'Février',
  'March'     => 'Mars',
  'April'     => 'Avril',
  'May'       => 'Mai',
  'June'      => 'Juin',
  'July'      => 'Juillet',
  'August'    => 'Août',
  'September' => 'Septembre',
  'October'   => 'Octobre',
  'November'  => 'November',
  'December'  => 'Décembre',
  'Mo' => 'Lu',
  'Tu' => 'Ma',
  'We' => 'Me',
  'Th' => 'Je',
  'Fr' => 'Ve',
  'Sa' => 'Sa',
  'Su' => 'Di',
  'Jan' => 'Jan',
  'Feb' => 'Fév',
  'Mar' => 'Mar',
  'Apr' => 'Avr',
  'May' => 'Mai',
  'Jun' => 'Jui',
  'Jul' => 'Jui',
  'Aug' => 'Aoû',
  'Sep' => 'Sep',
  'Oct' => 'Oct',
  'Nov' => 'Nov',
  'Dec' => 'Déc',
  'LAST_7_DAYS' => '7 derniers jours',
  'LAST_30_DAYS' => '30 derniers jours',
  'IN_THE_FUTURE' => 'Dans le futur',
  'LESS_THAN_A_MINUTE' => 'moins d\'une minute',
  'RELATIVE_DATE_PREFIX' => 'Il y a ',
  'RELATIVE_DATE_SUFFIX' => '',
  'SECOND' => 'seconde',
  'MINUTE' => 'minute',
  'HOUR' => 'heure',
  'DAY' => 'jour',
  'WEEK' => 'semaine',
  'MONTH' => 'mois',
  'YEARS' => 'ans',
  'SECONDS' => 'secondes',
  'MINUTES' => 'minutes',
  'HOURS' => 'heures',
  'DAYS' => 'jours',
  'WEEKS' => 'semaines',
  'MONTHS' => 'mois',
  'YEARS' => 'ans'
);

// Email
$lang += array(
  'EMAIL_RESET_SUBJECT' => 'Réinitialiser votre mot de passe',
  'EMAIL_RESET_INSTRUCTIONS' => 'Vous avez oublié votre mot de passe ?\n\nSi c\'est effectivement le cas, cliquez sur le lien suivant pour réinitialiser votre mot de passe, ou copiez le lien dans votre navigateur:\n%link%\n\nSi vous n\'avez pas demandé la réinitialisation de votre mot de passe, vous pouvez ignorer cet e-mail.',
  'EMAIL_NEW_PASSWORD_SUBJECT' => 'Nouveau mot de passe',
  'EMAIL_NEW_PASSWORD_INSTRUCTIONS' => 'Viens sur: %link%\n\nTon mdp: %password%',
  'EMAIL_CONFIRMATION_SUBJECT' => 'Confirme ton mail',
  'EMAIL_CONFIRMATION_INSTRUCTIONS' => 'Clic: %link%'
);

// Forms
$lang += array(
  'STAY_LOGGED_IN' => 'Rester connecté',
  'RESET_PASSWORD' => 'Réinitialiser le mot de passe',
  'NEW_PASSWORD' => 'Nouveau mot de passe',
  'NEW_PASSWORD_SENDED' => 'Un nouveau mot de passe vous a été envoyé',
  'TOO_RECENT_RECOVERY' => 'Un email vous a déjà été envoyé il y a moins de 5 minutes',
  'MAIL_CONFIRMED' => 'Ton email a bien été vérifié',
  'CHANGE_PASSWORD' => 'Changer mon mot de passe',
  'CHANGE_AVATAR' => 'Changer mon image de profil',
  'UPDATE_PROFIL' => 'Mettre à jour mon profil',
  'PROFIL' => 'Profil',
  'CONFIRM_PASSWORD' => 'Confirmer mon mot de passe',
  'OLD_PASSWORD' => 'Ancien mot de passe',
  'CHANGE_EMAIL' => 'Changer mon adresse email',
  'CONFIRM_EMAIL' => 'Confirmer mon adresse email',
  'NEW_EMAIL' => 'Nouvelle adresse email',
  'INFO_CHANGE_EMAIL' => 'Vous devrez peut-être confirmer votre nouvelle adresse email',
  'INFO_UPDATE_PROFIL' => 'Remplissez uniquement les champs que vous voulez mettre à jour',
  'OLD_PASSWORD_INCORRECT' => 'Votre ancien mot de passe est incorrect',
  'VERIFY' => 'Vérification',
  'CAPTCHA' => 'Captcha',
  'INVALID_CAPTCHA' => 'Le captcha est invalide',
  'URL_PICTURE' => 'Url de l\'image',
  'TOO_MUCH_NEWLINE' => 'Il y a trop de retour à la ligne',
  'MAIL_NOT_CONFIRMED' => 'Vous devez confirmer votre adresse email'
);

// User
$lang += array(
  'USER' => 'Utilisateur',
  'USERNAME' => 'Nom d\'utilisateur',
  'PASSWORD' => 'Mot de passe',
  'UNKNOW' => 'Inconnu',
  'EMAIL' => 'Adresse email',
  'BIRTHDAY' => 'Date de naissance',
  'BIRTHDAY_SHORT' => 'Anniversaire',
  'BIRTHDAY_PLACEHOLDER' => 'AAAA/MM/JJ',
  'INCORRECT_BIRTHDAY' => 'La date de naissance est invalide',
  'INCORRECT_CITY' => 'La ville est invalide',
  'INCORRECT_COUNTRY' => 'Le pays est invalide',
  'GENDER' => 'Civilité',
  'COUNTRY' => 'Pays',
  'CITY' => 'Ville',
  'REGISTER_DATE' => 'Date d\'inscription',
  'REGISTER_DATE_SHORT' => 'Inscription',
  'NEED_CONNECTED_TO_COMMENT' => 'Vous devez être connecté pour poster un message',
  'ADD_STATUS' => 'Écrivez un message..',
  'SEND_STATUS' => 'Envoyer',
  'STATUS_ADDED' => 'Le status a été envoyé',
  'STATUS_REMOVED' => 'Le status a été supprimé',
  'UNKNOW_USER' => 'L\'utilisateur est inconnu',
  'TOO_RECENT_STATUS' => 'Vous avez déjà posté un message il y a moins d\'une minute !',
  'STATUS_LENGHT' => 'Le commentaire doit faire entre 10 et 500 caractères',
  'UNKNOW_STATUS' => 'Le status est inconnu',
  'USER_NEED_SEARCH' => 'Recherchez un membre ou connectez vous !',
  'ADD_COMMENT' => 'Ajouter un commentaire'
);
