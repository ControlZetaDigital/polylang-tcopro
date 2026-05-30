#!/usr/bin/env bash
# Builds a WordPress.org-compliant ZIP from git-tracked files only,
# and copies it to wp-content/uploads (accessible from the dev domain).
#
# Run from the HOST (not inside Docker):
#   bash /srv/cz/www/dev/wp-content/plugins/polylang-tcopro/bin/build-wporg.sh

set -euo pipefail

PLUGIN_DIR="/srv/cz/www/dev/wp-content/plugins/polylang-tcopro"
OUT_DIR="/srv/cz/www/dev/wp-content/uploads/polylang-tcopro"
VERSION=$(grep -m1 "^ \* Version:" "${PLUGIN_DIR}/polylang-tcopro.php" | awk '{print $NF}')
ZIP_NAME="polylang-tcopro-${VERSION}-wporg.zip"

echo "Building WP.org ZIP for version ${VERSION}..."

mkdir -p "${OUT_DIR}"
rm -rf /tmp/wporg-build
mkdir -p /tmp/wporg-build/polylang-tcopro

# Export only git-tracked files — no untracked local files (.claude, etc.)
git -C "${PLUGIN_DIR}" archive HEAD | tar -x -C /tmp/wporg-build/polylang-tcopro

# Remove files/dirs excluded from the WP.org build
rm -rf \
  /tmp/wporg-build/polylang-tcopro/.github \
  /tmp/wporg-build/polylang-tcopro/.distignore \
  /tmp/wporg-build/polylang-tcopro/bin \
  /tmp/wporg-build/polylang-tcopro/composer.json \
  /tmp/wporg-build/polylang-tcopro/composer.lock \
  /tmp/wporg-build/polylang-tcopro/readme.md \
  /tmp/wporg-build/polylang-tcopro/settings.png \
  /tmp/wporg-build/polylang-tcopro/vendor \
  /tmp/wporg-build/polylang-tcopro/src/UpdateChecker.php \
  /tmp/wporg-build/polylang-tcopro/admin/views/debug.php

# Zip using Python (zip may not be available on all hosts)
python3 -c "
import zipfile, os, sys

src = '/tmp/wporg-build'
out = '${OUT_DIR}/${ZIP_NAME}'

with zipfile.ZipFile(out, 'w', zipfile.ZIP_DEFLATED) as zf:
    for root, dirs, files in os.walk(src):
        for file in files:
            abs_path = os.path.join(root, file)
            arc_path = os.path.relpath(abs_path, src)
            zf.write(abs_path, arc_path)
print('ZIP created: ' + out)
"

echo ""
echo "Done: ${OUT_DIR}/${ZIP_NAME}"
echo "URL:  https://dev.controlzetadigital.com/wp-content/uploads/polylang-tcopro/${ZIP_NAME}"
