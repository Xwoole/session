<?php

namespace Xwoole\Session\Identifier;

interface Contract
{
    
    public function get(): string;
    public function set(string $id): void;
    public function unset(): void;
    
}
