<?php

namespace Warpcode\Std\Tests;

use \Warpcode\Std\Arr;

class ArrTest  extends \PHPUnit\Framework\TestCase
{

    private $numeric_a = [
        0,1,2,3,4,5,6,7,8,9
    ];

    private $assoc_a = [
        'test1' => 'test1',
        'test2' => 'test2',
        'test3' => 'test3',
        'test4' => 'test4',
        'test5' => 'test5'
    ];



    /**
    * Test empty args to constructor
    */
    public function testConstruct(){
        new Arr();
        $this->assertTrue(true);

        $this->assertEquals(Arr::factory(), new Arr());
    }

    /**
    * Test the constructor with an array
    */
    public function testConstructWithArray(){
        new Arr($this->numeric_a);
        $this->assertTrue(true);

        $this->assertEquals(Arr::factory($this->numeric_a), new Arr($this->numeric_a));
    }

    /**
    * Test the constructor with another Arr instance
    */
    public function testConstructWithArrObject(){
        $obj = new Arr($this->numeric_a);
        new Arr($obj);
        $this->assertTrue(true);

        $this->assertEquals(Arr::factory(new Arr($this->numeric_a)), new Arr(new Arr($this->numeric_a)));
    }

    /**
    * Ensure invalid string arg produces exception
    * @expectedException \InvalidArgumentException
    */
    public function testConstructWithString(){
        new Arr('teststring');
    }

    /**
     * Tests various arrays to check if they are scalar
     */
    public function testIsScalar(){
        $this->assertTrue(Arr::factory([])->isScalar());

        $this->assertTrue(Arr::factory($this->numeric_a)->isScalar());

        $this->assertTrue(Arr::factory($this->assoc_a)->isScalar());

        $this->assertFalse(Arr::factory([-1 => 'test1', 0 => new Arr()])->isScalar());

        $this->assertFalse(Arr::factory([1 => 'test1', 0 => new Arr()])->isScalar());

        $this->assertTrue(Arr::factory([0 => 'test1', 2 => 'test2', 3 => null])->isScalar());

        $this->assertFalse(Arr::factory([0 => 'test1', 2 => 'test2', 3 => null])->isScalar(false));
    }

    /**
     * Various tests to make sure isIndexed successfull checks for indexed arrays
     * @return [type] [description]
     */
    public function testIsIndexed(){
        $this->assertTrue(Arr::factory([])->isIndexed());

        $this->assertTrue(Arr::factory($this->numeric_a)->isIndexed());

        $this->assertFalse(Arr::factory($this->assoc_a)->isIndexed());

        $this->assertFalse(Arr::factory([-1 => 'test1', 0 => 'test2'])->isIndexed());

        $this->assertFalse(Arr::factory([1 => 'test1', 0 => 'test2'])->isIndexed());

        $this->assertFalse(Arr::factory([0 => 'test1', 2 => 'test2'])->isIndexed());

        $this->assertTrue(Arr::factory([0 => 'test1', 2 => 'test2'])->isIndexed(false));
    }


    /**
     * Test whether an index exists
     */
    public function testHasIndex(){
        $arr = new Arr($this->numeric_a);

        for($i=0; $i < count($this->numeric_a); ++$i){
            $this->assertTrue($arr->hasIndex($i));
        }

        for($i=0; $i > -count($this->numeric_a); --$i){
            $this->assertTrue($arr->hasIndex($i));
        }

    }

    /**
    * Test to make sure that when we pass an array, it remains an array
    */
    public function testToArray(){
        $arr = new Arr($this->assoc_a);

        //test to make sure we have an array
        $this->assertTrue(is_array($arr->toArray()));

        //test to make sure the array remains identical
        $this->assertEquals($this->assoc_a, $arr->toArray());
    }

    /**
    * Test the count function of the array class
    */
    public function testCount(){
        $arr = new Arr($this->numeric_a);

        //test direct call to count
        $this->assertEquals(count($this->numeric_a), $arr->count());

        //test call to length
        $this->assertEquals(count($this->numeric_a), $arr->length());

        //test countable implementation
        $this->assertEquals(count($this->numeric_a), count($arr));
    }

    /**
     * Test the slice function with no length parameter set
     */
    public function testSliceNoLength(){
        $arr = new Arr($this->numeric_a);
        $arr2 = new Arr($this->assoc_a);

        $this->assertEquals($arr->slice(5)->toArray(), range(5,9));
        $this->assertEquals($arr->slice(-1)->toArray(), [9]);
        $this->assertEquals($arr2->slice(3)->toArray(), ['test4' => 'test4', 'test5' => 'test5']);
        $this->assertEquals($arr2->slice(-1, NULL, true)->toArray(), ['test5' => 'test5']);
    }

    /**
     * Test the slice function with a length parameter
     */
    public function testSliceWithLength(){
        $arr = new Arr($this->numeric_a);
        $arr2 = new Arr($this->assoc_a);

        $this->assertEquals($arr->slice(5, 1)->toArray(), [5]);
        $this->assertEquals($arr->slice(4, 2)->toArray(), [4,5]);
        $this->assertEquals($arr2->slice(3, 1)->toArray(), ['test4' => 'test4']);
        $this->assertEquals($arr2->slice(3, 2)->toArray(), ['test4' => 'test4', 'test5' => 'test5']);
    }
}
