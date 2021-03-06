<?php

// set error reporting level
if (version_compare(phpversion(), "5.3.0", ">=") == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);


// initialiserer loginsystemet
$oSimpleLoginSystem = new SimpleLoginSystem();

/*Tegner opp loginbox*/
echo $oSimpleLoginSystem->getLoginBox();

/*Tegner opp chatten */
echo $oSimpleLoginSystem->getShoutbox();

/*Klassen til simpleloginsystem*/
class SimpleLoginSystem {

    /*Variabler av brukere*/
    var $aExistedMembers; 

    /*Konstruktør */
    function SimpleLoginSystem() {
        $this->aExistedMembers = array(
            'Mats' => 'd8578edf8458ce06fbc5bb76a58c5ca4',
            'Hans' => 'd8578edf8458ce06fbc5bb76a58c5ca4',
            'Sina' => 'd8578edf8458ce06fbc5bb76a58c5ca4',
            'Edgar' => 'd8578edf8458ce06fbc5bb76a58c5ca4'
        );
    }

    function getLoginBox() {
        ob_start();
        $sLoginForm = ob_get_clean();

        $sLogoutForm = '<a href="'.$_SERVER['PHP_SELF'].'?logout=1">logout</a>';

        if ((int)$_REQUEST['logout'] == 1) {
            if (isset($_COOKIE['member_name']) && isset($_COOKIE['member_pass']))
                $this->simple_logout();
        }

        if ($_REQUEST['username'] && $_REQUEST['password']) {
            if ($this->check_login($_REQUEST['username'], MD5($_REQUEST['password']))) {
                $this->simple_login($_REQUEST['username'], $_REQUEST['password']);
                return 'Hello ' . $_REQUEST['username'] . '! ' . $sLogoutForm;
            } else {
                return 'Username or Password is incorrect' . $sLoginForm;
            }
        } else {
            if ($_COOKIE['member_name'] && $_COOKIE['member_pass']) {
                if ($this->check_login($_COOKIE['member_name'], $_COOKIE['member_pass'])) {
                    return 'Hello ' . $_COOKIE['member_name'] . '! ' . $sLogoutForm;
                }
            }
            return $sLoginForm;
        }
    }

    function simple_login($sName, $sPass) {
        $this->simple_logout();

        $sMd5Password = MD5($sPass);

        $iCookieTime = time() + 24*60*60*30;
        setcookie("member_name", $sName, $iCookieTime, '/');
        $_COOKIE['member_name'] = $sName;
        setcookie("member_pass", $sMd5Password, $iCookieTime, '/');
        $_COOKIE['member_pass'] = $sMd5Password;
    }

    function simple_logout() { 
        setcookie('member_name', '', time() - 96 * 3600, '/');
        setcookie('member_pass', '', time() - 96 * 3600, '/');

        unset($_COOKIE['member_name']);
        unset($_COOKIE['member_pass']);
    }

    function check_login($sName, $sPass) {
        return ($this->aExistedMembers[$sName] == $sPass);
    }

    /*Shoutbox funksjon*/
    function getShoutbox() {
        /*Kobler til mysql med host, brukernavn, passord og Databasenavn */
        $con = mysqli_connect("localhost","root","","chatnew");

        /*Legger til i Database*/
        if ($_COOKIE['member_name']) {
            if(isset($_POST['s_say']) && $_POST['s_message']) {
                $sUsername = $_COOKIE['member_name'];
                $sMessage = mysqli_real_escape_string($con,$_POST['s_message']);
                mysqli_query($con,"INSERT INTO `s_messages` SET `user`='{$sUsername}', `message`='{$sMessage}', `when`=UNIX_TIMESTAMP()");
            }
        }

        /*leser ut de 2 siste meldingene*/
        $vRes = mysqli_query($con,"SELECT * FROM `s_messages` ORDER BY `id` DESC LIMIT 2");

        $sMessages = '';

        /*Skriver ut meldingen i chatboxen */
        while($aMessages = mysqli_fetch_array($vRes)) {
            $sWhen = date(" d/m/y – H:i", $aMessages['when']);
            $sMessages .= '<div class="message">'.'<a class="user-name">'. $aMessages['user'] . ': '.'</a>' .'<div class="messagetextbox"><a class="text-of-message"><br>'.$aMessages['message'].'</a></div>' . '<span>' . $sWhen . '</span></div>';
        }

        /*Lukker database connection*/
        mysqli_close($con);

        ob_start();
        require_once('Dash.html');
        echo $sMessages;
        $sShoutboxForm = ob_get_clean();

        return $sShoutboxForm;
    }
}

?>