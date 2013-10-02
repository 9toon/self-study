<?php 

class Request
{
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name]) {
            return $_GET[$name]);
        }

        return $default;
    }
    
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name]) {
            return $_POST[$name]);
        }

        return $default;
    }

    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST']) {
            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    public function isSsl()
    {
        if (isset($_SERVER['HTTPS'] && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function getBaseUrl()
    {
        $script_name = $_SERVER['SCRIPT_NAME'];

        $request_uri = $this->getRequestUri();

        // strpos()関数で第一引数に指定した文字列中から、第二引数に指定した文字列が最初に出現する位置を取得する
        if (0 === strpos($request_uri, $script_name)) {
            // はじめに見つかるケース : フロントコントローラがURLに含まれる場合

            return $script_name;
        // dirname()関数で、PATH文字列からディレクトリ部分を取得する 
        } else if (0 === strpos($request_uri, dirname($script_name))) {
            // フロントコントローラが省略されている場合
            
            return rtrim($script_name, '/');
        }

        return '';
    }
    
    public function getPathInfo()
    {
        $base_url = $thid->getBaseUrl();
        $request_uri = $this->getRequestUri();

        if (false !== ($pos = strpos($request_uri, '?'))) {
            $request_uri = substr($request_uri, 0, $pos);
        }

        $path_info = (string)substr($request_uri, strlen($base_url));

        return $path_info;
    }

}

