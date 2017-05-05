<?php

namespace Warpcode\Std;

class Arr implements \ArrayAccess, \Iterator, \Serializable , \Countable{

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
     * Internal factory method for creating a new instance for an array
     * @param  array $array Content of the array
     * @return static
     */
    protected function createArray($array = []){
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
     * Checks whether the array contains only scalar values
     * @param  boolean $allow_null Includes null as a valid avlue in the check
     * @return boolean
     */
    public function isScalar($allow_null = true){
        $is_scalar = true;
        foreach($this->store as &$value){
            if(!is_scalar($value)){
                if($allow_null){
                    if($value !== null){
                        $is_scalar = false;
                        break;
                    }
                }
                else{
                    $is_scalar = false;
                    break;
                }
            }
        }
        unset($value);

        return $is_scalar;
    }

    /**
     * Returns a list of valid index numbers
     * @return static
     */
    public function getIndexes(){
        $count = $this->count();

        if($count < 1){
            return $this->createArray();
        }

        return $this->createArray(range(0, $this->count() - 1));
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
     * Retrieve a value from the array by it's index number
     * @param  int    $index Index to retrieve value
     * @return mixed
     */
    public function getByIndex($index){
        return current($this->slice($index, 1)->toArray());
    }

    /**
     * Retrieve the first value in the array
     * @return mixed
     */
    public function first(){
        return $this->getByIndex(0);
    }

    /**
     * Retrieve the last value in the array
     * @return mixed
     */
    public function last(){
        return $this->getByIndex($this->count() - 1);
    }

    /**
     * Performs an array search for a given value and returns the first corresponding index if successfull
     * @param  mixed  $value   Value to search for
     * @param  boolean $strict Whether to enable strict type comparison
     * @return mixed
     */
    public function getIndexFromValue($value, $strict = false){
        return $this->getValues()->getKeyFromValue($value, $strict);
    }

    /**
     * Retrieves a list of keys from the array
     * @return static
     */
    public function getKeys(){
        return new static(array_keys($this->store));
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
     * Retrieve a value from the array by it's key
     * @param  mixed $key  The keyfor the value in the array
     * @return mixed       The value corresponding to the key provided
     */
    public function getByKey($key){
        return $this->hasKey($key)? $this->store[$key]: null;
    }

    /**
     * Performs an array search for a given value and returns the first corresponding key if successfull
     * @param  mixed  $value   Value to search for
     * @param  boolean $strict Whether to enable strict type comparison
     * @return mixed
     */
    public function getKeyFromValue($value, $strict = false){
        $search = array_search($value, $this->store, $strict);

        if($search === null || $search === false){
            return false;
        }
        else{
            return $search;
        }
    }

    /**
     * Retrieves all the values from the array
     * @return static
     */
    public function getValues(){
        return $this->createArray(array_values($this->store));
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

        $array_length = $this->count();
        $elements_per_col = floor($array_length / $column_count);
        $elements_per_col_rem = $array_length % $column_count;

        $chunked = [];
        $offset = 0;
        for ($col = 0; $col < $column_count; $col++) {
            if($offset > ($array_length -1)){
                $chunked[$col] = [];
            }
            else{
                $length = ($col < $elements_per_col_rem) ? $elements_per_col + 1 : $elements_per_col;
                $chunked[$col] = $this->slice($offset, $length, $preserve_keys)->toArray();
                $offset += $length;
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
            return $this->createArray();
        }

        if($length !== NULL && !ctype_digit((string)$length)){
            throw new \InvalidArgumentException('Length must be an positive integer or NULL');
        }

        return $this->createArray(array_slice($this->store, (int)$index, $length === null? null: (int)$length, $preserve_keys));
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

    public function toQueryString($seperator = NULL, $escape_square_brackets = false, $rfc = PHP_QUERY_RFC3986){

        if($rfc !== PHP_QUERY_RFC3986 && $rfc !== PHP_QUERY_RFC1738){
            $rfc = PHP_QUERY_RFC3986;
        }

        if($seperator === null){
            $seperator = ini_get('arg_separator.output');
        }

        if($escape_square_brackets || !$this->isMultiDimensional()){
            return http_build_query($this->store, null, $seperator, $rfc);
        }
        else{
            return $this->toQueryStringProcessArray($this->store, $seperator, $rfc, '');
        }
    }

    protected function toQueryStringProcessArray($array, $sep, $rfc, $key){
        $ret = array();
        //if it's not an array or object, then it's not iterable (has no keys) so just return it
        if (!is_array($array) && !is_object($array))
            return ($rfc === PHP_QUERY_RFC3986? rawurlencode($array): urlencode($array));

        foreach ($array as $k => $v) {
            //if the key is not empty, it implies it's been called recursively so set up the query string array key
            if ((!empty($key)) || ($key === 0))
                $k = $key . '[' . ($rfc === PHP_QUERY_RFC3986? rawurlencode($k): urlencode($k)) . ']';

            //now we work on the value
            if (is_array($v)) {
                //just recusively call this function, eventually we'll get all the multidimensional array elements
                //unless some idiot does recursive references, at which point the loop will be infinite
                array_push($ret, $this->toQueryStringProcessArray($v, $sep, $rfc, $k));
            } elseif (is_object($v)) {
                //handle objects differently. First see if it has the toQueryString method
                if (method_exists($v, 'toQueryString')) {
                    $refl = new ReflectionMethod($v, 'toQueryString');

                    //the method is valid, so run it
                    if ($refl->isPublic() && !$refl->isStatic()) {
                        $obj_str = $v->toQueryString('', $sep, $k);
                    } else {
                        //it's not valid so we need to iterate the object
                        $obj_str = $v;
                    }
                } else {
                    //the method doesn't exist so we need to iterate through it
                    $obj_str = $v;
                }

                //it seems it has returned an array or the method wasn't found, so let's iterate through it
                if (is_array($obj_str) || is_object($obj_str)) {
                    array_push($ret, $this->toQueryStringProcessArray($obj_str, $sep, $rf, $k));
                } else {

                    //we had a return value that wasn't an array
                    //as the return is from toQueryString which calls this class (everything else is considered an array), we will trust it
                    array_push($ret, $k . '=' . $obj_str);
                }
            } else {
                array_push($ret, $k . '=' . ($rfc === PHP_QUERY_RFC3986? rawurlencode($v): urlencode($v)));
            }
        }

        //set the seperator and implode to create the final string
        return implode($sep, $ret);
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

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset){
        return $this->getByKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value){

    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset){
        return $this->hasKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset){

    }

    /**
     * {@inheritdoc}
     */
    public function current(){
        return current($this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function next(){
        return next($this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function key(){
        return key($this->store);
    }

    /**
     * {@inheritdoc}
     */
    public function valid(){
        return key($this->store) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(){
        return reset($this->store);
    }
}
