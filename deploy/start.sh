#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"/..

echo "[deploy] Starting deployment stack..."
docker compose up -d --build
echo "[deploy] [deploy] Done."
