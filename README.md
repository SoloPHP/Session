# PHP Session Handler

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/session.svg?style=flat-square)](https://packagist.org/packages/solophp/session)
[![License](https://img.shields.io/packagist/l/solophp/session.svg?style=flat-square)](https://packagist.org/packages/solophp/session)
[![PHP Version](https://img.shields.io/packagist/php-v/solophp/session.svg?style=flat-square)](https://packagist.org/packages/solophp/session)

Secure PHP session handler with advanced security features and session management.

## Features

- Secure session configuration out of the box
- Session timeout management
- Session integrity checks (IP and User-Agent validation)
- Protection against session fixation attacks
- Strict session management
- Cookie security controls
- Session status monitoring

## Requirements

- PHP 8.1 or higher

## Installation

```bash
composer require solophp/session
```

## Basic Usage

```php
use Solo\Session\Session;

// Create session with default secure settings
$session = new Session();

// Store data
$session->set('user', $userData);

// Get data
$userData = $session->get('user');

// Check if data exists
if ($session->has('user')) {
    // ...
}

// Remove data
$session->unset('user');

// Clear all data
$session->clear();

// Completely destroy session
$session->destroy();
```

## Advanced Configuration

```php
$session = new Session(
    lifetime: 3600,          // Cookie lifetime in seconds (0 = until browser closes)
    secure: true,            // Require HTTPS
    httpOnly: true,          // Prevent JavaScript access
    sameSite: 'Strict',      // CSRF protection (Strict|Lax|None)
    path: '/',               // Cookie path
    domain: '',              // Cookie domain
    useStrictMode: true,     // Enable strict mode
    gcMaxlifetime: 86400,    // Session garbage collection lifetime
    useCookiesOnly: true,    // Prevent session ID in URLs
    timeout: 1800            // Session timeout in seconds
);
```

## Security Features

### Session Timeout
Sessions automatically expire after a period of inactivity (default 30 minutes):

```php
// Check if session has expired
if ($session->isExpired()) {
    // Handle expired session
}

// Get last activity timestamp
$lastActivity = $session->getLastActivity();
```

### Session Integrity
Sessions are validated against:
- User's IP address
- User's browser (User-Agent)
- Session initiation status

### Cookie Security
Secure cookie settings:
- HttpOnly flag
- Secure flag (HTTPS only)
- SameSite attribute
- Configurable domain and path
- Optional lifetime

## Available Methods

### Data Management
```php
// Get value with default fallback
$value = $session->get('key', 'default');

// Set value
$session->set('key', 'value');

// Check existence
$exists = $session->has('key');

// Remove specific key
$session->unset('key');

// Get all session data
$allData = $session->all();

// Clear all data
$session->clear();
```

### Session Management
```php
// Regenerate session ID
$session->regenerateId();

// Destroy session completely
$session->destroy();

// Get current session ID
$id = $session->getCurrentId();

// Get session cookie name
$name = $session->getCookieName();

// Get session save path
$path = $session->getSavePath();

// Get session status
$status = $session->getStatus();

// Get configured timeout
$timeout = $session->getTimeout();
```

## Session Status Values

- `PHP_SESSION_DISABLED` = 0
- `PHP_SESSION_NONE` = 1
- `PHP_SESSION_ACTIVE` = 2

## Development

### Running Tests

```bash
composer test
```

### Code Style

Check code style:
```bash
composer cs
```

Fix code style:
```bash
composer cs-fix
```

## Best Practices

1. Always use HTTPS in production (`secure: true`)
2. Set appropriate timeout values for your application
3. Consider using 'Strict' SameSite setting for better security
4. Monitor session activity using provided methods
5. Handle expired sessions appropriately
6. Use session regeneration for sensitive operations

## License

MIT