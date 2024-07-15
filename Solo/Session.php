<?php declare(strict_types=1);

namespace Solo;

class Session
{
    private array $session = [];

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session = &$_SESSION;
    }

    /**
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->session[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->session;
    }

    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->session);
    }

    public function unset(string $key): void
    {
        unset($this->session[$key]);
    }

    public function clear(): void
    {
        $this->session = [];
    }
    
    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }
}