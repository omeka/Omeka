<?php
// PHPUnit 7: no return typehints, has assertStringContainsString, etc.
class Omeka_Test_TestCase extends Omeka_Test_AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setUpLegacy();
    }

    protected function tearDown()
    {
        $this->tearDownLegacy();
        parent::tearDown();
    }

    public function assertPreConditions()
    {
        parent::assertPreConditions();
        $this->assertPreConditionsLegacy();
    }

    public function assertPostConditions()
    {
        parent::assertPostConditions();
        $this->assertPostConditionsLegacy();
    }

    public static function setUpBeforeClass()
    {
        static::setUpBeforeClassLegacy();
    }
}
