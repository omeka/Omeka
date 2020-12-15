<?php

if (PHP_VERSION_ID >= 70200) {
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
} else if (PHP_VERSION_ID >= 70100) {
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
} else {
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
}
