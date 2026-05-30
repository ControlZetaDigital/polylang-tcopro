#!/usr/bin/env bash
# Builds a WordPress.org-compliant ZIP (respects .distignore) and copies it
# to wp-content/uploads so it's downloadable from the dev domain.
#
# Usage (from host):
#   docker exec <container> bash /var/www/html/wp-content/plugins/polylang-tcopro/bin/build-wporg.sh

set -euo pipefail

PLUGIN_DIR="/var/www/html/wp-content/plugins/polylang-tcopro"
OUT_DIR="/var/www/html/wp-content/uploads/polylang-tcopro"
VERSION=$(grep -m1 "^ \* Version:" "${PLUGIN_DIR}/polylang-tcopro.php" | awk '{print $NF}')
ZIP_NAME="polylang-tcopro-${VERSION}-wporg.zip"

echo "Building WP.org ZIP for version ${VERSION}..."

mkdir -p "${OUT_DIR}"
rm -rf /tmp/wporg-build
mkdir -p /tmp/wporg-build/polylang-tcopro

rsync -a \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='.distignore' \
  --exclude='bin' \
  --exclude='composer.json' \
  --exclude='composer.lock' \
  --exclude='readme.md' \
  --exclude='settings.png' \
  --exclude='src/UpdateChecker.php' \
  --exclude='vendor/yahnis-elsts' \
  --exclude='admin/views/debug.php' \
  "${PLUGIN_DIR}/" "/tmp/wporg-build/polylang-tcopro/"

cd /tmp/wporg-build
zip -rq "${OUT_DIR}/${ZIP_NAME}" polylang-tcopro/

echo ""
echo "Done: ${OUT_DIR}/${ZIP_NAME}"
echo "URL:  https://dev.controlzetadigital.com/wp-content/uploads/polylang-tcopro/${ZIP_NAME}"
