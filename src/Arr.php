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
            elseif($sequential_keys && ($previous_index + 1) !== $key){
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
     * Slices the array into a smaller specified section
     * @param  int $index  Index position to start the slice
     * @param  int $length How many items in the array to retrieve from the specified index
     * @return self        Returns a new instance of the class with the specified elements
     * @throws \InvalidArgumentException
     */
    public function slice($index, $length = NULL){
        if(!ctype_digit((string)$index)){
            throw new \InvalidArgumentException('Index must be an integer');
        }

        if($length !== NULL && !ctype_digit((string)$length)){
            throw new \InvalidArgumentException('Length must be an integer or NULL');
        }

        return new self(array_slice($this->store, $index, $length));
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
