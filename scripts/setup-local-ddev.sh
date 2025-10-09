#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PUBLIC_DIR="$ROOT_DIR/public"
WP_PATH="$PUBLIC_DIR"

# Detect which theme slug exists locally so we can wire it into WordPress.
PREFERRED_THEME_SLUGS=("svicloudtvbox" "svicloudtvbox-lumen")
THEME_SLUG=""
for candidate in "${PREFERRED_THEME_SLUGS[@]}"; do
  if [ -d "$ROOT_DIR/theme/$candidate" ]; then
    THEME_SLUG="$candidate"
    break
  fi
done

if [ -z "$THEME_SLUG" ]; then
  echo "Error: No theme directory found under $ROOT_DIR/theme (expected one of: ${PREFERRED_THEME_SLUGS[*]})." >&2
  exit 1
fi

THEME_SOURCE_REL="../../../theme/${THEME_SLUG}"
THEME_TARGET="$WP_PATH/wp-content/themes/${THEME_SLUG}"
SITE_URL="https://svicloudtvbox.ddev.site"
SITE_TITLE="SVICLOUDTVBOX Local"
ADMIN_USER="admin"
ADMIN_PASS="admin"
ADMIN_EMAIL="dev@example.com"

if ! command -v ddev >/dev/null 2>&1; then
  echo "Error: ddev is not installed or not on PATH." >&2
  echo "Install DDEV from https://ddev.readthedocs.io/en/latest/users/install/ and retry." >&2
  exit 1
fi

cd "$ROOT_DIR"

mkdir -p "$PUBLIC_DIR"

echo "Starting DDEV containers..."
ddev start >/dev/null

echo "Ensuring WordPress core is present..."
if [ ! -f "$WP_PATH/wp-load.php" ]; then
  ddev wp core download --path=public --force
fi

if [ ! -f "$WP_PATH/wp-config.php" ]; then
  ddev wp config create \
    --path=public \
    --dbname=db --dbuser=db --dbpass=db --dbhost=db \
    --skip-check
fi

if ! ddev wp db check --path=public >/dev/null 2>&1; then
  ddev wp db create --path=public
fi

if ! ddev wp core is-installed --path=public >/dev/null 2>&1; then
  ddev wp core install \
    --path=public \
    --url="$SITE_URL" \
    --title="$SITE_TITLE" \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email
fi

mkdir -p "$WP_PATH/wp-content/themes"
if [ -L "$THEME_TARGET" ]; then
  true
elif [ -e "$THEME_TARGET" ]; then
  echo "Existing theme directory at $THEME_TARGET detected; backing up to ${THEME_TARGET}.bak" >&2
  mv "$THEME_TARGET" "${THEME_TARGET}.bak"
  ln -s "$THEME_SOURCE_REL" "$THEME_TARGET"
else
  ln -s "$THEME_SOURCE_REL" "$THEME_TARGET"
fi

echo "Theme symlinked to $THEME_TARGET (source: $THEME_SOURCE_REL)."

# Activate theme if not already active
if ! ddev wp theme is-active "$THEME_SLUG" --path=public >/dev/null 2>&1; then
  ddev wp theme activate "$THEME_SLUG" --path=public
fi

PLUGINS=(
  woocommerce
  woocommerce-gateway-stripe
  woocommerce-paypal-payments
  litespeed-cache
  translatepress-multilingual
  wordpress-seo
  wp-mail-smtp
)

echo "Installing required plugins..."
for plugin in "${PLUGINS[@]}"; do
  if ddev wp plugin is-installed "$plugin" --path=public >/dev/null 2>&1; then
    continue
  fi
  ddev wp plugin install "$plugin" --activate --path=public
done

# Ensure WooCommerce pages exist
if ddev wp plugin is-active woocommerce --path=public >/dev/null 2>&1; then
  ddev wp wc tool install --name=install_woocommerce_pages --path=public >/dev/null 2>&1 || true
fi

# Helpful local dev constants
if ! ddev wp config has WP_DEBUG --path=public >/dev/null 2>&1; then
  ddev wp config set WP_DEBUG true --raw --path=public --type=constant
fi
if ! ddev wp config has WP_DEBUG_LOG --path=public >/dev/null 2>&1; then
  ddev wp config set WP_DEBUG_LOG true --raw --path=public --type=constant
fi

echo "Local WordPress environment is ready."
echo "Admin URL: ${SITE_URL}/wp-admin (user: ${ADMIN_USER} / pass: ${ADMIN_PASS})"
