<?php namespace kcfinder\integration;

class Yii {
    protected static $authenticated = null;
    static function checkAuthentication() {
        if (self::$authenticated !== null) {
            return self::authenticated;
        }
        $current_cwd = getcwd();
        chdir('..');
        include './vendor/autoload.php';
        $dir = dirname(__DIR__, 2);
        \Yii::createWebApplication($dir . '/protected/config/main.php');
        $state = \Yii::app()->user->getState('id');

        if ($state === null) {
            // not authenticated
            self::$authenticated = false;
            return false;
        }
        /*
         * Intentionally don't restore pwd if not authenticated, so that file
         * system related functionality is only for authenticated users.
         */
        chdir($current_cwd);
        if (!isset($_SESSION['KCFINDER'])) {
            $_SESSION['KCFINDER'] = array();
        }
        if (!isset($_SESSION['KCFINDER']['disabled'])) {
            $_SESSION['KCFINDER']['disabled'] = false;
        }
        $_SESSION['KCFINDER']['_check4htaccess'] = false;
        $_SESSION['KCFINDER']['uploadURL'] = $_SERVER['KCFINDER_UPLOAD_URL'];
        $_SESSION['KCFINDER']['uploadDir'] = $_SERVER['KCFINDER_UPLOAD_DIR'];
        $_SESSION['KCFINDER']['theme'] = 'default';
        self::$authenticated = true;
        return true;
    }
}
if (!\kcfinder\integration\Yii::checkAuthentication()) {
    die("NOT AUTHORISED. SESSION TIMED OUT.");
}
