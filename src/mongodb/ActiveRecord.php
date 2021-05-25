<?php

namespace mongodb\mongodb;

use mongodb\DocumentInterface;
use mongodb\helpers\MongoDBUtils;
use mongodb\DocumentTrait;
use yii\base\InvalidConfigException;
use yii\validators\Validator;

/**
 * Class ActiveRecord
 * @package mongodb\mongodb
 */
class ActiveRecord extends \yii\mongodb\ActiveRecord implements DocumentInterface
{
    use DocumentTrait;

    /**
     * ActiveRecord constructor.
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Init Documents
        $this->initDocuments();
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false; // @codeCoverageIgnore
        }

        $this->saving = true;

        // Convert documents to array
        $this->documentsToArray();

        return true;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->saving = false;

        // Convert array to documents
        $this->arrayToDocuments();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param array $values
     * @param bool $safeOnly
     * @throws InvalidConfigException
     */
    public function setAttributes($values, $safeOnly = true)
    {
        foreach ($values as $key => $value) {
            /* @var $value mixed */
            if (strpos($key, '.')) {
                $this->$key = $value;
                unset($values[$key]);
            } elseif (is_array($value) && isset($this->children[$key])) {
                foreach ($value as $subKey => $subValue) {
                    /* @var $subValue array */
                    if (is_array($subValue) && $this->children[$key] && $this->children[$key]['type'] === MongoDBUtils::DOCUMENT_TYPE_LIST) {
                        $values[$key][$subKey] = $this->mapDocument($subValue, $this->children[$key]['class'], $key, $this->isNewRecord);
                    } elseif ($this->children[$key] && $this->children[$key]['type'] === MongoDBUtils::DOCUMENT_TYPE_SIMPLE) {
                        $this->$key->setAttributes($value);
                        unset($values[$key]);
                    }
                }
            }
        }

        parent::setAttributes($values, $safeOnly);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws InvalidConfigException
     */
    public function setAttribute($name, $value)
    {
        if (strpos($name, '.')) {
            $this->$name = $value;
        } elseif (is_array($value) && isset($this->children[$name])) {
            foreach ($value as $subKey => $subValue) {
                /* @var $subValue array */
                if (is_array($subValue) && $this->children[$name] && $this->children[$name]['type'] === MongoDBUtils::DOCUMENT_TYPE_LIST) {
                    $value[$subKey] = $this->mapDocument($subValue, $this->children[$name]['class'], $name, $this->isNewRecord);
                } elseif ($this->children[$name] && $this->children[$name]['type'] === MongoDBUtils::DOCUMENT_TYPE_SIMPLE) {
                    $this->$name->setAttributes($value);
                    return;
                }
            }

            parent::setAttribute($name, $value);
        } else {
            parent::setAttribute($name, $value);
        }
    }

    /**
     * Returns the attribute names that are subject to validation in the current scenario.
     * @return string[] safe attribute names
     */
    public function activeAttributes()
    {
        $attributes = parent::activeAttributes();
        foreach ($attributes as $attribute) {
            /* @var $value mixed */
            $model = $this->getNestedDocument($attribute);
            if ($model !== $this) {
                foreach ($model->activeAttributes() as $nestedAttribute) {
                    /* @var $nestedAttribute string */
                    $attributes[] = "$attribute.$nestedAttribute";
                }
            }
        }

        return $attributes;
    }

    /**
     * Returns the validators applicable to the current [[scenario]].
     * @param string $attribute the name of the attribute whose applicable validators should be returned.
     * If this is null, the validators for ALL attributes in the model will be returned.
     * @return Validator[] the validators applicable to the current [[scenario]].
     */
    public function getActiveValidators($attribute = null)
    {
        $validators = parent::getActiveValidators($attribute);
        if (isset($attribute)) {
            $model = $this->getNestedDocument($attribute);
            if ($model !== $this) {
                if (strpos($attribute, '.')) {
                    $nameParts = explode('.', $attribute);
                    $attribute = array_pop($nameParts);
                }

                $validators = array_merge($validators, $model->getActiveValidators($attribute));
            }
        }

        return $validators;
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getAttributeLabel($attribute)
    {
        if (strpos($attribute, '.')) {
            $attributeParts = explode('.', $attribute);
            $neededAttribute = array_pop($attributeParts);
            return self::getNestedDocument($attribute)->getAttributeLabel($neededAttribute);
        }

        return parent::getAttributeLabel($attribute);
    }

    /**
     * Fill the model from the database
     * @param self $record
     * @param array $row
     * @throws InvalidConfigException
     */
    public static function populateRecord($record, $row)
    {
        $record->initDocuments();
        foreach ($row as $attribute => $value) {
            /* @var $value mixed */
            if (isset($record->children[$attribute])) {
                $document = $value;
                if ($record->children[$attribute]['type'] === MongoDBUtils::DOCUMENT_TYPE_SIMPLE) {
                    $document = $record->mapDocument($value, $record->children[$attribute]['class'], $attribute, false);
                } elseif ($record->children[$attribute]['type'] === MongoDBUtils::DOCUMENT_TYPE_LIST) {
                    $document = $record->mapDocumentList($value, $record->children[$attribute]['class'], $attribute, false);
                }

                $row[$attribute] = $document;
            }
        }

        parent::populateRecord($record, $row);
    }
}
