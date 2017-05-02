<?php

namespace Warpcode\Std;

class Arr implements /*\IteratorAggregate , \ArrayAccess , */ \Serializable , \Countable{

    /**
     * Storage variable for the provided array
     *
     * @var array
     */
    private $store = [];

    /**
     * Constructor.
     *
     * @param array $array Content of the array
     * @throws \InvalidArgumentException
     */
    public function __construct($array = []){
        if(is_array($array)){
            $this->store = $array;
        }
        elseif($array instanceof self){
            $this->store = $array->ToArray();
        }
        else{
            throw new \InvalidArgumentException('You must pass through a valid array');
        }
    }

    /**
     * Static method for creating a new instance of the class
     * @param  array $array Content of the array
     * @return static
     */
    public static function factory($array = []){
        return new static($array);
    }

    /**
     * Whether the array is empty
     * @return boolean
     */
    public function isEmpty(){
        return empty($this->store);
    }

    /**
     * Checks to see if the internal array is multidimensional
     * @return boolean
     */
    public function isMultiDimensional(){
        $is_multidim = false;
        foreach($this->store as &$value){
            if(is_array($value)){
                $is_multidim = true;
                break;
            }
        }
        unset($value);

        return $is_multidim;
    }

    /**
     * Checks wither the array has integer keys
     * @param  boolean $sequential_keys Make sure the keys are sequenctional
     * @return boolean
     */
    public function isIndexed($sequential_keys = true){
        $previous_index = NULL;
        $is_index = true;
        foreach($this->store as $key => &$value){
            if(!is_int($key)){
                $is_index = false;
                break;
            }
            elseif($key < 0){
                // keys less than 0 are not valid indexs for an indexed array
                $is_index = false;
                break;
            }elseif($sequential_keys && $previous_index === null && $key !== 0){
                //if the first key is not 0, we do not have sequentical keys
                $is_index = false;
                break;
            }
            elseif($sequential_keys && $previous_index !== null && ($previous_index + 1) !== $key){
                // current key is not +1 of previous index and therefor breaks sequence
                $is_index = false;
                break;
            }

            $previous_index = $key;
        }
        unset($value);

        return $is_index;
    }

    /**
     * Checks whether an index exists within the array
     * @param  int     $index Index value to check
     * @return boolean
     */
    public function hasIndex($index){
        if(!is_numeric($index)){
            throw new \InvalidArgumentException('Index must be numeric');
        }

        $array_count = $this->count() - 1;

        if($array_count < 0){
            return false;
        }

        return $array_count - abs((int)$index) >= 0;
    }

    /**
     * Checks whether the specified key exists
     * @param  mixed  $key  Key value
     * @return boolean
     */
    public function hasKey($key){
        return array_key_exists($key, $this->store);
    }

    /**
     * Checks whether the value exists within the array
     * @param  mixed   $value   Value to search for
     * @param  boolean $strict  Whether to check for the type of the value
     * @return boolean
     */
    public function hasValue($value, $strict = false){
        return in_array($value, $this->store, $strict);
    }

    /**
     * Retrieve a value from the array by it's index number
     * @param  int    $index Index to retrieve value
     * @return mixed
     */
    public function getByIndex($index){
        return current($this->slice($index, 1)->toArray());
    }

    /**
     * Retrieve a value from the array by it's key
     * @param  mixed $key  The keyfor the value in the array
     * @return mixed       The value corresponding to the key provided
     */
    public function getByKey($key){
        return $this->hasKey($key)? $this->store[$key]: null;
    }

    /**
     * Returns a list of valid index numbers
     * @return static
     */
    public function getIndexes(){
        $count = $this->count();

        if($count < 1){
            return new static();
        }

        return new static(range(0, $this->count() - 1));
    }

    /**
     * Retrieves a list of keys from the array
     * @return static
     */
    public function getKeys(){
        return new static(array_keys($this->store));
    }

