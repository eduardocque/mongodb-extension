<?php

namespace mongodb\tests\fixtures\mongodb;

use yii\mongodb\ActiveFixture;

/**
 * Class BaseFixture
 * @package mongodb\tests\fixtures\mongodb
 */
class BaseFixture extends ActiveFixture
{
    /**
     * @var string
     */
    public $db = 'mongodb_test';

    /**
     * Overwrite default unload functionality to reset collection
     * after each test is run.
     */
    public function unload()
    {
        parent::resetCollection();
        parent::unload();
    }
}
