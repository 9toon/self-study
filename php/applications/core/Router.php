<?php

class Router
{
    protected $routes;

    public function __construct($definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    public function compileRoutes($definitions)
    {
        $routes = array();

        foreach ($definitions as $url => $params) {
            // ltrim => 文字列の最初から空白を除去する
            // '/'でURL文字列を分割
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                // 分割したURL文字列の中に「:」を含む文字があれば、正規表現の形式に変換する
                if (0 === strpos($token, ':')) { // e.g $token = ':hoge'
                    $name = substr($token, 1);   // e.g $name = 'hoge'
                    $token = '(?P<' . $nmae . '>[^/]+)'; // e.g $token = '(?P<hoge>[^/]+)'
                }
                $tokens[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    public function resolve($path_info)
    {
        if ('/' !== substr($path_info, 0, 1)) {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match('#^' . $pattern . '$#', $path_info, $matches)) {
                $params = array_merge($params, $matches);

                return $params;
            }
        }

        return false;
    }
}
