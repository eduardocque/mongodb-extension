<?php

namespace mongodb\tests\helpers\brokenModels;

use MongoDB\BSON\UTCDateTime;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument1;
use Yii;

/**
 * Class CollectionWithoutInterface
 * @package mongodb\tests\helpers\brokenModels
 */
class CollectionWithoutInterface extends \yii\mongodb\ActiveRecord
{
    public $source;
    public $parent;
    public $parentAttribute;

    /**
     * @return array
     */
    public function attributes()
    {
        return [
        ];
    }
}
