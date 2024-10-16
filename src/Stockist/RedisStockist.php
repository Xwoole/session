<?php

namespace Xwoole\Session\Stockist;

use Redis;

readonly class RedisStockist implements Contract
{
    
    public function __construct(public Redis $dbc)
    {
        
    }
    
    public function check(string $id): bool
    {
        return false;
    }
    
    public function get(string $id): array
    {
        return [];
    }
    
    public function set(string $id, array $data): void
    {
        
    }
    
    public function rename(string $oldId, string $newId): void
    {
        
    }
    
    public function unset(string $id): void
    {
        
    }
    
    
}
