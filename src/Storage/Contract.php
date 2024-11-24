<?php

namespace Xwoole\Session\Storage;

interface Contract
{
    
    public function open(): void;
    public function check(string $id): bool;
    public function get(string $id): string;
    public function set(string $id, string $data): void;
    public function rename(string $oldId, string $newId): void;
    public function unset(string $id): void;
    public function close(): void;
    
}
