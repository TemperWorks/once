<?php

namespace Spatie\Once;

use Countable;
use SplObjectStorage;

class Cache implements Countable
{
    protected static self $cache;

    public SplObjectStorage $values;

    protected bool $enabled = true;

    public static function getInstance(): self
    {
        return static::$cache ??= new self;
    }

    protected function __construct()
    {
        $this->values = new SplObjectStorage();
    }

    public function has(object $object, string $backtraceHash): bool
    {
        if (! isset($this->values[$object])) {

            return false;
        }

        return array_key_exists($backtraceHash, $this->values[$object]);
    }

    public function get($object, string $backtraceHash)
    {
        return $this->values[$object][$backtraceHash];
    }

    public function set(object $object, string $backtraceHash, $value): void
    {
        $cached = $this->values[$object] ?? [];

        $cached[$backtraceHash] = $value;

        $this->values[$object] = $cached;
    }

    public function forget(object $object): void
    {
        unset($this->values[$object]);
    }

    public function flush(): self
    {
        $this->values = new SplObjectStorage();

        return $this;
    }

    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function count(): int
    {
        return count($this->values);
    }
}
