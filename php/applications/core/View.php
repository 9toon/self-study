<?php

class View
{
    protected $base_dir;
    protected $defaults;
    protected $layout_variables = array();

    public function __construct($base_dir, $defaults = array())
    {
        $this->base_dir = $base_dir;
        $this->default = $defaults;
    }

    public function setLayoutVar($name, $value)
    {
        $this->layout_variables[$name] = $value;
    }

    // このメソッドでは変数の展開を行うので、このメソッド元々の変数は変数名の衝突が起こりにくいように"$_"で始まるようにしてる
    // @todo 完全にぶつからないとは限らない。もっといい方法ない？
    public function render($_path, $_variables = array(), $_layout = false)
    {
        $_file = $this->base_dir . '/' . $_path . '.php';

        // Note: extract() 連想配列のキーを変数名に、値をその変数の値に格納して展開する関数
        extract(array_merge($this->default, $_variables));

        ob_start();
        ob_implicit_flush(0);


        require $_file;

        $content = ob_get_clean();

        if ($_layout) {
            $content = $this->render($_layout, 
                array_merge($this->layout_variables, array(
                    '_content' => $content,
                )
            ));
        }

        return $content;
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

