<?php

namespace common\extensions\mongodb\tests\unit\mongodb;

use Codeception\Specify;
use mongodb\tests\helpers\FixtureConstants;
use mongodb\tests\helpers\FixtureHelper;
use Codeception\Test\Unit;

/**
 * Class AssetHostFixComponentTest
 * @package dashboard\tests\unit\helpers
 */
class ActiveRecordTest extends Unit
{
    use Specify;

    /**
     * @return array
     */
    public function _fixtures()
    {
        return FixtureHelper::fixtureLoader([
            FixtureConstants::CUSTOM_COLLECTION
        ]);
    }

    /**
     * Test functionality.
     */
    public function testFunctionality()
    {
        $this->specify('Functionality', function () {
        });
    }
}
