<?php

/**
 * DokuWiki Plugin authcas (Auth Component)
 *
 * Intercepts the 'login' action and redirects the user to the Cas server login page
 * instead of showing the login form.
 *
 * @author  Mathieu Hetru <mathieu.hetru@univ-lille.fr>
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @link https://github.com/l3-team/dokuwiki-extensions-authcas
 */

/**
 * Inspired from
 * http://www.esup-portail.org/display/PROJDOCUWIKICAS/CASification+de+Docuwiki;jsessionid=58187C0F5A8834D07E6D7F1EB30744C2
 */
/**
 * Adapted by Benjamin BERNARD (Maison du libre Brest) <benvii at mdl29.net>, http://mdl29.net/
 * Adapted by David Darras (Université Lille 1) <david.darras at univ-lille1.fr>
 * Adapted by Mathieu Hétru (Université Lille) <mathieu.hetru at univ-lille.fr>
 *
 * In this implementation :
 * - some bots/crawlers/readers can fetch dokuwiki pages without being redirected the CAS
 *     Thanks for adding user-agents to the pattern and reporting it to me <benvii at mdl29.net> and/or to http://www.dokuwiki.org/auth:cas
 * - debugging mode, simply put the log file location ($conf['plugin']['authcas']['logFile']) to enable logs, check acces rights
 * - Trusted CAS hosts : to handle CAS logout request you need to precis a list of trusted cas hosts like this :
 *     $conf['plugin']['authcas']['handlelogoutrequestTrustedHosts'] = Array("cas.mdl29.net", "cas2.mdl29.net");
 */
//include_once(DOKU_INC . 'lib/plugins/authcas/CAS-1.3.8/CAS.php');
include_once(DOKU_INC . 'lib/plugins/authcas/vendor/autoload.php');

class auth_plugin_authcas extends DokuWiki_Auth_Plugin {

    function __construct() {
        parent::__construct();

        $this->cando['external'] = (preg_match("#(bot)|(slurp)|(netvibes)#i", $_SERVER['HTTP_USER_AGENT'])) ? false : true; //Disable CAS redirection for bots/crawlers/readers
        $this->cando['login'] = true;
        $this->cando['logout'] = true;

        $logfile = $this->getConf("logFile");
        if (!empty($logfile)) {
            //phpCAS::setDebug($this->getConf("logFile")); // for phpcas 1.3.8
            \phpCAS::setLogger($this->getConf("logFile"));
        } //If $conf['plugin']['authcas']['logFile'] exist we start phpCAS in debug mode
        else \phpCAS::setLogger();

        \phpCAS::setVerbose(false);

        //Note the last argument true, to allow phpCAS to change the session_id so he will be able to destroy the session after a CAS logout request - Enable Single Sign Out
        // curl extension is needed
        //phpCAS::client(CAS_VERSION_2_0, $this->getConf('server'), (int) $this->getConf('port'), $this->getConf('rootcas'), true); // for phpcas 1.3.8
        \phpCAS::client(CAS_VERSION_2_0, $this->getConf('server'), (int) $this->getConf('port'), $this->getConf('rootcas'), $this->getConf('hostURL'), true);

        if (!function_exists('curl_init')) {
            if ($this->getConf('debug'))
                msg("CAS err: CURL extension not found.", -1, __LINE__, __FILE__);
            $this->success = false;
            return;
        }

        // automatically log the user when there is a cas session opened
        if ($this->getConf('autologin')) {
            \phpCAS::setCacheTimesForAuthRecheck(1);
        } else {
            \phpCAS::setCacheTimesForAuthRecheck(-1);
        }

        if ($this->getConf('cert')) {
            \phpCAS::setCasServerCert($this->getConf('cert'));
        } elseif ($this->getConf('cacert')) {
            \phpCAS::setCasServerCACert($this->getConf('cacert'));
        } else {
            \phpCAS::setNoCasServerValidation();
        }

        if ($this->getConf('handlelogoutrequest')) {
            \phpCAS::handleLogoutRequests(true, $this->getConf('handlelogoutrequestTrustedHosts'));
        } else {
            \phpCAS::handleLogoutRequests(false);
        }
    }

    public function autoLogin() {

    }

    public function trustExternal($user, $pass, $sticky = false) {
        global $conf;
        //modif
        global $ACT;

        $sticky ? $sticky = true : $sticky = false; //sanity check
/*
        if ($ACT == 'logout')
                $this->logOff();
        */

        if ($this->getUserData($user)) {
            return true;
        }

        if ($this->getConf('forceauthentication') == 'true') {
           $this->logIn();
        }

        return false;
    }


    public function getUserData($user, $requireGroups = true) {
        //global $USERINFO;
        global $conf;

        $session = $_SESSION[$conf['title']]['auth'];
        if (\phpCAS::checkAuthentication()) {
            $user = \phpCAS::getUser();

            if (isset($session)) {
                $_SERVER['REMOTE_USER'] = $user;
                $userinfo = $session['info'];
                $_SESSION[$conf['title']]['auth']['user'] = $user;
                $_SESSION[$conf['title']]['auth']['pass'] = $session['pass'];
                $_SESSION[$conf['title']]['auth']['info'] = $userinfo;
                $_SESSION[$conf['title']]['auth']['buid'] = $session['buid'];
            } else {
                $_SERVER['REMOTE_USER'] = $user;
                $_SESSION[$conf['title']]['auth']['user'] = $user;
                $_SESSION[$conf['title']]['auth']['pass'] = $pass;
                //$_SESSION[$conf['title']]['auth']['info'] = $USERINFO;
                $_SESSION[$conf['title']]['auth']['buid'] = auth_browseruid();
            }

            $attributes = \phpCAS::getAttributes();
            foreach($attributes as $key=>$val) {
                $userinfo[$key] = \phpCAS::getAttribute($key);
            }

            return $userinfo;
        }
        return false;
    }

    public function logIn() {
        global $QUERY;
        //$login_url = DOKU_URL . 'doku.php?id=' . $QUERY;
        $login_url = $this->getCurrentPageURL();
        // \phpCAS::setFixedServiceURL($login_url); // disabled because can't run in many cases
        \phpCAS::forceAuthentication();
    }

    public function logOff() {
        global $QUERY;
        if ($this->getConf('caslogout')) { // dokuwiki + cas logout
            //dbglog(session_id());

            @session_start();
            //session_destroy();
            $logout_url = DOKU_URL;
            //$logout_url = DOKU_URL . 'doku.php?id=' . $QUERY;
            \phpCAS::logoutWithRedirectService($logout_url);
        } else { // dokuwiki logout only
            @session_start();
            session_destroy();
        }
    }

    public function getLoginURL() {
        return \phpCAS::getServerLoginURL();
    }

    public function getCurrentPageURL() {
        $pageURL = 'http';
        if ( (isset($_SERVER["HTTP_HTTPS"])) && ($_SERVER["HTTP_HTTPS"] == "on") ) { // for reverse proxy / front proxy
            $pageURL .= "s";
        } else if ( (isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on") ) { // for backend / local server
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

}
