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
     * {@inheritdoc}
     */
    public function count(){
        return count($this->store);
    }
}
