<?php
/**
 * milkycode Joomla 1.5 reCaptcha plugin.
 * @author      Christian Hinz <christian@milkycode.com>
 * @category    plugins
 * @package     plugins_system
 * @copyright   Copyright (c) 2015 milkycode UG (http://www.milkycode.com)
 * @url         https://github.com/milkycode/joomla_recaptcha_j15
 */

require_once(dirname(__FILE__).'/recaptchalib.php');

class ReCaptchaApi
{
    var $_success;

    var $_error;

    var $_resp;

    var $_ajax = true;

    var $_submitted = false;

    var $_processed = false;

    var $_publicKey = '6Lf2-QQAAAAAAC5kQM5ChJfvRP1jZNvOn8kE590h';

    var $_privateKey = '6Lf2-QQAAAAAAFcse8UtCXQ82wW5fWG9koEQAktv';

    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new ReCaptchaApi();
        }

        return $instance;
    }

    function get($key, $default = '')
    {
        $inst =& ReCaptchaApi::getInstance();

        return $inst->_get($key, $default);
    }

    function _get($key, $default = '')
    {
        $key = '_'.$key;

        return isset($this->$key) ? $this->$key : $default;
    }

    function setKeys($public, $private)
    {
        $inst =& ReCaptchaApi::getInstance();
        $inst->_set('publicKey', $public);
        $inst->_set('privateKey', $private);
    }

    function _set($key, $value)
    {
        $key = '_'.$key;
        $this->$key = $value;
    }

    function process()
    {
        $inst =& ReCaptchaApi::getInstance();
        $inst->_process();
    }

    function _process()
    {
        if ($this->_processed) {
            return;
        }

        if (JRequest::getVar("email")) {
            $this->_submitted = true;
            $secret = $this->_get('privateKey');
            $lib = new ReCaptcha($secret);
            $this->_resp = $lib->verifyResponse(
              $_SERVER["REMOTE_ADDR"],
              JRequest::getVar("g-recaptcha-response")
            );
            $this->_success = $this->_resp->success;
            if (!$this->_success) {
                $this->_error = $this->_resp->errorCodes;
            }
        }

        $inst =& ReCaptchaApi::getInstance();
        $this->_html = $inst->_recaptcha_get_html($this->_get('publicKey'), $this->_get('error'));
        $this->_processed = true;
    }

    function _recaptcha_get_html($key, $error)
    {
      return "
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class='g-recaptcha' data-sitekey='$key'></div>
      ";
    }
}
