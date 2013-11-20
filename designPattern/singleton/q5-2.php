<?php

class Triple {
    private static $_triple = [];
    private $_id;
    private function __construct($id) {
        echo "The instance {$id} is created." . PHP_EOL;
        $this->_id = $id;
    }

    public static function getInstance($id) {
        if (!isset(self::$_triple[$id])) {
            self::$_triple[$id] = new Triple($id);
        }

        return self::$_triple[$id]->_id;
    }
}

function main () {
    echo "start" . PHP_EOL;
    for ($i=0;$i<9;$i++) {
        $id = $i % 3;
        $triple = Triple::getInstance($id);
        echo "$i : {$triple}" . PHP_EOL;
    }
    echo "end" . PHP_EOL;
}

main();
