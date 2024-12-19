<?php

namespace Xwoole\Session\Serializer;

use RuntimeException;

class IGBinarySerializer implements Contract
{
    public function __construct()
    {
        if( ! extension_loaded("igbinary") )
        {
            throw new RuntimeException("'igbinary' extension is not loaded!");
        }
    }
    
    public function serialize(array $dictionary): string
    {
        return igbinary_serialize($dictionary);
    }
    
    public function unserialize(string $data): array
    {
        return igbinary_unserialize($data);
    }
    
}

