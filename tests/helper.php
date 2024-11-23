<?php

namespace XwooleTest;

function serialize(array $dictionary): string
{
    if( extension_loaded("igbinary") )
    {
        $data = call_user_func("igbinary_serialize", $dictionary);
    }
    else
    {
        $data = serialize($dictionary);
    }
    
    return $data;
}

function unserialize(string $data): array
{
    if( extension_loaded("igbinary") )
    {
        $dictionary = call_user_func("igbinary_unserialize", $data);
    }
    else
    {
        $dictionary = unserialize($data);
    }
    
    return $dictionary;
}
