# Data Models — root

## Scope
Deep scan for schema/migrations/models within the repo.

## Findings
- No database schema, migrations, or ORM models are present in the repo.
- The theme assumes WordPress + WooCommerce standard tables; nothing custom is defined here.

## Implications
- Any data model changes occur in the backing WordPress/WooCommerce DB, not tracked in this repository.
- If schema documentation is needed, export from the live DB or the WooCommerce schema reference.
