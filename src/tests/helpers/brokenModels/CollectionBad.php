<?php

namespace mongodb\tests\helpers\brokenModels;

use MongoDB\BSON\UTCDateTime;
use mongodb\mongodb\ActiveRecord;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument1;
use Yii;

/**
 * Class CollectionBad
 * @package mongodb\tests\helpers\brokenModels
 */
class CollectionBad extends ActiveRecord
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
            'attribute_target',
            'document'
        ];
    }

    public function embedDocument()
    {
        return $this->mapDocument([], CustomCollectionSubDocument1::class, 'document');
    }
}
