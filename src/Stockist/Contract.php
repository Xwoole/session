<?php

namespace Xwoole\Session\Stockist;

interface Contract
{
    
    public function check(string $id): bool;
    public function get(string $id): array;
    public function set(string $id, array $data): void;
    public function rename(string $oldId, string $newId): void;
    public function unset(string $id): void;
    
}
