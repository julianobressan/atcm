<?php

use Test\Data\Models\ExampleModel;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class ModelBaseTest extends TestCase
{
    public function testCreateNewModel()
    {
        
        $example = new ExampleModel();
        $example->foo = 'bar';

        assertEquals('bar', $example->foo);

        /*
        assertEquals("INSERT INTO example_model () VALUES ();", $example->save());

        $example2 = new ExampleModel();
        $example2->name = "Foo";
        $example2->lastName = "Bar";
        $example2->age = 26;
        $example2->activeAccount = true;
        $example2->height = 1.81;

        
        assertEquals(
            "INSERT INTO example_model (name, last_name, age, active_account, height) VALUES ('Foo', 'Bar', 26, 1, 1.81);", 
            $example2->save()
        );

        $example3 = new ExampleModel();
        $example3->name = "Foo";
        $example3->id = 1;

        assertEquals(
            "UPDATE example_model SET name = 'Foo' WHERE id = 1;", 
            $example3->save()
        );*/
    }

    
}