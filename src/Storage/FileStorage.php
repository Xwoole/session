<?php

namespace Xwoole\Session\Storage;

use OutOfBoundsException;

class FileStorage implements Contract
{
    
    public function __construct(readonly string $dir)
    {
        @mkdir($this->dir);
    }
    
    private function getPath(string $id)
    {
        return $this->dir ."/". $id;
    }
    
    public function check(string $id): bool
    {
        return is_file($this->getPath($id));
    }
    
    public function get(string $id): string
    {
        if( ! $this->check($id) )
        {
            throw new OutOfBoundsException("invalid id");
        }
        
        return file_get_contents($this->getPath($id));
    }
    
    public function set(string $id, string $data): void
    {
        file_put_contents($this->getPath($id), $data);
    }
    
    public function rename(string $oldId, string $newId): void
    {
        rename($this->dir ."/". $oldId, $this->dir ."/". $newId);
    }
    
    public function unset(string $id): void
    {
        @unlink($this->getPath($id));
    }
    
    public function close(): void
    {
        
    }
    
}
