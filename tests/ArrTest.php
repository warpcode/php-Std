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
     * Tests the is empty method
     */
    public function testIsEmpty(){
        $this->assertTrue(Arr::factory([])->isEmpty());

        $this->assertFalse(Arr::factory([null])->isEmpty());

        $this->assertFalse(Arr::factory($this->numeric_a)->isEmpty());

        $this->assertFalse(Arr::factory($this->assoc_a)->isEmpty());
    }

    /**
     * Tests various arrays to check if they are scalar
     */
    public function testIsMultiDimensional(){
        $this->assertFalse(Arr::factory([])->isMultiDimensional());

        $this->assertFalse(Arr::factory($this->numeric_a)->isMultiDimensional());

        $this->assertFalse(Arr::factory($this->assoc_a)->isMultiDimensional());

        $this->assertFalse(Arr::factory([-1 => 'test1', 0 => new Arr()])->isMultiDimensional());

        $this->assertTrue(Arr::factory([1 => 'test1', 0 => []])->isMultiDimensional());
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
     * Tests to make sure the correct index array is returned
     */
    public function testGetIndexesWithAssocArray(){
        $this->assertEquals(Arr::factory($this->assoc_a)->getIndexes()->toArray(), range(0, count($this->assoc_a) - 1));
    }

    /**
     * Tests to make sure the correct index array is returned
     */
    public function testGetIndexesWithEmptyArray(){
        $this->assertEquals(Arr::factory([])->getIndexes()->toArray(), []);
    }

    /**
     * Various tests to make sure isIndexed successfull checks for indexed arrays
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
     * @expectedException \InvalidArgumentException
     */
    public function testHasIndexWithNoneNumericIndex(){
        Arr::factory($this->numeric_a)->hasIndex('test_string');
    }

    /**
     * Test whether an index exists
     */
    public function testHasIndexWithEmptyArray(){
        $this->assertFalse(Arr::factory([])->hasIndex(0));
    }

    /**
     * Test whether an index exists
     */
    public function testHasIndexWithNoneExistantIndex(){
        $this->assertFalse(Arr::factory($this->numeric_a)->hasIndex(count($this->numeric_a)));
    }

    /**
     * Test whether an index exists
     */
    public function testHasIndexWithValidIndexes(){
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

    /**
     * Force and invalid numeric length parameter
     * @expectedException \InvalidArgumentException
     */
    public function testChunkWithInvalidLengthBelow0(){
        Arr::Factory($this->numeric_a)->chunk(-1);
    }

    /**
     * Force and invalid numeric length parameter
     * @expectedException \InvalidArgumentException
     */
    public function testChunkWithInvalidLengthString(){
        Arr::Factory($this->numeric_a)->chunk('Not a number');
    }

    /**
     * Force and invalid numeric length parameter
     */
    public function testChunkWithEmptyArray(){
        $this->assertEquals(Arr::Factory([])->chunk(5), null);
    }

    /**
     * Force and invalid numeric length parameter
     */
    public function testChunk(){
        $this->assertEquals(Arr::Factory($this->numeric_a)->chunk(2), array_chunk($this->numeric_a, 2));
    }

    /**
     * Force and invalid numeric length parameter
     */
    public function testChunkWithPreserveKeys(){
        $this->assertEquals(Arr::Factory($this->numeric_a)->chunk(2, true), array_chunk($this->numeric_a, 2, true));
    }

    /**
     * Test chunk columns in normal use
     * @expectedException \InvalidArgumentException
     */
    public function testChunkColumnsWith0ItemArrayWithInvalidColumnsArg(){
        Arr::factory()->chunkColumns('test');
    }

    /**
     * Test chunk columns in normal use
     * @expectedException \InvalidArgumentException
     */
    public function testChunkColumnsWith0ItemArrayWith0Columns(){
        Arr::factory()->chunkColumns(0);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith0ItemArrayTo3Columns(){
        $this->assertEquals(Arr::factory()->chunkColumns(3), null);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith9ItemArrayTo3Columns(){
        $test_array = range(0,8);

        $this->assertEquals(Arr::factory($test_array)->chunkColumns(3), [array_slice($test_array, 0, 3), array_slice($test_array, 3, 3), array_slice($test_array, 6, 3)]);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith10ItemArrayTo3Columns(){
        $test_array = range(0,9);

        $this->assertEquals(Arr::factory($test_array)->chunkColumns(3), [array_slice($test_array, 0, 4), array_slice($test_array, 4, 3), array_slice($test_array, 7, 3)]);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith11ItemArrayTo3Columns(){
        $test_array = range(0,10);

        $this->assertEquals(Arr::factory($test_array)->chunkColumns(3), [array_slice($test_array, 0, 4), array_slice($test_array, 4, 4), array_slice($test_array, 8, 3)]);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith2ItemArrayTo10Columns(){
        $test_array = range(0,1);

        $this->assertEquals(Arr::factory($test_array)->chunkColumns(10), [array_slice($test_array, 0, 1), array_slice($test_array, 1, 1), [], [], [], [], [], [], [], []]);
    }

    /**
     * Test chunk columns in normal use
     */
    public function testChunkColumnsWith11ItemArrayTo3ColumnsWithKeysPreserved(){
        $test_array = range(0,10);

        $this->assertEquals(Arr::factory($test_array)->chunkColumns(3, true), [array_slice($test_array, 0, 4, true), array_slice($test_array, 4, 4, true), array_slice($test_array, 8, 3, true)]);
    }

    /**
    * Test the count function of the array class
    */
    public function testCount(){
        $this->assertEquals(count($this->numeric_a), Arr::factory($this->numeric_a)->count());
    }

    /**
    * Test the length function of the array class
    */
    public function testLength(){
        $this->assertEquals(count($this->numeric_a), Arr::factory($this->numeric_a)->length());
    }

    /**
    * Test the sizeof function of the array class
    */
    public function testSizeOf(){
        $this->assertEquals(count($this->numeric_a), Arr::factory($this->numeric_a)->sizeOf());
    }

    /**
    * Test the countable interface of the array class
    */
    public function testCountableInterface(){
        $this->assertEquals(count($this->numeric_a), count(Arr::factory($this->numeric_a)));
    }
}
