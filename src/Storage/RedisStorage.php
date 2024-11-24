<?php

namespace Xwoole\Session\Storage;

use OutOfBoundsException;
use Redis;

class RedisStorage implements Contract
{
    
    public function __construct(
        readonly Redis $dbc,
        private $host,
        private $port = 6379,
        private $timeout = 0.0,
        private $reserved = null,
        private $retryInterval = 0,
        private $readTimeout = 0.0
    )
    {
        $this->open();
    }
    
    public function open(): void
    {
        $this->dbc->connect(
            $this->host,
            $this->port,
            $this->timeout,
            $this->reserved,
            $this->retryInterval,
            $this->readTimeout
        );
    }
    
    public function check(string $id): bool
    {
        return $this->dbc->exists($id);
    }
    
    public function get(string $id): string
    {
        if( ! $this->dbc->exists($id) )
        {
            throw new OutOfBoundsException;
        }
        
        return $this->dbc->get($id);
    }
    
    public function set(string $id, string $data): void
    {
        $this->dbc->set($id, $data);
    }
    
    public function rename(string $oldId, string $newId): void
    {
        $this->dbc->rename($oldId, $newId);
    }
    
    public function unset(string $id): void
    {
        $this->dbc->del($id);
    }
    
    public function close(): void
    {
        $this->dbc->close();
    }
    
}
