<?php

/**
 * DokuWiki Plugin authcas (Action Component)
 *
 * Intercepts the 'login' action and redirects the user to the cas server login page
 * instead of showing the login form.
 *
 * @author  Mathieu Hetru <mathieu.hetru@univ-lille.fr>
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @link https://github.com/l3-team/dokuwiki-extensions-authcas
 */

// must be run within Dokuwiki
if (! defined('DOKU_INC'))
    die();

if (! defined('DOKU_LF'))
    define('DOKU_LF', "\n");
if (! defined('DOKU_TAB'))
    define('DOKU_TAB', "\t");
if (! defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');

require_once DOKU_PLUGIN . 'action.php';
require_once DOKU_PLUGIN . 'auth.php';


class action_plugin_authcas extends DokuWiki_Action_Plugin
{

    var $authcasplugin = null;

    function action_plugin_authcas() {
        $this->authcasplugin = plugin_load('auth', 'authcas');
    }

    public function register(Doku_Event_Handler $controller)
    {
	$this->authcasplugin = plugin_load('auth', 'authcas');
        if($this->getConf('displayDokuLoginForm')) {
            $controller->register_hook('HTML_LOGINFORM_OUTPUT', 'BEFORE', $this, 'addCasToLoginForm', array());
        } else {
            $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'redirectToCasLogin');
        }
    }

    function addCasToLoginForm(&$event, $param)
    {
	$this->authcasplugin = plugin_load('auth', 'authcas');
        $caslink = '<p><br/><a href="'.$this->authcasplugin->getLoginURL().'">'.$this->getLang('connection').'</a></p>';
        $pos = $event->data->findElementById('form', 'dw__login');
        $event->data->insertElement($pos-1, $caslink);

    }


    public function redirectToCasLogin(&$event, $param)
    {
	$this->authcasplugin = plugin_load('auth', 'authcas');
        global $ACT;
        if ($ACT == 'login') {
            //used instead of $auth if use of authsplit or authchained
            $this->authcasplugin->logIn();
        }
    }
}
