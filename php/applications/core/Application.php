<?php

// @todo abstractって？
// abstractは「抽象」を意味する
// この場合は、このクラスは継承先でメソッドなりなんなりをきちんと再定義する必要あり
// ってのをしめしてるのかと！
abstract class Application
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    protected $login_action = array();


    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        } 

    }

    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    // 空のメソッドを定義しておくことで、アプリケーションごとに設定を追加できるようにしてる
    protected function configure()
    {
    }

    // abstract は「抽象」的に定義することを指すっぽい
    // この場合、これがついたメソッドはアプリケーションごとに再定義しないとエラーが出るから、
    // 定義漏れをなくすためにこうしてるらしい。
    abstract public function getRootDir();

    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->db_manager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }
    
    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    /*
     * コントローラを特定し、レスポンスを返すまでの処理を管理するメソッド
     */
    public function run()
    {
        try {
            // ルーティングパラメータを取得
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }

    /*
     * 実際にアクションを実行するメソッド
     */
    public function runAction($controller_name, $action, $params = array())
    {
        // Note: ucfirst() -> 先頭一文字を大文字にする関数
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            // 例外処理を投げれば、returnで例外を示す値を返し続けなくても伝播してくれてベンリ！
            throw new HttpNotFoundException($controller_class . ' controller is not found.');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    /*
     * コントローラクラスを判別・生成するメソッド
     */
    protected function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }

        return new $controller_class($this);
    }

    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html"; charset="utf-8" />
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
    );
    }
}



