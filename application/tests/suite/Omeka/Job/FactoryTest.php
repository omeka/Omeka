<?php

class Omeka_Job_FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testBuildJobFromJson()
    {
        $factory = new Omeka_Job_Factory(array());
        $json = Zend_Json::encode(array(
            'className' => 'Omeka_Job_Mock',
            'options' => array('bar' => true)
        ));
        $job = $factory->from($json);
        $this->assertEquals('Omeka_Job_Mock', get_class($job));
    }

    public function testJobOptionsFromJson()
    {
        $factory = new Omeka_Job_Factory(array());
        $json = Zend_Json::encode(array(
            'className' => 'Omeka_Job_Mock',
            'options' => array('bar' => true)
        ));
        $job = $factory->from($json);
        $this->assertEquals(array('bar' => true), $job->getMiscOptions());
    }

    public function testConstructorOptionsPassedToJobs()
    {
        $factory = new Omeka_Job_Factory(array('foo' => true));
        $json = Zend_Json::encode(array(
            'className' => 'Omeka_Job_Mock',
            'options' => array('bar' => true)
        ));
        $job = $factory->from($json);
        $this->assertEquals(array('foo' => true, 'bar' => true),
            $job->getMiscOptions());
    }

    /**
     * @expectedException Omeka_Job_Factory_MalformedJobException
     */
    public function testErrorOnMissingJobClass()
    {
        $factory = new Omeka_Job_Factory(array());
        $json = Zend_Json::encode(array(
            'options' => array('bar' => true)
        ));
        $job = $factory->from($json);
    }

    /**
     * @expectedException Omeka_Job_Factory_MissingClassException
     */
    public function testErrorOnInvalidJobClass()
    {
        $factory = new Omeka_Job_Factory(array());
        $json = Zend_Json::encode(array(
            'className' => 'NonexistentJobClass_12345',
            'options' => array('bar' => true)
        ));
        $job = $factory->from($json);
    }

    /**
     * @expectedException Omeka_Job_Factory_MalformedJobException
     */
    public function testErrorOnMalformedJson()
    {
        $factory = new Omeka_Job_Factory(array());
        $factory->from("foobar_invalid_json");
    }

    /**
     * @expectedException Omeka_Job_Factory_MalformedJobException
     */
    public function testErrorOnMissingOptions()
    {
        $factory = new Omeka_Job_Factory(array());
        $json = Zend_Json::encode(array(
            'className' => 'Omeka_Job_Mock',
        ));
        $job = $factory->from($json);
    }

    public function testBuildShortcutCreatesInstance()
    {
        $factory = new Omeka_Job_Factory(array('foo' => true));
        $job = $factory->build(
            array(
                'className' => 'Omeka_Job_Mock', 
                'bar' => false,
            )
        );
        $this->assertInstanceOf('Omeka_Job_Mock', $job);
    }

    public function testBuildShortcutPassesOptions()
    {
        $factory = new Omeka_Job_Factory(array('foo' => true));
        $job = $factory->build(
            array(
                'className' => 'Omeka_Job_Mock', 
                'options' => array('bar' => false),
            )
        );
        $this->assertEquals(
            array(
                'foo' => true, 
                'bar' => false,
            ), 
            $job->getMiscOptions()
        );
    }
}
