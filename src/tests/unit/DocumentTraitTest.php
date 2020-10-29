<?php

namespace mongodb\tests\unit;

use Codeception\Specify;
use Codeception\Test\Unit;
use mongodb\DocumentTrait;
use mongodb\tests\helpers\brokenModels\CollectionBad;
use mongodb\tests\helpers\brokenModels\CollectionWithoutInterface;
use mongodb\tests\helpers\FixtureHelper;
use mongodb\tests\helpers\models\CustomCollection;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

/**
 * Class DocumentTraitTest
 * @package mongodb\tests\unit
 */
class DocumentTraitTest extends Unit
{
    use Specify;
    use DocumentTrait;

    public $attribute_parent;

    public function embedAttributeParent()
    {
        return new CustomCollection();
    }

    public function embedSubDocument2()
    {
        return 'WRONG';
    }

    /**
     * @return array
     */
    public function _fixtures()
    {
        return FixtureHelper::fixtureLoader([
        ]);
    }

    public function testMapDocument()
    {
        $this->specify('Valid Source', function () {
            $this->mapDocument([], CustomCollection::class, 'attribute_parent');
        });
    }

    public function testInvalidMapDocument()
    {
        $this->specify('Invalid Source', function () {
            $this->expectException(InvalidArgumentException::class);
            $this->mapDocument('WRONG', CollectionBad::class, 'attribute_parent');
        });
    }

    public function testValidIsDocument()
    {
        $this->specify('isDocument - true', function () {
            $this->mapDocument([], CustomCollection::class, 'attribute_parent');
            $this->assertEquals(true, $this->isDocument('attribute_parent', $this->embedAttributeParent()));
        });
    }

    public function testInvalidIsDocument1()
    {
        $this->specify('isDocument - false', function () {
            $this->expectException(InvalidConfigException::class);
            $this->assertEquals(true, $this->isDocument('attribute_parent', new CollectionWithoutInterface()));
        });
    }

    public function testInvalidIsDocument2()
    {
        $this->specify('isDocument - false', function () {
            $this->expectException(InvalidConfigException::class);
            $this->assertEquals(true, $this->isDocument('attribute_parent', [new CollectionWithoutInterface()]));
        });
    }

    public function testMapDocumentList()
    {
        $this->specify('Valid Source', function () {
            $this->mapDocumentList([[]], CustomCollection::class, 'attribute_parent');
            $this->mapDocumentList([[]], null, 'attribute_parent');
        });
    }

    public function testInvalidMapDocumentList()
    {
        $this->specify('Invalid Source', function () {
            $this->expectException(InvalidArgumentException::class);
            $this->mapDocumentList('WRONG', CollectionBad::class, 'attribute_parent');
        });
    }

    public function testGenerateAttributeLabel()
    {
        $this->specify('Functionality', function () {
            $model = new CustomCollection();
            $this->assertEquals('Test', $model->generateAttributeLabel('test'));
            $this->assertEquals('Simple Attribute String', $model->generateAttributeLabel('simple_attribute_string'));
            $this->assertEquals('Simple Attribute Number', $model->generateAttributeLabel('sub_document_1.simple_attribute_number'));
        });
    }
}
