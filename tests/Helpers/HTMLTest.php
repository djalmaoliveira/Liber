<?php

include_once dirname(dirname(__FILE__)).'/include.php'        ;

class HTMLTest extends PHPUnit_Framework_TestCase {

    function SetUp() {
        Liber::loadHelper(array('HTML', 'Url'));
    }




    function testHeader() {
        html_header_('css', 'a.css');
        html_header_('css');

        html_header_('js', 'a.js');
        html_header_('js');

        html_header_();
    }

    function testScript() {
        html_script_('alert()');
        $this->assertEquals( 'alert()', html_script_(true) );

        html_script_('alert("ok");', true);
        $this->assertEquals( 'alert("ok");', html_script_(true) );
    }


    function testStyle() {
        html_style_('h1 {color:white}');
        $this->assertEquals( 'h1 {color:white}', html_style_(true) );

        html_style_('h1 {color:red}', true);
        $this->assertEquals( 'h1 {color:red}', html_style_(true) );

        $style = html_style_(true);
        $this->assertFalse( empty($style), 'Style not returned.' );
    }

    function testTitle() {
        html_title_('Title');
        $this->assertEquals('Title', html_title_(true), 'Title text should be the same.' );
    }

}
?>