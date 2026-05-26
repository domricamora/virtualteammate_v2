#!/usr/bin/env bash
# Deploy the Virtual Teammate site to the staging FTP server.
# Reads credentials from .ftp.local (gitignored). Uploads production
# assets only — skips git/dev files (.git, *.md, .gitignore, deploy.sh).
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

if [ ! -f .ftp.local ]; then
  echo "deploy.sh: .ftp.local not found in $SCRIPT_DIR — skipping deploy." >&2
  exit 0
fi
# shellcheck disable=SC1091
. ./.ftp.local

: "${FTP_USER:?FTP_USER missing in .ftp.local}"
: "${FTP_PASS:?FTP_PASS missing in .ftp.local}"
: "${FTP_HOST:?FTP_HOST missing in .ftp.local}"

USER_PASS="${FTP_USER}:${FTP_PASS}"
REMOTE_BASE="ftp://${FTP_HOST}/"

# Top-level production files at the repo root.
TOP_FILES=(
  "index.php"
  ".htaccess"
  "robots.txt"
  "sitemap.xml"
  "favicon.ico"
)

# Production directories to mirror recursively.
TOP_DIRS=(
  "css"
  "js"
  "images"
  "includes"
)

upload(){
  local src="$1" dest="$2"
  curl -sS --user "$USER_PASS" --ftp-create-dirs -T "$src" "${REMOTE_BASE}${dest}"
}

upload_dir(){
  local dir="$1"
  [ -d "$dir" ] || return 0
  while IFS= read -r -d '' f; do
    local rel="${f#./}"
    echo "  $rel"
    upload "$f" "$rel"
  done < <(find "$dir" -type f -print0)
}

echo "Deploying to ${FTP_HOST} as ${FTP_USER}"
for f in "${TOP_FILES[@]}"; do
  if [ -f "$f" ]; then
    echo "  $f"
    upload "$f" "$f"
  fi
done
for d in "${TOP_DIRS[@]}"; do
  upload_dir "$d"
done
echo "Deploy complete."
