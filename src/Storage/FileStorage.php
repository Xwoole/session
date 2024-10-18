<?php

namespace Xwoole\Session\Storage;

use OutOfBoundsException;

class FileStorage implements Contract
{
    public function __construct(readonly string $dir)
    {
        @mkdir($this->dir);
    }
    
    public function check(string $id): bool
    {
        return is_file($this->dir ."/". $id);
    }
    
    public function get(string $id): array
    {
        if( ! $this->check($id) )
        {
            throw new OutOfBoundsException("invalid id");
        }
        
        return unserialize(file_get_contents($this->dir ."/". $id));
    }
    
    public function set(string $id, array $data): void
    {
        file_put_contents($this->dir ."/". $id, serialize($data));
    }
    
    public function rename(string $oldId, string $newId): void
    {
        rename($this->dir ."/". $oldId, $this->dir ."/". $newId);
    }
    
    public function unset(string $id): void
    {
        @unlink($this->dir ."/". $id);
    }
    
    public function close(): void
    {
        
    }
    
}
