<?php

/**
 * MainController
 *
 */
class MainController extends Controller{

    function __construct($p) {
        parent::__construct($p);
        Liber::loadHelper('Url');
    }


    public function index(){

        $this->render();

    }


    public function browser() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo $_SERVER['HTTP_USER_AGENT'];
    }

    // catch error
    public function error() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        b(); // this function doesn't exist and log this error
    }

    // URL /params/one/two
    public function params($param1='', $param2='') {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo "Params passed:<br/>";
        echo $param1;
        echo "<br/>";
        echo $param2;
    }

    public function direct() {
        echo "Called from Class <strong>".__CLASS__."</strong>, Method <strong>".__FUNCTION__."</strong>.<hr>";
        echo 'Direct routing.';
    }



    public function test() {
        echo Http::get('name');
    }

    public function download($method) {
        switch($method) {
            case 1:
                Http::download(Liber::conf('APP_PATH').'data/download.txt');
            break;
            case 2:
                Http::download(Liber::conf('APP_PATH').'data/download.txt', array('type' => 'plain', 'name' => 'report.txt'));
            break;
            case 3:
                Http::download(array('content' => 'this is the content file.', 'type' => 'plain', 'name' => 'other_report.txt'));
            break;
        }
    }


    public function template() {
        $this->view()->template('default.html');
        $this->view()->load('index.html');
    }

    public function caching() {
        $this->view()->cache('index.html', true);
        $this->view()->load('index.html');
    }

    public function layout() {
        $this->view()->setLayoutOnce('mylayout');
        $this->view()->load('index.html');
    }

    public function view_load() {
        echo $this->view()->load('index.html', true);
    }

    public function bench() {
        $a = '1';
        $a1 = (integer) $a;
        $p1 = 'c';
        $p2 = 'index.php';
        $b ='';
        $url = ' /a/b/c/index.php#';


        $total=(int) 70000;
        $ini = microtime(true);
        for($i=0; $i < $total; $i++) {
            $b = 'ab'.$url;
        }
        echo 'First:  '.(microtime(true)-$ini);

        echo "<br>=$b=";


        echo "<hr>";

        $ini = microtime(true);
        for($i=0; $i < $total; $i++) {
            $b = "ab$url";
        }
        echo 'Second: '.(microtime(true)-$ini);

        echo "<br>=$b=";
    }

}

?>