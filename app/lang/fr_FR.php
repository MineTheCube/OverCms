<?php

$lang = array();

// General
$lang = array_merge($lang, array(
  'SEARCH_USER' => 'Rechercher un membre',
  'PREVIOUS' => 'Précédent',
  'NEXT' => 'Suivant',
  'PAGE_PREVIOUS' => 'Page précédente',
  'PAGE_NEXT' => 'Page suivante',
  'BACK_TO_TOP' => 'Retour en haut',
  'BIRTHDATE_PLACEHOLDER' => 'jj-mm-aaaa',
  'RESET_YOUR_PASSWORD' => 'Réinitialiser votre mot de passe',
  'OR' => 'ou',
  'BY' => 'par',
  'AT' => 'à'
));

// Error
$lang = array_merge($lang, array(
  'ERROR' => 'Erreur',
  'UNKNOW_ERROR' => 'Une erreur est survenue..',
  'INVALID_URL' => 'Lien invalide',
  'INVALID_TOKEN' => 'Une erreur de sécurité s\'est produite',
  'PAGE_NOT_FOUND' => 'Page inconnue',
  'FATAL_ERROR' => 'Erreur fatale',
  'ERROR_SQLITE_MISSING' => 'SQlite n\'est pas implémenté sur votre hébergeur Web.',
  'ERROR_SQLITE_INCORRECT' => 'Impossible de se connecter à la base de donnée.',
  'ERROR_MISSING_FILE' => 'Fichier manquant: %file%.',
  'ERROR_MISSING_PLUGIN' => 'Plugin manquant: %plugin%.'
));

// Buttons
$lang = array_merge($lang, array(
  'BTN_SEARCH' => 'Rechercher',
  'BTN_CANCEL' => 'Annuler',
  'BTN_REGISTER' => 'S\'inscrire',
  'BTN_LOGIN' => 'Se connecter',
  'BTN_REMEMBER' => 'Rester connecté',
  'BTN_RESET' => 'Réinitialiser le mot de passe',
  'BTN_CHANGE_AVATAR' => 'Changer mon image de profil',
  'BTN_EDIT' => 'Modifier',
  'BTN_REMOVE' => 'Supprimer',
  'BTN_CLOSE' => 'Fermer',
  'BTN_SAVE' => 'Enregistrer',
  'BTN_OPTIONS' => 'Options',
  'BTN_NO' => 'Non',
  'BTN_YES' => 'Oui',
  'BTN_I_REMEMBER' => 'Finalement je m\'en souviens !',
  'BTN_FORGOT_PASSWORD' => 'Mot de passe oublié ?'
));

// Text editor
$lang = array_merge($lang, array(
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
));

// Alerts
$lang = array_merge($lang, array(
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
  'USER_UNKNOWN' => 'Ce membre n\'existe pas',
  'PASSWORD_INCORRECT' => 'Mot de passe incorrect',
  'REGISTRATION_SUCCESSFUL' => 'Votre inscription est terminée !',
  'RECOVERY_SUCCESSFUL' => 'Les instructions vous ont été envoyées par mail',
  'MODIFICATION_SUCCESSFUL' => 'Les modifications ont été enregistrées !',
  'AVATAR' => 'Image de profil',
  'PROFIL_OF' => 'Profil de',
  'PICTURE_FILE_SIZE' => 'Votre image est trop volumineuse, maximum: 100 Ko',
  'PICTURE_SIZE' => 'Votre image est trop grande, maximum: 300x300',
  'WRONG_PICTURE_FORMAT' => 'Votre image doit être en format png, jpg ou jpeg',
  'NOT_A_PICTURE' => 'L\'image envoyée est incorrecte',
  'PICTURE_DOESNT_EXIST' => 'L\'image n\'existe pas'
));

// Date
$lang = array_merge($lang, array(
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
));

// Email
$lang = array_merge($lang, array(
  'EMAIL_RESET_SUBJECT' => 'Réinitialiser votre mot de passe',
  'EMAIL_RESET_INSTRUCTIONS' => 'Vous avez oublié votre mot de passe ?\n\nSi c\'est effectivement le cas, cliquez sur le lien suivant pour réinitialiser votre mot de passe, ou copiez le lien dans votre navigateur:\n%link%\n\nSi vous n\'avez pas demandé la réinitialisation de votre mot de passe, vous pouvez ignorer cet e-mail.',
  'EMAIL_RESET_INSTRUCTIONS_TXT' => 'Copiez/collez le lien dans votre navigateur pour réinitialiser votre mot de passe.'
));

// Forms
$lang = array_merge($lang, array(
  'STAY_LOGGED_IN' => 'Rester connecté',
  'RESET_PASSWORD' => 'Réinitialiser le mot de passe',
  'NEW_PASSWORD' => 'Nouveau mot de passe',
  'CHANGE_PASSWORD' => 'Changer mon mot de passe',
  'CHANGE_AVATAR' => 'Changer mon image de profil',
  'CONFIRM_PASSWORD' => 'Vérification',
  'URL_PICTURE' => 'Url de l\'image'
));

// User
$lang = array_merge($lang, array(
  'USERNAME' => 'Nom d\'utilisateur',
  'PASSWORD' => 'Mot de passe',
  'EMAIL' => 'Adresse email',
  'BIRTHDATE' => 'Date de naissance',
  'COUNTRY' => 'Pays',
  'CITY' => 'Ville',
  'REGISTER_DATE' => 'Date d\'inscription'
));
