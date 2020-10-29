<?php

namespace mongodb\tests\helpers\models\customCollection;

use MongoDB\BSON\UTCDateTime;
use mongodb\mongodb\ActiveRecord;
use yii;

/**
 * Class CustomCollectionSubDocument1
 * @package mongodb\tests\helpers\models\customCollection
 *
 * @property string $simple_attribute_string
 * @property integer simple_attribute_number
 * @property UTCDateTime $simple_attribute_date
 * @property array $simple_attribute_array
 * @property boolean $simple_attribute_boolean
 *
 * here are sub documents
 *
 * @property CustomCollectionSubDocument3 $subDocument3
 *
 */
class CustomCollectionSubDocument1 extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['simple_attribute_string', 'simple_attribute_number', 'simple_attribute_date', 'simple_attribute_array', 'simple_attribute_boolean'], 'required'],
            [['sub_document_3'], 'mongodb\Validator', 'fullPath' => true]
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
            'simple_attribute_boolean',

            'sub_document_3',
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

            'sub_document_3' => Yii::t('app', 'Sub Document 3'),
        ];
    }

    /**
     * @return ActiveRecord|object
     * @throws yii\base\InvalidConfigException
     */
    public function embedSubDocument3()
    {
        return $this->mapDocument([], CustomCollectionSubDocument3::class, 'sub_document_3');
    }
}
