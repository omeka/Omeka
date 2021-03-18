<?php
// PHPUnit 8+: requires return type hints on setUp, tearDown, etc.
class Omeka_Test_TestCase extends Omeka_Test_AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpLegacy();
    }

    protected function tearDown(): void
    {
        $this->tearDownLegacy();
        parent::tearDown();
    }

    public function assertPreConditions(): void
    {
        parent::assertPreConditions();
        $this->assertPreConditionsLegacy();
    }

    public function assertPostConditions(): void
    {
        parent::assertPostConditions();
        $this->assertPostConditionsLegacy();
    }

    public static function setUpBeforeClass(): void
    {
        static::setUpBeforeClassLegacy();
    }
}
