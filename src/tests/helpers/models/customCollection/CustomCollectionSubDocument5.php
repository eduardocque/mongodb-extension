<?php

namespace mongodb\tests\helpers\models\customCollection;

use MongoDB\BSON\UTCDateTime;
use mongodb\mongodb\ActiveRecord;
use yii;

/**
 * Class CustomCollectionSubDocument5
 * @package mongodb\tests\helpers\models\customCollection
 *
 * @property string $simple_attribute_string
 * @property integer simple_attribute_number
 * @property UTCDateTime $simple_attribute_date
 * @property array $simple_attribute_array
 * @property boolean $simple_attribute_boolean
 */
class CustomCollectionSubDocument5 extends ActiveRecord
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['simple_attribute_string', 'simple_attribute_number', 'simple_attribute_date', 'simple_attribute_array', 'simple_attribute_boolean'], 'required']
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'simple_attribute_string',
            'simple_attribute_number',
            'simple_attribute_date',
            'simple_attribute_array',
            'simple_attribute_boolean'
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'simple_attribute_string' => Yii::t('app', 'Simple String'),
            'simple_attribute_number' => Yii::t('app', 'Simple Number'),
            'simple_attribute_date' => Yii::t('app', 'Simple Date'),
            'simple_attribute_array' => Yii::t('app', 'Simple Array'),
            'simple_attribute_boolean' => Yii::t('app', 'Simple Boolean'),
        ];
    }
}
