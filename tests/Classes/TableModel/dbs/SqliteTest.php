<?

include_once realpath( dirname(__FILE__).'/../../../' ).'/include.php';
require realpath( dirname(__FILE__).'/../' )."/TableModelBaseTest.php";
Liber::loadModel('Model');

class SqliteTest extends Base {
    function __construct() {
        $this->Model = new Model( Liber::db('sqlite') );
        $this->sql = "
            CREATE TABLE   model_test (
                id          integer NOT NULL PRIMARY KEY AUTOINCREMENT,
                str_value   varchar(255),
                int_value   integer,
                date_value  date,
                datetime_value  datetime,
                null_value  varchar(255),
                email_value varchar(255)
            );
        ";
    }
}
?>