    /**
     * Retrieves all the values from the array
     * @return static
     */
    public function getValues(){
        return new static(array_values($this->store));
    }



    /**
     * Chunks the array into multiple arrays with the amount specified in each array
     * @param  int  $length           Number of elements in each array
     * @param  boolean $preserve_keys Preserve original keys of the array
     * @return static                 Returns a new instance of the class with the specified elements
     */
    public function chunk($length, $preserve_keys = false){
        if( !ctype_digit((string)$length) || $length < 1){
            throw new \InvalidArgumentException('Length must be an integer that is greater than 0');
        }

        if($this->isEmpty()){
            return null;
        }

        return array_chunk($this->store, $length, $preserve_keys);
    }

    /**
     * Chunks the array by distributing the number of elements as equally as possible into the number of "columns" specified
     * @param  int     $column_count  Number of columns to split the array into
     * @param  boolean $preserve_keys Preserve original keys of the array
     * @return static                 Returns a new instance of the class with the specified elements
     */
    public function chunkColumns($column_count, $preserve_keys = false){
        if( !ctype_digit((string)$column_count) || $column_count < 1){
            throw new \InvalidArgumentException('Column count must be an integer that is greater than 0');
        }

        if($this->isEmpty()){
            return null;
        }

        $elements_per_col = ceil($this->count()/ $column_count);

        $chunked = $this->chunk($elements_per_col, $preserve_keys);
        $chunked_count = count($chunked);
        if($chunked_count != $column_count){
            //Ensure the correct number of columns are in the array
            $chunk_diff = $column_count - $chunked_count;
            for($i = 0; $i < $chunk_diff; ++$i){
                $chunked[] = [];
            }
        }
        return $chunked;
    }

    /**
     * Slices the array into a smaller specified section
     * @param  int $index               Index position to start the slice
     * @param  int $length              How many items in the array to retrieve from the specified index
     * @param  boolean $preserve_keys   Whether to preserve the array keys
     * @return static                   Returns a new instance of the class with the specified elements
     * @throws \InvalidArgumentException
     */
    public function slice($index, $length = null, $preserve_keys = false){
        if(!$this->hasIndex($index)){
            return new static();
        }

        if($length !== NULL && !ctype_digit((string)$length)){
            throw new \InvalidArgumentException('Length must be an positive integer or NULL');
        }

        return new static(array_slice($this->store, (int)$index, $length === null? null: (int)$length, $preserve_keys));
    }

    /**
     * Returns the first element of the array with a key
     * @param  boolean $preserve_key Whether to preserve the elements original key
     * @return static                  Returns a new instance of the class with the specified elements
     */
    public function sliceFirst($preserve_key = false){
        return $this->slice(0, 1, $preserve_keys);
    }

    /**
     * Returns the last element of the array with a key
     * @param  boolean $preserve_key Whether to preserve the elements original key
     * @return static                  Returns a new instance of the class with the specified elements
     */
    public function sliceLast($preserve_key = false){
        return $this->slice(-1, 1, $preserve_keys);
    }

    /**
     * Return the internal array
     *
     * @return array Stored array
     */
    public function toArray(){
        return $this->store;
    }

    /**
     * Converts the array to a CSV formatted string
     * @return string
     */
    public function toCSV(){

    }

    /**
     * Forces a json object to preserve keys
     * @return string Json formatted string
     */
    public function toJsonObject(){

    }

    /**
     * Forces a json array which doesn't preserve keys.
     * @return string Json formatted string
     */
    public function toJsonArray(){

    }

    /**
     * Output an XML formatted string
     * @return string XML in a string
     */
    public function toXML(){

    }

    /**
     * {@inheritdoc}
     */
    public function serialize() {
        return serialize($this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($content) {
        $this->store = unserialize($content);
    }

    /**
     * Alias of count()
     * @return int
     */
    public function length(){
        return $this->count();
    }

    /**
     * Alias of count()
     * @return int
     */
    public function sizeof(){
        return $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function count(){
        return count($this->store);
    }
}
