<?php

namespace mongodb\tests\helpers;

use MongoDB\BSON\Binary;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use stdClass;

/**
 * Class MongoJsonParser
 * @package mongodb\tests\helpers
 */
class MongoJsonParser
{
    /**
     * @var array
     */
    protected $arr = [];

    /**
     * @var array
     */
    protected $attributesEmptyObject = [];

    /**
     * @const string
     */
    const OID_KEY = '$oid';

    /**
     * @const string
     */
    const DATETIME_KEY = '$milliseconds';

    /**
     * @const string
     */
    const BINARY_KEY = '$binary';

    /**
     * MongoJsonParser constructor.
     * @param $arr
     * @param $attributesEmptyObject array
     */
    public function __construct($arr, $attributesEmptyObject = [])
    {
        $this->arr = $arr;
        $this->attributesEmptyObject = $attributesEmptyObject;
    }

    /**
     * @param $key string
     * @return array
     */
    public function parse($key = null)
    {
        return $this->replaceElements($this->arr, $key);
    }

    /**
     * @param $arr
     * @return array|ObjectID|UTCDateTime|Binary|stdClass
     */
    protected function replaceElements($arr, $key = null)
    {
        if (is_array($arr)) {
            if (isset($arr[self::OID_KEY])) {
                // If $oid key found, convert to mongo ID
                try {
                    $objectID = new ObjectID($arr[self::OID_KEY]);

                    return $objectID;
                } catch (\Exception $e) {
                    return null;
                }
            } elseif (isset($arr[self::DATETIME_KEY])) {
                // Replace datetime objects
                $milliseconds = $arr[self::DATETIME_KEY];

                return new UTCDateTime($milliseconds);
            } elseif (isset($arr[self::BINARY_KEY])) {
                $binary = $arr[self::BINARY_KEY];

                return new Binary($binary, Binary::TYPE_GENERIC);
            } elseif (is_array($arr) && empty($arr) && in_array($key, $this->attributesEmptyObject)) {
                return new stdClass();
            }

            foreach ($arr as $key => $item) {
                /* @var $item mixed */
                if ($key !== self::OID_KEY && is_array($item)) {
                    $arr[$key] = $this->replaceElements($item, $key);
                }
            }
        }

        return $arr;
    }
}
