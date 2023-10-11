<?php

namespace Mongolid;

use Illuminate\Container\Container as IlluminateContainer;
use Mockery as m;
use Mongolid\Container\Container;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;
use ReflectionMethod;

class TestCase extends PHPUnitTestCase
{
    public function assertMongoQueryEquals($expectedQuery, $query)
    {
        $this->assertEquals($expectedQuery, $query, 'Queries are not equals');

        if (!is_array($expectedQuery)) {
            return;
        }

        foreach ($expectedQuery as $key => $value) {
            if (is_object($value)) {
                $this->assertInstanceOf(
                    $value::class,
                    $query[$key],
                    'Type of an object within the query is not equals'
                );

                if (method_exists($value, '__toString')) {
                    $this->assertEquals(
                        (string) $expectedQuery[$key],
                        (string) $query[$key],
                        'Object within the query is not equals'
                    );
                }
            }

            if (is_array($value)) {
                $this->assertMongoQueryEquals($value, $query[$key]);
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        Container::setContainer(new IlluminateContainer());
    }

    protected function tearDown(): void
    {
        Container::flush();
        m::close();

        parent::tearDown();
    }

    /**
     * Actually runs a protected method of the given object.
     *
     * @param object $obj
     * @param array  $args
     *
     * @return mixed
     */
    protected function callProtected($obj, string $method, array $args = [])
    {
        $methodObj = new ReflectionMethod($obj::class, $method);
        $methodObj->setAccessible(true);

        return $methodObj->invokeArgs($obj, $args);
    }

    /**
     * Set a protected property of an object.
     *
     * @param mixed  $obj      object Instance
     * @param string $property property name
     * @param mixed  $value    value to be set
     */
    protected function setProtected($obj, string $property, $value): void
    {
        $class = new ReflectionClass($obj);
        $property = $class->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }

    /**
     * Get a protected property of an object.
     *
     * @param mixed  $obj      object Instance
     * @param string $property property name
     *
     * @return mixed property value
     */
    protected function getProtected($obj, string $property)
    {
        $class = new ReflectionClass($obj);
        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($obj);
    }

    /**
     * Replace instance on Ioc
     */
    protected function instance(string $abstract, $instance)
    {
        Container::bind(
            $abstract,
            function () use ($instance) {
                return $instance;
            }
        );

        return $instance;
    }
}
