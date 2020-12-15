<?php
// PHPUnit 6-: no return typehints, no assertStringContainsString
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

    /*
     * PHPUnit 8 deprecates calling assertContains on string haystacks.
     * Here we "backport" assert[Not]StringContainsString to PHPUnit 6 and under
     * No typehints are used to preserve PHP 5.x compatibility
     */
    public static function assertStringContainsString($needle, $haystack, $message = '')
    {
        parent::assertContains($needle, $haystack, $message);
    }

    public static function assertStringNotContainsString($needle, $haystack, $message = '')
    {
        parent::assertNotContains($needle, $haystack, $message);
    }
}
