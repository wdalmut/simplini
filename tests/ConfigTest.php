<?php 
require_once __DIR__ . '/../src/Config.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{
    private $object;
    
    public function setUp()
    {
        $this->object = new Config();
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testMissingFile()
    {
        $this->object->load("no-one.ini");
    }

    public function testLoadConfig()
    {
        $this->object->load(__DIR__ . '/configs/base.ini');
        
        $this->assertEquals("ciao", $this->object->production()->a->b->c);
    }
}