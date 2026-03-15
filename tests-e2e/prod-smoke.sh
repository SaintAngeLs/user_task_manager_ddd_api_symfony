#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:18082/api/graphql/}"

echo "Checking GraphQL endpoint on ${BASE_URL} ..."
curl -fsS "$BASE_URL" \
  -H "Content-Type: application/json" \
  -d '{"query":"query { __typename }"}' >/dev/null

echo "Prod smoke test passed."