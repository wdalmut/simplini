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
    
    public function testSimpleConfig()
    {
        $this->object->load(__DIR__ . '/configs/simple.ini');
        
        $this->assertEquals("ciao", $this->object->hello);
        $this->assertEquals("ok", $this->object->my);
    }

    public function testBaseConfig()
    {
        $this->object->load(__DIR__ . '/configs/base.ini');
        
        $this->assertEquals("ciao", $this->object->production()->hello);
    }
    
    public function testBaseOverrideConfig()
    {
        $this->object->load(__DIR__ . '/configs/base.ini', "development");
        
        $this->assertEquals("hello", $this->object->production()->hello);
    }
    
    public function testSubsetsConfig()
    {
        $this->object->load(__DIR__ . '/configs/subsets.ini');
    
        $this->assertEquals("ciao", $this->object->production()->a->b->c);
        $this->assertEquals("I don't know", $this->object->production()->a->b->e);
    }
    
    public function testSubsetsOverrideConfig()
    {
        $this->object->load(__DIR__ . '/configs/subsets.ini', "development");
    
        $this->assertEquals("hello", $this->object->production()->a->b->c);
        $this->assertEquals("I don't know", $this->object->production()->a->b->e);
    }
   
    public function testArrayConfig()
    {
        $this->object->load(__DIR__ . '/configs/arrays.ini');
        
        $this->assertEquals("b", $this->object->production()->a->b[0]);
        $this->assertEquals("c", $this->object->production()->a->b[1]);
        
        $this->assertEquals("hello", $this->object->production()->easy[0]);
        $this->assertEquals("ciao", $this->object->production()->easy[1]);
    }
    
    public function testArrayOverrideConfig()
    {
        $this->object->load(__DIR__ . '/configs/arrays.ini', "development");
        
        $this->markTestSkipped("Need a proposal...");
    }
    
    public function testNoOverrideMultipleSections()
    {
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini');
        
        $this->assertEquals("ciao", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("OK", $this->object->third()->value);
        
        // Force no override
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', false);
        
        $this->assertEquals("ciao", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("OK", $this->object->third()->value);
        
        // Force no override
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', array());
        
        $this->assertEquals("ciao", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("OK", $this->object->third()->value);
        
        // Force no override
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', '');
        
        $this->assertEquals("ciao", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("OK", $this->object->third()->value);
    }
    
    public function testRewriteMultipleSections()
    {
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', true);
    
        $this->assertEquals("hello", $this->object->production()->hello);
        $this->assertEquals("192.168.2.2", $this->object->mysql()->host);
        $this->assertEquals("KO", $this->object->third()->value);
    }
    
    public function testRewriteSingleSection()
    {
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', "development");
        
        $this->assertEquals("hello", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("OK", $this->object->third()->value);
    }
    
    public function testRewriteSubsetOfSections()
    {
        $this->object->load(__DIR__ . '/configs/multiple-sections.ini', array("development", "dthird"));
        
        $this->assertEquals("hello", $this->object->production()->hello);
        $this->assertEquals("localhost", $this->object->mysql()->host);
        $this->assertEquals("KO", $this->object->third()->value);
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testMagicSet()
    {
        $this->object->hello = "ciao";
    }
    
    public function testMagicGet()
    {
        $this->assertFalse($this->object->nonExistingKey);
        
        $this->assertFalse(array_key_exists("anotherFalse", $this->object));
    }
    
    public function testToArray()
    {
        $array = array("ca" => "cu", "a"=>array("b" => 'c'));
        $o = new Config($array);
        
        $t = $o->toArray();
        
        $this->assertThat($t, $this->equalTo($array));
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testOffsetSet()
    {
        $this->object->load(__DIR__ . '/configs/array.ini');
        
        $this->object->w[] = "hello";
    }
    
    public function testOffsetExists()
    {
        $this->object->load(__DIR__ . '/configs/simple.ini');
        $this->assertTrue(isset($this->object["hello"]));
    }
    
    public function testOffsetGet()
    {
        $this->object->load(__DIR__ . '/configs/simple.ini');
        
        $this->assertFalse($this->object["helloWorld"]);
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testOffsetUnset()
    {
        $this->object->load(__DIR__ . '/configs/simple.ini');
        
        unset($this->object["hello"]);
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testUnset()
    {
        $this->object->load(__DIR__ . '/configs/simple.ini');
        
        unset($this->object["hello"]);
    }
}