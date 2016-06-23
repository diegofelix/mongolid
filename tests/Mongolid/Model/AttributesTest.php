<?php
namespace Mongolid\Model;

use TestCase;
use Mockery as m;
use Mongolid\Container\Ioc;
use stdClass;

class AttributesTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testShouldHaveDynamicSetters()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $childObj = new stdClass;

        // Assert
        $model->name = 'John';
        $model->age = 25;
        $model->child = $childObj;
        $this->assertAttributeEquals(
            [
                'name' => 'John',
                'age' => 25,
                'child' => $childObj,
            ],
            'attributes',
            $model
        );
    }

    public function testShouldHaveDynamicGetters()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $childObj = new stdClass;
        $this->setProtected(
            $model,
            'attributes',
            [
                'name' => 'John',
                'age' => 25,
                'child' => $childObj,
            ]
        );

        // Assert
        $this->assertEquals('John', $model->name);
        $this->assertEquals(25, $model->age);
        $this->assertEquals($childObj, $model->child);
        $this->assertEquals(null, $model->nonexistant);
    }

    public function testShouldCheckIfAttributeIsSet()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $this->setProtected(
            $model,
            'attributes',
            ['name' => 'John',]
        );

        // Assert
        $this->assertTrue(isset($model->name));
        $this->assertFalse(isset($model->nonexistant));
    }

    public function testShouldUnsetAttributes()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $this->setProtected(
            $model,
            'attributes',
            [
                'name' => 'John',
                'age' => 25,
            ]
        );

        // Assert
        unset($model->age);
        $this->assertAttributeEquals(
            [
                'name' => 'John',
            ],
            'attributes',
            $model
        );
    }

    /**
     * @dataProvider getFillableOptions
     */
    public function testShouldFillOnlyPermittedAttributes(
        $fillable,
        $guarded,
        $input,
        $expected
    ) {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $this->setProtected($model, 'fillable', $fillable);
        $this->setProtected($model, 'guarded', $guarded);

        // Assert
        $model->fill($input);
        $this->assertAttributeEquals($expected, 'attributes', $model);
    }

    public function testShouldForceFillAttributes()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $input = [
            'name'                => 'Josh',
            'notAllowedAttribute' => true
        ];

        // Act
        $model->fill($input, true);

        // Assert
        $this->assertTrue($model->notAllowedAttribute);
    }

    public function testShouldBeCastableToArray()
    {
        // Arrange
        $model = new class {
            use Attributes;
        };

        $model->name = 'John';
        $model->age = 25;

        // Assert
        $this->assertEquals(
            ['name' => 'John', 'age' => 25],
            $model->toArray()
        );
    }

    public function getFillableOptions()
    {
        return [
            // -----------------------------
            '$fillable = []; $guarded = []' => [
                'fillable' => [],
                'guarded' => [],
                'input' => [
                    'name' => 'John',
                    'age' => 25,
                    'sex' => 'male',
                ],
                'expected' => [
                    'name' => 'John',
                    'age' => 25,
                    'sex' => 'male',
                ],
            ],

            // -----------------------------
            '$fillable = ["name"]; $guarded = []' => [
                'fillable' => ['name'],
                'guarded' => [],
                'input' => [
                    'name' => 'John',
                    'age' => 25,
                    'sex' => 'male',
                ],
                'expected' => [
                    'name' => 'John',
                ],
            ],

            // -----------------------------
            '$fillable = []; $guarded = []' => [
                'fillable' => [],
                'guarded' => ['sex'],
                'input' => [
                    'name' => 'John',
                    'age' => 25,
                    'sex' => 'male',
                ],
                'expected' => [
                    'name' => 'John',
                    'age' => 25,
                ],
            ],

            // -----------------------------
            '$fillable = ["name", "sex"]; $guarded = ["sex"]' => [
                'fillable' => ['name', 'sex'],
                'guarded' => ['sex'],
                'input' => [
                    'name' => 'John',
                    'age' => 25,
                    'sex' => 'male',
                ],
                'expected' => [
                    'name' => 'John',
                ],
            ],
        ];
    }
}
