<?php

namespace mongodb;

use mongodb\helpers\MongoDBUtils;
use mongodb\mongodb\ActiveRecord;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

/**
 * Trait DocumentTrait
 * @package mongodb
 */
trait DocumentTrait
{
    /**
     * @var array
     */
    private $children = [];

    /**
     * @var bool
     */
    private $saving = false;

    /**
     * @var null|ActiveRecord
     */
    public $parent = null;

    /**
     * Initial data for the document
     * @var array|null
     */
    public $source = null;

    /**
     * Strict mode detect invalid attributes
     * @var bool
     */
    public $strictMode = true;

    /**
     * @throws InvalidConfigException
     */
    public function initDocuments()
    {
        $pattern = sprintf('/%s[A-Z]/', MongoDBUtils::METHOD_DOCUMENT_PREFIX);
        $methods = preg_grep($pattern, get_class_methods($this));
        foreach ($methods as $method) {
            /* @var $method string */

            $attribute = str_replace(
                MongoDBUtils::METHOD_DOCUMENT_PREFIX . '_',
                '',
                MongoDBUtils::composeDocumentDeclarationMethodName($method, '', true)
            );

            $this->children[$attribute] = null;
            $document = $this->$method();
            if (is_array($document)) {
                $list = [];
                foreach ($document as $key => $doc) {
                    /* @var $doc ActiveRecord */
                    if ($this->isDocument($attribute, $doc)) {
                        $list[$key] = $doc;
                    }
                }

                $this->$attribute = $list;
            } elseif ($this->isDocument($attribute, $document)) {
                $this->$attribute = $document;
            }
        }
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that document objects can be accessed like properties.
     *
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute()
     */
    public function __get($name)
    {
        if (strpos($name, '.')) {
            $nameParts = explode('.', $name);
            $attribute = array_pop($nameParts);

            return $this->getNestedDocument($name)->$attribute;
        }

        $nameParsed = MongoDBUtils::composeDocumentDeclarationMethodName($name, '', true);
        if (isset($this->children[$nameParsed])) {
            $name = $nameParsed;
        }

        return parent::__get($name);
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that document objects can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value)
    {
        if ($this->saving) {
            parent::__set($name, $value);
            return;
        }

        if (strpos($name, '.')) {
            $nameParts = explode('.', $name);
            $attribute = array_pop($nameParts);
            $model = $this->getNestedDocument($name);
            $model->$attribute = $value;
        } else {
            $nameParsed = MongoDBUtils::composeDocumentDeclarationMethodName($name, '', true);
            if (isset($this->children[$nameParsed])) {
                $name = $nameParsed;
            }

            if (is_array($value) && isset($this->children[$name]) && $this->children[$name]['type'] === MongoDBUtils::DOCUMENT_TYPE_SIMPLE) {
                $model = $this->getNestedDocument($name);
                foreach ($model->attributes as $key => $val) {
                    /* @var $val mixed */
                    if (!in_array($key, ['_id']) && $model->parent !== null) {
                        $model->$key = $value[$key] ?? $val;
                    }
                }

                $value = $model;
            }

            if (method_exists(get_parent_class($this), '__set')) {
                parent::__set($name, $value);
            }
        }
    }

    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified document object.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
        $name = MongoDBUtils::composeDocumentDeclarationMethodName($name, '', true);
        if (isset($this->children[$name])) {
            unset($this->children[$name]);
        }

        parent::__unset($name);
    }

    /**
     * @param $attribute string
     * @param $documents ActiveRecord|ActiveRecord[]
     * @return boolean
     * @throws InvalidConfigException
     */
    public function isDocument($attribute, $documents)
    {
        $method = MongoDBUtils::composeDocumentDeclarationMethodName($attribute);
        if (is_array($documents)) {
            foreach ($documents as $document) {
                /* @var $document ActiveRecord */
                if (!$document instanceof DocumentInterface) {
                    throw new InvalidConfigException("Mapping declaration '" . get_class($this) . "::{$method}()' should return instance of '" . DocumentInterface::class . "'");
                }
            }
        } elseif (!$documents instanceof DocumentInterface) {
            throw new InvalidConfigException("Mapping declaration '" . get_class($this) . "::{$method}()' should return instance of '" . DocumentInterface::class . "'");
        }

        return true;
    }

