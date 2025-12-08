#!/usr/bin/evn bash
set -euo pipefail
cd "$(dirname "$0")"/..

echo "[deploy] Stopping deployment stack..."
docker compose down
echo "[deploy] Done."