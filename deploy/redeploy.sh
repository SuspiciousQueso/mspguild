#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"/..

echo "[deploy] Pull latest images (if remote) and rebuild local images..."
#docker compose pull
docker compose up -d --build --remove-orphans
echo "[deploy] Stack redeployed."
