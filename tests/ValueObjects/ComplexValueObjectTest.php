<?php


namespace Runn\tests\ValueObjects\ComplexValueObject;

use Runn\Core\ObjectAsArrayInterface;
use Runn\ValueObjects\ComplexValueObject;
use Runn\ValueObjects\Errors\ComplexValueObjectErrors;
use Runn\ValueObjects\Errors\EmptyFieldClass;
use Runn\ValueObjects\Errors\InvalidField;
use Runn\ValueObjects\Errors\InvalidFieldClass;
use Runn\ValueObjects\Errors\InvalidFieldValue;
use Runn\ValueObjects\Errors\MissingField;
use Runn\ValueObjects\Values\IntValue;
use Runn\ValueObjects\Values\StringValue;
use Runn\ValueObjects\ValueObjectInterface;

class testComplexValueObject extends ComplexValueObject {
    protected static $schema = [
        'foo' => ['class' => IntValue::class]
    ];
}

class ComplexValueObjectTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyComplexObjectEmptyData()
    {
        $object = new class extends ComplexValueObject {};
        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(0, count($object));
    }

    public function testEmptyComplexObjectInvalidKey()
    {
        try {
            $object = new class(['foo' => 42]) extends ComplexValueObject {};
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(1, $errors);

            $this->assertInstanceOf(InvalidField::class, $errors[0]);
            $this->assertSame('foo', $errors[0]->getField());
            $this->assertSame('Invalid complex value object field key: "foo"', $errors[0]->getMessage());

            return;
        }
        $this->fail();
    }

    public function testComplexObjectMissingField()
    {
        try {
            $object = new class extends ComplexValueObject {
                protected static $schema = [
                    'foo' => ['class' => IntValue::class]
                ];
            };
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(1, $errors);

            $this->assertInstanceOf(MissingField::class, $errors[0]);
            $this->assertSame('foo', $errors[0]->getField());
            $this->assertSame('Missing complex value object field "foo"', $errors[0]->getMessage());

            return;
        }
        $this->fail();
    }

    public function testValidConstructOneField()
    {
        $object = new class(['foo' => 42]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class]
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(1, count($object));
        $this->assertInstanceOf(IntValue::class, $object->foo);
        $this->assertSame(42, $object->foo->getValue());

        $object = new class(['foo' => new IntValue(42)]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class]
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(1, count($object));
        $this->assertInstanceOf(IntValue::class, $object->foo);
        $this->assertSame(42, $object->foo->getValue());
    }

    public function testValidConstructManyFields()
    {
        $object = new class(['foo' => 42, 'bar' => 'baz']) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class],
                'bar' => ['class' => StringValue::class],
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(2, count($object));
        $this->assertInstanceOf(IntValue::class, $object->foo);
        $this->assertSame(42, $object->foo->getValue());
        $this->assertInstanceOf(StringValue::class, $object->bar);
        $this->assertSame('baz', $object->bar->getValue());

        $object = new class(['foo' => new IntValue(42), 'bar' => new StringValue('baz')]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class],
                'bar' => ['class' => StringValue::class],
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(2, count($object));
        $this->assertInstanceOf(IntValue::class, $object->foo);
        $this->assertSame(42, $object->foo->getValue());
        $this->assertInstanceOf(StringValue::class, $object->bar);
        $this->assertSame('baz', $object->bar->getValue());
    }

    public function testValidConstructWithDefault()
    {
        $object = new class(['bar' => 'baz']) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class, 'default' => 42],
                'bar' => ['class' => StringValue::class],
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(2, count($object));
        $this->assertInstanceOf(IntValue::class, $object->foo);
        $this->assertSame(42, $object->foo->getValue());
        $this->assertInstanceOf(StringValue::class, $object->bar);
        $this->assertSame('baz', $object->bar->getValue());
    }

    public function testValidConstructWithDefaultNull()
    {
        $object = new class(['bar' => 'baz']) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class, 'default' => null],
                'bar' => ['class' => StringValue::class],
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(2, count($object));
        $this->assertNull($object->foo);
        $this->assertInstanceOf(StringValue::class, $object->bar);
        $this->assertSame('baz', $object->bar->getValue());
    }

    public function testValidConstructWithDefaultValue()
    {
        $object = new class(['foo' => null, 'bar' => 'baz']) extends ComplexValueObject
        {
            protected static $schema = [
                'foo' => ['class' => IntValue::class, 'default' => null],
                'bar' => ['class' => StringValue::class],
            ];
        };

        $this->assertInstanceOf(ComplexValueObject::class, $object);
        $this->assertInstanceOf(ObjectAsArrayInterface::class, $object);
        $this->assertInstanceOf(ValueObjectInterface::class, $object);
        $this->assertEquals(2, count($object));

        $this->assertNull($object->foo);
        $this->assertInstanceOf(StringValue::class, $object->bar);
        $this->assertSame('baz', $object->bar->getValue());
    }

    public function testValidConstructWithoutDefault()
    {
        try {
            $object = new class(['bar' => 'baz']) extends ComplexValueObject {
                protected static $schema = [
                    'foo' => ['class' => IntValue::class],
                    'bar' => ['class' => StringValue::class],
                ];
            };
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(1, $errors);

            $this->assertInstanceOf(MissingField::class, $errors[0]);
            $this->assertSame('foo', $errors[0]->getField());
            $this->assertSame('Missing complex value object field "foo"', $errors[0]->getMessage());

            return;
        }
        $this->fail();
    }

    public function testInvalidFieldConstruct()
    {
        try {
            $object = new class(['baz' => 'blablabla']) extends ComplexValueObject {
                protected static $schema = [
                    'foo' => ['class' => IntValue::class],
                    'bar' => ['class' => StringValue::class],
                ];
            };
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(3, $errors);

            $this->assertInstanceOf(InvalidField::class, $errors[0]);
            $this->assertSame('baz', $errors[0]->getField());
            $this->assertSame('Invalid complex value object field key: "baz"', $errors[0]->getMessage());

            $this->assertInstanceOf(MissingField::class, $errors[1]);
            $this->assertSame('foo', $errors[1]->getField());
            $this->assertSame('Missing complex value object field "foo"', $errors[1]->getMessage());

            $this->assertInstanceOf(MissingField::class, $errors[2]);
            $this->assertSame('bar', $errors[2]->getField());
            $this->assertSame('Missing complex value object field "bar"', $errors[2]->getMessage());

            return;
        }
        $this->fail();
    }

    public function testEmptyFieldClassConstruct()
    {
        try {
            $object = new class(['foo' => 42]) extends ComplexValueObject {
                protected static $schema = [
                    'foo' => ['wtf' => IntValue::class]
                ];
            };
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(2, $errors);

            $this->assertInstanceOf(EmptyFieldClass::class, $errors[0]);
            $this->assertSame('foo', $errors[0]->getField());
            $this->assertSame('Empty complex value object field "foo" class', $errors[0]->getMessage());

            $this->assertInstanceOf(MissingField::class, $errors[1]);
            $this->assertSame('foo', $errors[1]->getField());
            $this->assertSame('Missing complex value object field "foo"', $errors[1]->getMessage());

            return;
        }
        $this->fail();
    }

    /*
     * @expectedException \Runn\ValueObjects\Exception
     * @expectedExceptionMessage Invalid complex value object field "foo" class
     */
    public function testInvalidFieldClassConstruct()
    {
        try {
            $object = new class(['foo' => 42]) extends ComplexValueObject {
                protected static $schema = [
                    'foo' => ['class' => \stdClass::class]
                ];
            };
        } catch (ComplexValueObjectErrors $errors) {
            $this->assertCount(2, $errors);

            $this->assertInstanceOf(InvalidFieldClass::class, $errors[0]);
            $this->assertSame('foo', $errors[0]->getField());
            $this->assertSame(\stdClass::class, $errors[0]->getClass());
            $this->assertSame('Invalid complex value object field "foo" class', $errors[0]->getMessage());

            $this->assertInstanceOf(MissingField::class, $errors[1]);
            $this->assertSame('foo', $errors[1]->getField());
            $this->assertSame('Missing complex value object field "foo"', $errors[1]->getMessage());

            return;
        }
        $this->fail();
    }

    /**
     * @expectedException \Runn\ValueObjects\Exception
     * @expectedExceptionMessage Can not set field "foo" value because of value object is constructed
     */
    public function testImmutable()
    {
        $object = new testComplexValueObject(['foo' => 42]);
        $this->assertSame(42, $object->foo->getValue());

        $object->foo = 13;
    }

    public function testGetValue()
    {
        $object = new class(['value' => 42]) extends ComplexValueObject {
            protected static $schema = [
                'value' => ['class' => IntValue::class]
            ];
        };
        $this->assertInstanceOf(IntValue::class, $object->value);
        $this->assertSame(42, $object->value->getValue());
        $this->assertEquals(['value' => 42], $object->getValue());
    }

    public function testIsSame()
    {
        $object1 = new testComplexValueObject(['foo' => 42]);
        $this->assertTrue($object1->isSame($object1));

        $object2 = new class(['foo' => 42]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class]
            ];
        };
        $this->assertFalse($object1->isSame($object2));
        $this->assertFalse($object2->isSame($object1));

        $object2 = new testComplexValueObject(['foo' => 24]);
        $this->assertFalse($object1->isSame($object2));
        $this->assertFalse($object2->isSame($object1));

        $object2 = new testComplexValueObject(['foo' => 42]);
        $this->assertTrue($object1->isSame($object2));
        $this->assertTrue($object2->isSame($object1));
    }

    public function testJson()
    {
        $object = new class extends ComplexValueObject {};
        $this->assertSame('[]', json_encode($object));

        $object = new class(['foo' => new IntValue(42), 'bar' => new StringValue('baz')]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class],
                'bar' => ['class' => StringValue::class],
            ];
        };
        $this->assertSame('{"foo":42,"bar":"baz"}', json_encode($object));

        $object = new class(['foo' => new IntValue(42)]) extends ComplexValueObject {
            protected static $schema = [
                'foo' => ['class' => IntValue::class],
                'bar' => ['class' => StringValue::class, 'default' => null],
            ];
        };
        $this->assertSame('{"foo":42}', json_encode($object));
    }

}