# `lint-php-action`

Runs PHP's built-in syntax check on your source code.
Adds error annotations on any files with problems.

## Inputs

| Input | Required | Default | Description |
|---|---|---|---|
| `php-version` | no | `latest` | What version pf PHP to test with. Any version supported by [`shivammathur/setup-php`](https://github.com/shivammathur/setup-php#tada-php-support) should work. |
| `file-extensions` | no | `php` | Comma-separated list of PHP file extensions (e.g. `php, inc`) |

## Example

See [`self-test.yml`](https://github.com/Firehed/lint-php-action/blob/main/.github/workflows/self-test.yml) for up-to-date examples.

```yaml
name: Lint
on:
  pull_request:
jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: firehed/lint-php-action@v1
```
