<?php

use JustGeeky\LaravelCybersource\CybersourceHelper;
use LaravelCybersource\TestCase;

class CybersourceHelperTest extends TestCase {

    public function testCsvToArray()
    {
        $csv = 'test,test2,test3,test4';
        $array = CybersourceHelper::csvToArray($csv);

        $this->assertEquals('test', $array[0]);
        $this->assertEquals('test2', $array[1]);
        $this->assertEquals('test3', $array[2]);
        $this->assertEquals('test4', $array[3]);
    }

    public function testArrayToCsv()
    {
        $array = array('test', 'test2', 'test3');
        $csv = CybersourceHelper::arrayToCsv($array);

        $this->assertEquals('test,test2,test3', $csv);
    }

    public function testSign()
    {
        $params = array(
            'signed_field_names' => 'test-key,test-key-2',
            'test-key' => 'test-value',
            'test-key-2' => 'test-value-2'
        );
        $key = 'test-secret-key';

        $signResult = '+98jXmH+IBMeFdw70ciVdA89er7BKAfAMgWkexx/7m8=';

        $this->assertEquals($signResult, CybersourceHelper::sign($params, $key));
    }

    public function testStrGetCSV()
    {
        $string = 'header1,header2' . "\n";
        $string .= 'val1,val2' . "\n";

        $array = CybersourceHelper::str_getcsv($string);
        $array = $array[0];

        $this->assertArrayHasKey('header1', $array);
        $this->assertArrayHasKey('header2', $array);

        $this->assertEquals('val1', $array['header1']);
        $this->assertEquals('val2', $array['header2']);
    }

} 