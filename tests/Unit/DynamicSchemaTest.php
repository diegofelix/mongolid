<?php

namespace Mongolid;

use Mockery as m;
use Mongolid\Schema\DynamicSchema;
use Mongolid\Schema\Schema;

class DynamicSchemaTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function testShouldExtendSchema(): void
    {
        // Arrange
        $schema = new DynamicSchema();

        // Assert
        $this->assertInstanceOf(Schema::class, $schema);
    }

    public function testShouldBeDynamic(): void
    {
        // Arrange
        $schema = new DynamicSchema();

        // Assert
        $this->assertEquals(true, $schema->dynamic);
    }
}
