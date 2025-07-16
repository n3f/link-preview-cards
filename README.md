# Link Preview Cards

A WordPress plugin that provides a Gutenberg block for displaying link preview cards from any URL.

## Development

### Prerequisites

- Node.js 16+
- PHP 7.4+
- WordPress development environment

### Setup

1. Clone the repository
2. Install dependencies:
   ```bash
   bun install
   ```

### Development Commands

```bash
# Start development environment
bun run start

# Build for production
bun run build

# Start block development (hot reload)
bun run block-start

# Run unit tests
bun run test:unit

# Run PHP tests
bun run test:php

# Run Playwright tests
bun run test:playwright

# Stop development environment
bun run stop

# Reset development environment
bun run reset
```

### Project Structure

```
link-preview-cards/
├── src/                    # Block source files
│   ├── block.json          # Block configuration
│   ├── edit.js             # Editor component
│   ├── save.js             # Frontend render
│   ├── view.js             # Frontend JavaScript
│   └── style.scss          # Styles
├── build/                  # Built files (generated)
├── tests/                  # PHP tests
├── specs/                  # Playwright tests
└── link-preview-cards.php  # Main plugin file
```

### Testing

The project includes multiple testing layers:

- **Unit Tests**: JavaScript tests using Jest
- **PHP Tests**: API endpoint testing
- **E2E Tests**: Playwright tests for full user workflows

### TODO

- [x] README.txt (WordPress.org format)
- [x] README.md (Development info)
- [x] LICENSE.txt
- [x] Fix block display... it's not working
- [x] Add nonce to API endpoint
- [ ] Configure color palette (block settings)
- [ ] Add PHPUnit tests
- [ ] Rate limiting API endpoint
- [ ] Refresh button on block (to refresh the link preview data)
- [ ] Add proper error handling if link preview data is not found
- [ ] Internationalization (i18n)
- [ ] Caching link preview data (use transient API)

