<?php

namespace mongodb\tests\unit\helpers;

use Codeception\Specify;
use Codeception\Test\Unit;
use mongodb\helpers\MongoDBUtils;
use mongodb\tests\helpers\FixtureHelper;

/**
 * Class MongoDBUtilsTest
 * @package common\extensions\mongodb\tests\unit
 */
class MongoDBUtilsTest extends Unit
{
    use Specify;

    /**
     * @return array
     */
    public function _fixtures()
    {
        return FixtureHelper::fixtureLoader([
        ]);
    }

    /**
     * Test functionality.
     */
    public function testComposeDocumentDeclarationMethodName()
    {
        $this->specify('Functionality', function () {
            $this->assertEquals('embedSubDocument1', MongoDBUtils::composeDocumentDeclarationMethodName('sub_document_1'));
            $this->assertEquals('embedSubDocument', MongoDBUtils::composeDocumentDeclarationMethodName('sub_document'));
            $this->assertEquals('embedSubDocument1', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument_1'));
            $this->assertEquals('embedSubDocument', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument'));
        });

        $this->specify('Functionality Reverse', function () {
            $this->assertEquals('sub_document_1', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument1', '', true));
            $this->assertEquals('sub_document', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument', '', true));
            $this->assertEquals('sub_document_1', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument1', '', true));
            $this->assertEquals('sub_document', MongoDBUtils::composeDocumentDeclarationMethodName('subDocument', '', true));
            $this->assertEquals('sub_document', MongoDBUtils::composeDocumentDeclarationMethodName('sub_document', '', true));
            $this->assertEquals('sub_document_1', MongoDBUtils::composeDocumentDeclarationMethodName('sub_document_1', '', true));
        });
    }
}
