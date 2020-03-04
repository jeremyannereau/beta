<?php

namespace App\Utilities;

class ApiFunctions{
    public function genererToken(){
        $random = random_bytes(10);
        //return $random;
        return "abcdefrd";
    }
}