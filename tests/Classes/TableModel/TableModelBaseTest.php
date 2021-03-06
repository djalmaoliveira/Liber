<?php


/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-06 at 18:43:51.
 */
class Base extends PHPUnit_Framework_TestCase
{
    /**
     * @var TableModel
     */
    protected $Model;

    protected $sql;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        if ( !is_object($this->Model->db()) ) {
            $this->markTestSkipped(
              'No PDO connection.'
            );
        }

        //$this->Model->db()->beginTransaction();
        $this->Model->db()->exec("DROP TABLE model_test");
        $created = $this->Model->db()->exec($this->sql);
        //$this->Model->db()->commit();

        if ( $created === false ) {
            print_r( $this->Model->db()->errorInfo() );
            $this->markTestSkipped(
              'Table not created.'
            );
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers TableModel::turnValidation
     */
    public function testTurnValidation()
    {
        $this->Model->field('email_value', 'aaa@aaa');
        $this->assertFalse( $this->Model->validateData(), 'Should return invalid email.' );
        $this->Model->field('email_value', 'djalmaoliveira@gmail.com');
        $this->assertTrue( $this->Model->validateData(), 'Should return a valid email.' );
    }

    /**
     * @covers TableModel::db
     */
    public function testDb()
    {
        $this->assertEquals( 'PDO', get_class( $this->Model->db() ) );
    }


    /**
     * @covers TableModel::changed
     */
    public function testChanged()
    {
        $this->assertFalse($this->Model->changed('str_value'));
        $this->Model->field('str_value', 'new value');
        $this->assertTrue($this->Model->changed('str_value'));
    }

    /**
     * @covers TableModel::insert
     */
    public function testInsert()
    {
        $this->Model->field('str_value', 'insert test');
        $this->assertTrue( $this->Model->insert(), "A correct insert should return true.".print_r($this->Model->errors(),true) );

        // retrieve value
        $current_value = $this->Model->field('str_value');
        $model = $this->Model->getField( $this->Model->field('id'), 'str_value' );
        $this->assertEquals( $current_value,  $model['str_value'], "Updated value should have been stored.");
    }

    /**
     * @covers TableModel::update
     */
    public function testUpdate()
    {
        // prepare
        $this->Model->field('str_value', 'insert test');
        $this->assertTrue( $this->Model->insert(), "Should be capable to insert before update data." ) ;

        // update()
        $this->Model->field('str_value', 'updated value');
        $this->assertTrue( $this->Model->update() );

        // update(args)
        $model = Array('id' => $this->Model->field('id'), 'str_value' => 'updated value by arg');
        $this->assertTrue( $this->Model->update($model), "Should be possible update by args." );

        // retrieve value
        $current_value = $this->Model->field('str_value');
        $model = $this->Model->getField( $this->Model->field('id'), 'str_value' );
        $this->assertEquals( $current_value,  $model['str_value'], "Updated value should have been stored.");
    }


    /**
     * @covers TableModel::delete
     */
    public function testDelete()
    {
        $this->Model->field('str_value', 'delete test');
        $this->assertTrue( $this->Model->insert(), "Should be capable to insert before delete data." ) ;

        // delete()
        $this->assertTrue( $this->Model->delete() );

        //delete(arg)
        $this->assertTrue( $this->Model->insert(), "Should be capable to insert before delete data." ) ;
        $id = $this->Model->field('id');
        $this->assertTrue( $this->Model->delete( $id ) );

        // retrieve value
        $this->assertFalse( $this->Model->get( $id ), "Deleted row still stored.");
    }


    /**
     * @covers TableModel::save
     */
    public function testSave()
    {
        // save(), insertion
        $this->Model->field('str_value', 'saving by insert');
        $this->Model->field('int_value', null);
        $this->Model->field('date_value', null);
        $this->Model->field('datetime_value', null);
        $this->assertTrue( $this->Model->save(), "Should be capable to save." ) ;
        $saved = $this->Model->toArray();
        $this->Model->get( $saved['id'] );
        $this->assertEquals( $saved, $this->Model->toArray(), "Should be the same values inserted and retrieved." ) ;

        // save(), updating
        $this->Model->field('str_value', 'saving by update');
        $this->assertTrue( $this->Model->save(), "Should be capable to save by update." ) ;
        $saved = $this->Model->toArray();
        $this->Model->get( $saved['id'] );
        $this->assertEquals( $saved, $this->Model->toArray(), "Should be the same values updated and retrieved." ) ;

        // save(arg), insertion
        $this->Model->clear();
        $model = Array('str_value' => 'saving by insert with arg');
        $this->assertTrue( $this->Model->save( $model ), "Should be capable to save with arg." ) ;

        // save(arg), updating
        $model = Array('id' => $this->Model->field('id'), 'str_value' => 'saving by update with arg');
        $this->assertTrue( $this->Model->save( $model ), "Should be capable to save by update with arg." ) ;
    }


    /**
     * @covers TableModel::field
     */
    public function testField()
    {
        $this->assertEmpty($this->Model->field('str_value'), "Field should be empty.");

        $this->Model->field('str_value', 'aaa');
        $this->assertEquals('aaa', $this->Model->field('str_value'), "Field should be have a value.");
    }


    /**
     * @covers TableModel::desc
     */
    public function testDesc()
    {
        $this->assertNotEmpty($this->Model->desc('str_value'), "Description should be empty.");

        $this->Model->desc('str_value', 'aaa');
        $this->assertEquals('aaa', $this->Model->desc('str_value'), "Description should be have a value.");
    }


    /**
     * @covers TableModel::getField
     */
    public function testGetField()
    {
        $this->Model->field('str_value', 'getField test');
        $this->assertTrue( $this->Model->insert() );

        $model_expected = $this->Model->toArray();
        $model = $this->Model->getField( $this->Model->field('id'), 'str_value' );
        $this->assertEquals( $model_expected['str_value'],  $model['str_value']);
    }


    /**
     * @covers TableModel::get
     */
    public function testGet()
    {
        $this->Model->field('str_value', 'getField test');
        $this->assertTrue( $this->Model->insert(), "Should be capable insert before get." );

        $this->assertTrue( $this->Model->get( $this->Model->field('id') ) );
    }

    /**
     * @covers TableModel::clear
     */
    public function testClear()
    {
        $keys = array_keys( $this->Model->toArray() );

        $this->Model->clear();

        foreach ($keys as $field) {
            $this->assertEmpty( $this->Model->field( $field ) );
        }


        $this->Model->clear(true);
        foreach ($keys as $field) {
            $this->assertEmpty( $this->Model->field( $field ), "Field should be have a empty value." );
        }
    }


    /**
     * @covers TableModel::loadFrom
     */
    public function testLoadFrom()
    {
        $model              = $this->Model->toArray();
        $model['id']        = 1;
        $model['str_value'] = 'some value';

        $this->assertTrue( $this->Model->loadFrom( $model ) );
        $this->assertEquals( $model, $this->Model->toArray() );
    }


    /**
     * @covers TableModel::toArray
     */
    public function testToArray()
    {

        $this->assertInternalType('array', $this->Model->toArray()  );
        $this->assertInternalType('array', $this->Model->toArray('desc')  );
    }


    /**
     * @covers TableModel::searchBy
     */
    public function testSearchBy()
    {

        $this->Model->field('str_value', 'searchBy');
        $this->assertTrue( $this->Model->save(), "Should be capable to save before to search." ) ;

        // single field search
        $res = $this->Model->searchBy('str_value', 'searchBy')->fetch();
        $this->assertInternalType('array', $res);
        $this->assertEquals( $this->Model->field('str_value'),  $res['str_value']);

        // multiple field search
        $res = $this->Model->searchBy( Array('str_value' => 'searchBy', 'id' => $this->Model->field('id') ) )->fetch();
        $this->assertEquals( $this->Model->field('str_value'),  $res['str_value'], "Problem with Multiple field searching using 'AND'.");

        $res = $this->Model->searchBy( Array( 'or' => Array('str_value' => 'searchBy', 'id' => $this->Model->field('id') ) ) )->fetch();
        $this->assertEquals( $this->Model->field('str_value'),  $res['str_value'], "Problem with Multiple field searching using 'OR'.");

        // single field search, returning other field
        $res = $this->Model->searchBy('str_value', 'searchBy', array('fields'=>array('id')))->fetch();
        $this->assertInternalType('array', $res);
        $this->assertArrayHasKey( 'id', $res, 'Should return id field.');
    }


    /**
     * @covers TableModel::search
     */
    public function testSearch()
    {

        $this->Model->field('str_value', 'searchBy name and age');
        $this->assertTrue( $this->Model->save(), "Should be capable to save before to search." ) ;

        $res = $this->Model->search( 'By' )->fetchAll();
        $this->assertGreaterThan(0, count($res), "Should have returned at least one row.");

        $res = $this->Model->search( 'name age' )->fetchAll();
        $this->assertGreaterThan(0, count($res), "Should have returned at least one row.");

    }

    /**
     * @covers TableModel::addValidation
     */
    public function testAddValidation()
    {
        $this->Model->addValidation('test', create_function('$Model', '
            if ( $Model->field("str_value") != "check" ) {
                return Array("str_value" => "wrong value");
            }
        '));
        $this->Model->field('str_value', 'aaa');
        $this->assertFalse( $this->Model->save(), "Validation should have blocked saving." );
    }

    /**
     * @covers TableModel::validateData
     */
    public function testValidateData()
    {

        $this->assertTrue($this->Model->validateData(), "Empty validation should return true.");

        $this->Model->addValidation('test', create_function('$Model', '
            if ( $Model->field("str_value") != "check" ) {
                return Array("str_value" => "wrong value");
            }
        '));

        $this->assertFalse($this->Model->validateData(), "Validation should works.");
    }

    /**
     * @covers TableModel::errors
     */
    public function testErrors()
    {

        $this->assertEmpty($this->Model->errors('info'));
        $this->assertEmpty($this->Model->errors());
        $this->Model->errors('info', 'some error message');
        $this->assertNotEmpty($this->Model->errors(), "Should return an errors array.");
        $this->assertNotEmpty($this->Model->errors('info'), 'Should return array of namespace "info" errors.');
    }

    /**
     * Test transactions methods
     */
    public function testTransactions() {

        // test simple transaction call
        //
        $this->Model->beginTransaction();
        $this->Model->field('str_value', 'simple transaction');
        $this->assertTrue( $this->Model->save(), "Should be capable to save before commit." ) ;
        $this->assertTrue( $this->Model->commit(), 'Should do commit on simple transaction.' );
        $this->Model->rollback();
        $this->assertTrue( $this->Model->get( $this->Model->field('id') ), 'Should be stored after commit call.' );

        // multiple transaction calls
        //
        $this->Model->clear();
        $this->Model->beginTransaction();
        $this->Model->field('str_value', 'first value');
        $this->assertTrue( $this->Model->save(), "Should be capable to save before second beginTransaction." ) ;

        $this->Model->clear();
        $this->Model->beginTransaction();
        $this->Model->field('str_value', 'second value');
        $this->assertTrue( $this->Model->save(), "Should be capable to save after second beginTransaction." ) ;

        $this->assertTrue( $this->Model->commit(), 'Should do commit on first commit.' );
        $this->assertTrue( $this->Model->commit(), 'Should do commit on second commit.' );

        $this->Model->rollback();
        $this->assertTrue( $this->Model->get( $this->Model->field('id') ), 'Should be stored after commit call.' );

    }

    /**
     * Test Paginate method
     */
    public function testPaginate() {
        // save(), insertion
        $this->Model->field('str_value', 'saving by insert');
        $this->assertTrue( $this->Model->save(), "Should be capable to save." ) ;
        $res = $this->Model->paginate( 'by' );
        $this->assertGreaterThan(0, count($res), "Should have returned at least one row.");

    }
}
?>