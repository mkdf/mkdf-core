<?php
namespace MKDF\Core\Entity;

class Bucket
{
    public function getProperties() {
        $properties = array();
        foreach ($this as $key => $value) {
            $properties[$key] = $value;
        }
        return $properties;
    }

    public function setProperties($data) {
        foreach($data as $key => $value){
            $this->{$key} = !empty($data[$key]) ? $data[$key] : null;            
        }
    }
    
    public static function collectionToArray($collection){
        $array = array();
        foreach($collection as $bucket){
            array_push($array, $bucket->getProperties());
        }
        return $array;
    }
}