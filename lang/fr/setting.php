<?php
/**
 * French language file
 */

$lang['casserver']             = 'Votre serveur CAS';
$lang['casport']               = 'Port du serveur CAS si l\'URL complète n\'a pas été indiquée ci-dessus';
$lang['rootcas']               = 'Racine du contexte CAS s\'il y en a un';
$lang['hostURL']               = 'URL de votre serveur wiki';
$lang['autologin']             = 'Identifie automatiquement l\'utilisateur si une session CAS est déjà ouverte';
$lang['handlelogoutrequest']   = 'déconnexion du wiki quand déconnexion du CAS (fonctionne en CAS V3)';
$lang['forceauthentication']   = 'Force l\'authentification sur le wiki, pas d\'utilisateurs anonymes';
$lang['server']                = 'Votre serveur LDAP. Soit le nom d\'hôte (<code>localhost</code>) ou l\'URL complète (<code>ldap://serveur.dom:389</code>)';
$lang['port']                  = 'Port du serveur LDAP si l\'URL complète n\'a pas été indiquée ci-dessus';
$lang['usertree']              = 'Où trouver les comptes utilisateur. Ex.: <code>ou=Utilisateurs, dc=serveur, dc=dom</code>';
$lang['grouptree']             = 'Où trouver les groupes d\'utilisateurs. Ex.: <code>ou=Groupes, dc=serveur, dc=dom</code>';
$lang['userfilter']            = 'Filtre LDAP pour rechercher les comptes utilisateur. Ex.: <code>(&amp;(uid=%{user})(objectClass=posixAccount))</code>';
$lang['groupfilter']           = 'Filtre LDAP pour rechercher les groupes. Ex.: <code>(&amp;(objectClass=posixGroup)(|(gidNumber=%{gid})(memberUID=%{user})))</code>';
$lang['version']               = 'La version de protocole à utiliser. Il se peut que vous deviez utiliser <code>3</code>';
$lang['starttls']              = 'Utiliser les connexions TLS?';
$lang['referrals']             = 'Suivre les références?';
$lang['binddn']                = 'Nom de domaine d\'un utilisateur de connexion facultatif si une connexion anonyme n\'est pas suffisante. Ex. : <code>cn=admin, dc=mon, dc=accueil</code>';
$lang['bindpw']                = 'Mot de passe de l\'utilisateur ci-dessus.';
$lang['userscope']             = 'Limiter la portée de recherche d\'utilisateurs';
$lang['groupscope']            = 'Limiter la portée de recherche de groupes';
$lang['groupkey']              = 'Affiliation aux groupes à partir de n\'importe quel attribut utilisateur (au lieu des groupes AD standards), p. ex. groupes par département ou numéro de téléphone';
$lang['debug']                 = 'Afficher des informations de bégogage supplémentaires pour les erreurs';
