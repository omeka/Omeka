<?php

class Omeka_Job_FactoryTest extends Omeka_Test_TestCase
{
    public function testBuildJobFromJson()
    {
        $factory = new Omeka_Job_Factory([]);
        $json = Zend_Json::encode([
            'className' => 'Omeka_Job_Mock',
            'options' => ['bar' => true]
        ]);
        $job = $factory->from($json);
        $this->assertEquals('Omeka_Job_Mock', get_class($job));
    }

    public function testJobOptionsFromJson()
    {
        $factory = new Omeka_Job_Factory([]);
        $json = Zend_Json::encode([
            'className' => 'Omeka_Job_Mock',
            'options' => ['bar' => true]
        ]);
        $job = $factory->from($json);
        $this->assertEquals(['bar' => true], $job->getMiscOptions());
    }

    public function testConstructorOptionsPassedToJobs()
    {
        $factory = new Omeka_Job_Factory(['foo' => true]);
        $json = Zend_Json::encode([
            'className' => 'Omeka_Job_Mock',
            'options' => ['bar' => true]
        ]);
        $job = $factory->from($json);
        $this->assertEquals(['foo' => true, 'bar' => true],
            $job->getMiscOptions());
    }

    public function testErrorOnMissingJobClass()
    {
        $this->setExpectedException('Omeka_Job_Factory_MalformedJobException');
        $factory = new Omeka_Job_Factory([]);
        $json = Zend_Json::encode([
            'options' => ['bar' => true]
        ]);
        $job = $factory->from($json);
    }

    public function testErrorOnInvalidJobClass()
    {
        $this->setExpectedException('Omeka_Job_Factory_MissingClassException');
        $factory = new Omeka_Job_Factory([]);
        $json = Zend_Json::encode([
            'className' => 'NonexistentJobClass_12345',
            'options' => ['bar' => true]
        ]);
        $job = $factory->from($json);
    }

    public function testErrorOnMalformedJson()
    {
        $this->setExpectedException('Omeka_Job_Factory_MalformedJobException');
        $factory = new Omeka_Job_Factory([]);
        $factory->from("foobar_invalid_json");
    }

    public function testErrorOnMissingOptions()
    {
        $this->setExpectedException('Omeka_Job_Factory_MalformedJobException');
        $factory = new Omeka_Job_Factory([]);
        $json = Zend_Json::encode([
            'className' => 'Omeka_Job_Mock',
        ]);
        $job = $factory->from($json);
    }

    public function testBuildShortcutCreatesInstance()
    {
        $factory = new Omeka_Job_Factory(['foo' => true]);
        $job = $factory->build(
            [
                'className' => 'Omeka_Job_Mock',
                'bar' => false,
            ]
        );
        $this->assertInstanceOf('Omeka_Job_Mock', $job);
    }

    public function testBuildShortcutPassesOptions()
    {
        $factory = new Omeka_Job_Factory(['foo' => true]);
        $job = $factory->build(
            [
                'className' => 'Omeka_Job_Mock',
                'options' => ['bar' => false],
            ]
        );
        $this->assertEquals(
            [
                'foo' => true,
                'bar' => false,
            ],
            $job->getMiscOptions()
        );
    }
}
