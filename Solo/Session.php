<?php declare(strict_types=1);

namespace Solo;

class Session
{
    private array $session = [];

    public function __construct(
        private readonly int $lifetime = 0,
        private readonly bool $secure = true,
        private readonly bool $httpOnly = true,
        private readonly string $sameSite = 'Strict',
        private readonly string $path = '/',
        private readonly string $domain = '',
        private readonly bool $useStrictMode = true,
        private readonly int $gcMaxlifetime = 86400,
        private readonly bool $useCookiesOnly = true,
        private readonly int $timeout = 1800
    ) {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', $this->useStrictMode ? '1' : '0');
            ini_set('session.gc_maxlifetime', (string)$this->gcMaxlifetime);
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', $this->useCookiesOnly ? '1' : '0');

            session_set_cookie_params([
                'lifetime' => $this->lifetime,
                'path' => $this->path,
                'domain' => $this->domain,
                'secure' => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite,
            ]);

            session_start();

            $this->checkSessionTimeout();
            $this->checkSessionIntegrity();

            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
            }
        }
        $this->session = &$_SESSION;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->session[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->session;
    }

    public function set(string $key, mixed $value): void
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

    private function checkSessionTimeout(): void
    {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $this->timeout)) {
            session_unset();
            session_destroy();
            session_start();
    }
        $_SESSION['last_activity'] = time();
    }

    private function checkSessionIntegrity(): void
    {
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            session_unset();
            session_destroy();
            session_start();
    }
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        if (isset($_SESSION['ip']) && $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
            session_unset();
            session_destroy();
            session_start();
    }
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => $this->path,
                'domain' => $this->domain,
                'secure' => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite,
            ]);
        }
    }

    public function getCurrentId(): string
    {
        return session_id();
    }

    public function getCookieName(): string
    {
        return session_name();
    }

    public function getSavePath(): string
    {
        return session_save_path();
    }

    public function getStatus(): int
    {
        return session_status();
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getLastActivity(): ?int
    {
        return $_SESSION['last_activity'] ?? null;
    }

    public function isExpired(): bool
    {
        $lastActivity = $this->getLastActivity();
        return $lastActivity !== null && (time() - $lastActivity > $this->timeout);
    }
}