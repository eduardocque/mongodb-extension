<?php

namespace mongodb;

use mongodb\mongodb\ActiveRecord;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class Validator
 * @package common\extensions\mongodb
 */
class Validator extends \yii\validators\Validator
{
    /**
     * @var bool whether to add an error message to embedded source attribute instead of embedded name itself.
     */
    public $fullPath = true;

    /**
     * Init method
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @param ActiveRecord $model
     * @param string $attribute
     * @throws InvalidConfigException
     */
    public function validateAttribute($model, $attribute)
    {
        if (!($model instanceof DocumentInterface)) {
            throw new InvalidConfigException(sprintf('Owner model must implement "%s" interface.', DocumentInterface::class));
        }

        $documentAttribute = $model->$attribute;
        if (is_array($documentAttribute)) {
            foreach ($documentAttribute as $key => $documentModel) {
                /* @var $documentModel ActiveRecord */
                if ($documentModel instanceof Model && !$documentModel->validate()) {
                    foreach ($documentModel->errors as $errorKey => $embError) {
                        /* @var $embError array */
                        foreach ($embError as $message) {
                            /* @var $message string */
                            if ($this->fullPath) {
                                $this->addError($model, sprintf('%s.%s.%s', $attribute, $key, $errorKey), $message);
                            } else {
                                $this->addError($documentAttribute[$key], $errorKey, $message);
                            }
                        }
                    }
                }
            }
        } else {
            if (!isset($documentAttribute) || !($documentAttribute instanceof Model)) {
                throw new InvalidConfigException('Document object must be an instance or descendant of "' . Model::class . '".');
            }

            if (!$documentAttribute->validate()) {
                foreach ($documentAttribute->errors as $key => $embError) {
                    /* @var $embError array */
                    foreach ($embError as $message) {
                        /* @var $message string */
                        if ($this->fullPath) {
                            $this->addError($model, sprintf('%s.%s', $attribute, $key), $message);
                        } else {
                            $this->addError($documentAttribute, $key, $message);
                        }
                    }
                }
            }
        }
    }
}
