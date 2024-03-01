# Dokuwiki CAS extension

The **CAS** extension uses :
- the [authsplit](https://www.dokuwiki.org/plugin:authsplit) extension : in order to combine the plugin authcas and authldap
- the [authldap](https://www.dokuwiki.org/plugin:authldap) extension : in order to have access to the acl of the wiki

to provide authentication using [Apereo CAS phpCAS](https://github.com/apereo/phpCAS).

Recommended Dokuwiki version: **54.1+** (Jack Jack Rum or possibly earlier)

## Requisites

- PHP 8.3+
- Dokuwiki 54.1 (Jack Jack Rum) or possibly earlier
- Extension authsplit (version of 2024-02-22) or possibly earlier
- Extension authldap (version of 2024-02-17) or possibly earlier

## Installation

> This extension requires :
- the [authsplit](https://www.dokuwiki.org/plugin:authsplit) extension
- the [authldap](https://www.dokuwiki.org/plugin:authldap) extension
to be installed first.

* Download the plugin from the extension manager of dokuwiki or manually place the file(s) in a directory called authcas in your lib/plugins/ folder.
* The plugin is provided with apereo/phpcas version 1.6.1 but if you want change the version you can modify composer.json and launch this command in the directory to install composer packages (dependencies of Cas package like apereo/phpcas) from composer.json and composer.lock :
```
composer install
```
or
```
composer update
```
* Here are the values that you can find in the config of your wiki in local.protected.php :

```php
$conf['superuser'] = 'mhetru';
$conf['superuser'] = $conf['superuser'].',@GROUP-ADMIN';
$conf['authtype']    = 'authsplit';
$conf['plugin']['authsplit']['primary_authplugin'] = 'authcas';
$conf['plugin']['authsplit']['secondary_authplugin'] = 'authldap';

$conf['plugin']['authcas']['handlelogoutrequestTrustedHosts'] = Array("");
$conf['plugin']['authcas']['server'] = 'cas.univ.fr';
$conf['plugin']['authcas']['port'] = 443;
// CAS server root parameter
$conf['plugin']['authcas']['rootcas'] = '';
$conf['plugin']['authcas']['hostURL'] = 'https://dokuwiki.host.com';
// automatically log the user when there is already a CAS session opened
$conf['plugin']['authcas']['autologin'] = 1;
$conf['plugin']['authcas']['caslogout'] = 1;
// log out from wiki when loggin out from CAS(should work with CAS V3, experimental)
$conf['plugin']['authcas']['handlelogoutrequest'] = 1;
// force cas connection (set to false if you want an anonymous page on your wiki)
$conf['plugin']['authcas']['forceauthentication'] = 'false';

$conf['plugin']['authcas']['logFile']="";

$conf['plugin']['authldap']['binddn']     = 'uid=app-user,ou=ldapusers,dc=univ,dc=fr';
$conf['plugin']['authldap']['bindpw']     = '***********';
$conf['plugin']['authldap']['server']      = 'ldap://ldap.univ.fr:389'; #instead of the above two settings
$conf['plugin']['authldap']['usertree']    = 'ou=people,dc=univ,dc=fr';
$conf['plugin']['authldap']['grouptree']   = 'ou=groups,dc=univ,dc=fr';
$conf['plugin']['authldap']['userfilter']  = '(uid=%{user})';
$conf['plugin']['authldap']['groupfilter'] = '(member=uid=%{user},ou=people,dc=univ,dc=fr)';
$conf['plugin']['authldap']['version']    = 3;
```


## Reload config of your wiki
```
touch local.protected.php
```

