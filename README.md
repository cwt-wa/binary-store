# CWT Binary

A filesystem datastore interface for storing binaries of CWT.

PHP Version 8.1

## Usage

```
php -S localhost:8000 -t public
```

### `photo`

| Method | Endpoint |
| --- | --- |
| POST | /api/user/{userId}/photo |
| GET  | /api/user/{userId}/photo |

### `replay`

| Method | Endpoint |
| --- | --- |
| POST | /api/game/{gameId}/replay |
| GET  | /api/game/{gameId}/replay |

### `map`

| Method | Endpoint |
| --- | --- |
| POST | /api/game/{gameId}/map/{round} |
| GET  | /api/game/{gameId}/map/{round} |

