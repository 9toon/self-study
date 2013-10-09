<?php
class Session
{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenerated = false;

    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    public function clear()
    {
        $_SESSION = array();
    }

    public function regenerate($destroy = true)
    {
        if (!self::$sessionIdRegenerated) {
            /*
             * Note: セッションIDを新しく発行する関数
             * セキュリティ対策(セッション固定攻撃対策)で使用(p. 349)
             */
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        // ログイン後にsession_idを変更するためにこの関数を呼び出してる
        $this->regenerate();
    }

    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}

