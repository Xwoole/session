<?php

namespace Xwoole\Session\Identifier;

use Stringable;

interface Contract extends Stringable
{
    
    public function get(): string;
    public function set(string $id): void;
    public function unset(): void;
    
}
