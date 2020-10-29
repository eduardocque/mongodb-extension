<?php

namespace mongodb\tests\helpers;

use Codeception\Module;
use Codeception\TestInterface;
use common\tests\unit\TestCase;
use mongodb\tests\fixtures\mongodb\CustomCollection;
use yii\helpers\ArrayHelper;
use yii\test\FixtureTrait;

/**
 * This helper is used to populate database with needed fixtures before any tests should be run.
 * For example - populate database with demo login user that should be used in acceptance and functional tests.
 * All fixtures will be loaded before suite will be starded and unloaded after it.
 */
class FixtureHelper extends Module
{
    /**
     * Redeclare visibility because codeception includes all public methods that not starts from "_"
     * and not excluded by module settings, in actor class.
     */
    use FixtureTrait {
        loadFixtures as protected;
        fixtures as protected;
        globalFixtures as protected;
        unloadFixtures as protected;
        getFixtures as protected;
        getFixture as protected;
    }

    /**
     * @var TestInterface
     */
    protected $test;

    /**
     * Const folder data fixture
     */
    const BASE_PATH_FIXTURE_DATA = __DIR__ . '/../fixtures/mysql/data/';

    /**
     * Method called before any suite tests run. Loads User fixture login user
     * to use in acceptance and functional tests.
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        $this->test = $test;

        $this->loadFixtures();
    }

    /**
     * Method is called after all suite tests run
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        $this->unloadFixtures();
    }

    /**
     * @return mixed
     */
    protected function getTestClass()
    {
        if ($this->test === null) {
            return false;
        }

        if ($this->test instanceof TestCase) {
            return $this->test;
        }

        return $this->test->getTestClass();
    }

    /**
     * @return array
     */
    public function fixtures()
    {
        if ($this->getTestClass() === false) {
            return [];
        }

        // Load test features
        if (method_exists($this->getTestClass(), '_fixtures')) {
            $fixtures = $this->getTestClass()->_fixtures();

            if (is_array($fixtures)) {
                return $fixtures;
            }
        }

        // No features found, return empty array
        return [];
    }

    /**
     * @param array $fixtures
     * @return array
     */
    public static function fixtureLoader(array $fixtures = [])
    {
        return ArrayHelper::filter(self::fixtureList(), $fixtures);
    }

    /**
     * @return array
     */
    public static function fixtureList()
    {
        return [
            FixtureConstants::CUSTOM_COLLECTION => [
                'class' => CustomCollection::class,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function _fixtures()
    {
        return [];
    }
}
