<?php

namespace mongodb\helpers;

/**
 * Class utils
 * @package mongodb\helpers
 */
class MongoDBUtils
{
    /**
     * @const string
     */
    public const METHOD_DOCUMENT_PREFIX = 'embed';

    /**
     * @const string
     */
    public const DOCUMENT_TYPE_SIMPLE = 'simple';
    public const DOCUMENT_TYPE_LIST = 'list';

    public static function composeDocumentDeclarationMethodName($name, $prefix = self::METHOD_DOCUMENT_PREFIX, $reverse = false)
    {
        if ($reverse) {
            $name = preg_replace_callback('/([^_])([A-Z0-9])/', function ($char) {
                return $char[1] . '_' . strtolower($char[2]);
            }, $name);

            return $prefix . $name;
        }

        $name = implode('', array_map('ucfirst', explode('_', $name)));

        return $prefix . ucfirst($name);
    }
}
