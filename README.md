# CWT Binary

A filesystem datastore interface for storing binaries of CWT.

PHP Version 7.1.31-he.0

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

