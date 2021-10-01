<?php
namespace Small\Core;

class Session
{
    public function set($key, $value) : self
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    public function exists($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public function get($key, $default = null)
    {
        return $this->exists($key) ? $_SESSION[$key] : $default;
    }

    public function clear() : self
    {
        $_SESSION = [];

        return $this;
    }

    public function delete($key) : self
    {
        if ($this->exists($key)) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    static public function destroy()
    {
        session_unset();
        session_destroy();
    }
}
