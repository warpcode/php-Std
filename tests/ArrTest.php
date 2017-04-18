<?php

namespace Warpcode\Std\Tests;

use \Warpcode\Std\Arr;

class ArrTest  extends \PHPUnit\Framework\TestCase
{

    /**
    * Test empty args to constructor
    */
    public function testConstruct(){
        new Arr();
        $this->assertTrue(true);
    }

    /**
    * Test the constructor with an array
    */
    public function testConstructWithArray(){
        $arr = range(0,9);
        new Arr($arr);
        $this->assertTrue(true);
    }

    /**
    * Test the constructor with another Arr instance
    */
    public function testConstructWithArrObject(){
        $obj = new Arr(['test array']);
        new Arr($obj);
        $this->assertTrue(true);
    }

    /**
    * Ensure invalid string arg produces exception
    * @expectedException \InvalidArgumentException
    */
    public function testConstructWithString(){
        new Arr('teststring');
    }

    /**
    * Test to make sure that when we pass an array, it remains an array
    */
    public function testToArray(){
        $arr = new Arr([]);

        //test to make sure we have an array
        $this->assertTrue(is_array($arr->toArray()));

        //test to make sure the array remains identical
        $this->assertEquals([], $arr->toArray());
    }

    /**
    * Test the count function of the array class
    */
    public function testCount(){
        $array = range(0,9);
        $arr = new Arr($array);

        //test direct call to count
        $this->assertEquals(count($array), $arr->count());

        //test countable implementation
        $this->assertEquals(count($array), count($arr));
    }

    /**
     * Test the slice function with no length parameter set
     */
    public function testSliceNoLength(){
        $arr = new Arr(range(0,9));

        $array = ['test1', 'test2', 'test3', 'test4', 'test5'];
        $arr2 = new Arr(array_combine($array, $array));

        $this->assertEquals($arr->slice(5)->toArray(), range(5,9));
        $this->assertEquals($arr2->slice(3)->toArray(), ['test4' => 'test4', 'test5' => 'test5']);
    }

    /**
     * Test the slice function with a length parameter
     */
    public function testSliceWithLength(){
        $arr = new Arr(range(0,9));

        $array = ['test1', 'test2', 'test3', 'test4', 'test5'];
        $arr2 = new Arr(array_combine($array, $array));

        $this->assertEquals($arr->slice(5, 1)->toArray(), [5]);
        $this->assertEquals($arr->slice(4, 2)->toArray(), [4,5]);
        $this->assertEquals($arr2->slice(3, 1.1)->toArray(), ['test4' => 'test4']);
        $this->assertEquals($arr2->slice(3, 2)->toArray(), ['test4' => 'test4', 'test5' => 'test5']);
    }
}