    /**
     * Declare document.
     * @param array $source
     * @param $target
     * @param $attribute
     * @param bool $isNewRecord
     * @param array $config
     * @return ActiveRecord|object
     * @throws InvalidConfigException
     */
    public function mapDocument($source, $target, $attribute, $isNewRecord = true, array $config = [])
    {
        $source = $source ?? [];
        if (!is_array($source)) {
            throw new InvalidArgumentException("Source value for document should be an array.");
        }

        if (!isset($this->children[$attribute])) {
            $this->children[$attribute] = ['class' => $target, 'type' => MongoDBUtils::DOCUMENT_TYPE_SIMPLE];
        }

        $document = Yii::createObject(array_merge(
            [
                'class' => $target,
                'source' => $source,
                'parent' => $this
            ],
            $config
        ));

        if ($this->strictMode) {
            foreach ($source as $attribute => $value) {
                /* @var $value mixed */
                if (strpos($attribute, '.')) {
                    $nameParts = explode('.', $attribute);
                    $attribute = array_pop($nameParts);
                }

                if (!$document->hasAttribute($attribute)) {
                    throw new InvalidConfigException("Attribute [{$attribute}] not allowed in strict mode");
                }
            }
        }

        $document->setAttributes($source);
        if (!$isNewRecord) {
            $document->setOldAttributes($source);
        }

        return $document;
    }

    /**
     * Declares document list of objects.
     * @param $source
     * @param $target
     * @param $attribute
     * @param bool $isNewRecord
     * @param array $config
     * @return array
     * @throws InvalidConfigException
     */
    public function mapDocumentList($source, $target, $attribute, $isNewRecord = true, array $config = [])
    {
        if (!is_array($source)) {
            throw new InvalidArgumentException("Source value for document should be an array.");
        }

        if (!isset($this->children[$attribute])) {
            $this->children[$attribute] = ['class' => $target, 'type' => MongoDBUtils::DOCUMENT_TYPE_LIST];
        }

        $list = [];
        foreach ($source as $element) {
            /* @var $element array */
            if (isset($target) && class_exists($target)) {
                $list[] = $this->mapDocument($element, $target, $attribute, $isNewRecord, $config);
            } else {
                $list[] = $element;
            }
        }

        return $list;
    }

    /**
     * Fills up own fields by values fetched from documents ActiveRecord.
     */
    private function documentsToArray()
    {
        foreach ($this->attributes as $attribute => $value) {
            /* @var $value ActiveRecord|mixed */
            if (isset($this->children[$attribute])) {
                $this->children[$attribute]['backup'] = $value;
                if ($value instanceof ActiveRecord) {
                    $value->setOldAttributes($value->attributes);
                    $this->$attribute = $value->toArray();
                } elseif (is_array($value)) {
                    foreach ($value as $subAttribute => $subValue) {
                        /* @var $subValue ActiveRecord|mixed */
                        if ($subValue instanceof ActiveRecord) {
                            $subValue->setOldAttributes($subValue->attributes);
                            $value[$subAttribute] = $subValue->toArray();
                        }
                    }

                    $this->$attribute = $value;
                }
            }
        }
    }

    /**
     * Rollback documents in array to ActiveRecord.
     */
    private function arrayToDocuments()
    {
        foreach ($this->children as $attribute => $child) {
            /* @var $document ActiveRecord|array */
            $this->$attribute = $child['backup'];
            unset($this->children[$attribute]['backup']);
        }
    }

    /**
     * @param $path
     * @return ActiveRecord|null|$this
     */
    private function getNestedDocument($path)
    {
        $model = $this;
        $attribute = $path;
        $pathParts = [];
        if (strpos($path, '.')) {
            $pathParts = explode('.', $path);
            $attribute = array_shift($pathParts);
        }

        do {
            if (isset($model[$attribute]) && $model[$attribute] instanceof ActiveRecord) {
                $model = $model[$attribute];
            }

            $attribute = array_shift($pathParts);
        } while (isset($attribute)); // @codeCoverageIgnore
        return $model;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function generateAttributeLabel($attribute)
    {
        if (strpos($attribute, '.')) {
            $attributeParts = explode('.', $attribute);
            $attribute = array_pop($attributeParts);
        }

        return parent::generateAttributeLabel($attribute);
    }
}
