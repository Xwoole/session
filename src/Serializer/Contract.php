<?php

namespace Xwoole\Session\Serializer;

interface Contract
{
    
    public function serialize(array $dictionary): string;
    public function unserialize(string $data): array;
    
}

