<?php

include_once dirname(dirname(__FILE__)).'/include.php'        ;

class HTMLTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
        Liber::loadHelper(array('HTML', 'Url'));
    }




    function testHtmheader() {
        html_header_('css', 'a.css');
        html_header_('css');

        html_header_('js', 'a.js');
        html_header_('js');

        html_header_();
    }

}
?>