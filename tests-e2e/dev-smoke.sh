#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:18081/api/graphql/}"
PGADMIN_URL="${PGADMIN_URL:-http://127.0.0.1:18080/login}"

echo "Checking GraphQL endpoint on ${BASE_URL} ..."
curl -fsS "$BASE_URL" \
  -H "Content-Type: application/json" \
  -d '{"query":"query { __typename }"}' >/dev/null

echo "Checking pgAdmin on ${PGADMIN_URL} ..."
curl -fsS "$PGADMIN_URL" >/dev/null

echo "Dev smoke test passed."