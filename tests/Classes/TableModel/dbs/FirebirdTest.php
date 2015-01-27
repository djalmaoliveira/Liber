<?
include_once realpath( dirname(__FILE__).'/../../../' ).'/include.php';
require realpath( dirname(__FILE__).'/../' )."/TableModelBaseTest.php";
Liber::loadModel('Model');

class FirebirdTest extends Base {
    function __construct() {
        $this->Model = new Model( Liber::db('firebird') );
        $this->sql = "
            CREATE TABLE   model_test (
                id          integer NOT NULL PRIMARY KEY,
                str_value   varchar(255),
                int_value   integer,
                date_value  date,
                datetime_value  timestamp,
                null_value  varchar(255),
                email_value varchar(255)
            );

        ";
    }

    function setUp() {
        //$this->Model->db()->rollback();
        $this->Model->beginTransaction();

        $this->Model->db()->exec("DROP TRIGGER MODEL_TEST_ID;");
        $this->Model->db()->exec("DROP GENERATOR GEN_MODEL_TEST_ID;");

        parent::setUp();

        $this->Model->db()->exec("CREATE GENERATOR gen_model_test_id;");
        $this->Model->db()->exec("SET GENERATOR gen_model_test_id TO 1;");
        $this->Model->db()->exec("

            CREATE TRIGGER model_test_id for MODEL_TEST ACTIVE
            BEFORE INSERT POSITION 0
            AS
            BEGIN
              IF (NEW.id IS NULL) THEN
                NEW.id = GEN_ID(gen_model_test_id,1);
            END

        ");

        $this->Model->commit();
    }
}
?>