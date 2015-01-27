<?php

Liber::loadClass('TableModel');

/**
 * This class is used to represent a table to generate tests.
 */
class Model extends TableModel {

    function __construct($PDO) {
        parent::__construct( $PDO );

        $this->table        = 'model_test';
        $this->idField      = 'id';
        $this->sequenceName = 'gen_model_test_id';

        $this->aFields = Array (
            'id'                => Array('', 'ID', 0),
            'str_value'         => Array('', 'String value', 0),
            'int_value'         => Array('', 'Int. Value', 0),
            'date_value'        => Array('', 'Date value', 0),
            'datetime_value'    => Array('', 'DateTime value', 0),
            'null_value'        => Array('', 'Null Value', 0),
            'email_value'       => Array('', 'Email Value', Validation::EMAIL)
        );


    }
}
?>