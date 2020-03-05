<?php

namespace App\Utilities;

class ApiFunctions{
    public function genererToken(){
        $random = bin2hex(random_bytes(64));
        return $random;
       
    }
}