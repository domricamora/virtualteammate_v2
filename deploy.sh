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

# Number of concurrent uploads. Each curl opens its own FTP connection, so
# uploading in parallel is dramatically faster than one-file-at-a-time.
# Override with FTP_PARALLEL=N ./deploy.sh if the host rejects many connections.
MAX_PAR="${FTP_PARALLEL:-8}"

# Top-level production files at the repo root.
TOP_FILES=(
  "index.php"
  ".htaccess"
  "robots.txt"
  "sitemap.xml"
  "favicon.ico"
  "talent-photo.php"
  "talent-media.php"
  "vt-link.php"
  "track.php"
  "lead.php"
)

# Production directories to mirror recursively.
TOP_DIRS=(
  "css"
  "js"
  "images"
  "includes"
  "services"
  "business"
  "healthcare-landing"
  "about"
  "careers"
  "case-studies"
  "contact"
  "guarantee"
  "virtual-teammates"
  "terms"
  "privacy-policy"
  "portal"
)

# data/ is uploaded selectively — never ship the SQLite DB, sync state,
# super-admin credentials file or downloaded media. The bare directory
# (with .htaccess deny + .gitkeep) seeds the writable folder the portal
# installer will populate on first run.
DATA_FILES=(
  "data/.htaccess"
  "data/.gitkeep"
)

upload(){
  # --retry rides out transient FTP drops; --ftp-create-dirs tolerates dirs
  # that already exist (so parallel uploads to the same folder don't clash).
  curl -sS --user "$USER_PASS" --ftp-create-dirs --retry 2 -T "$1" "${REMOTE_BASE}$2" \
    || echo "  ! FAILED: $2" >&2
}

# Block until fewer than $MAX_PAR background uploads are running.
gate(){ while [ "$(jobs -rp | wc -l)" -ge "$MAX_PAR" ]; do wait -n 2>/dev/null || true; done; }

queue(){            # queue <src> <dest> — launch upload in the background pool
  echo "  $2"
  gate
  upload "$1" "$2" &
}

upload_dir(){
  local dir="$1"
  [ -d "$dir" ] || return 0
  while IFS= read -r -d '' f; do
    queue "$f" "${f#./}"
    # smtp.local.php holds server-only SMTP creds — never ship/overwrite it.
  done < <(find "$dir" -type f ! -name 'smtp.local.php' -print0)
}

echo "Deploying to ${FTP_HOST} as ${FTP_USER} (parallel x${MAX_PAR})"
for f in "${TOP_FILES[@]}"; do
  if [ -f "$f" ]; then queue "$f" "$f"; fi
done
for d in "${TOP_DIRS[@]}"; do
  upload_dir "$d"
done
for f in "${DATA_FILES[@]}"; do
  if [ -f "$f" ]; then queue "$f" "$f"; fi
done
wait
echo "Deploy complete."
