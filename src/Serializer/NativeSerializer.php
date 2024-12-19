<?php

namespace Xwoole\Session\Serializer;

class NativeSerializer implements Contract
{
    
    public function serialize(array $dictionary): string
    {
        return serialize($dictionary);
    }
    
    public function unserialize(string $data): array
    {
        return unserialize($data);
    }
    
}

