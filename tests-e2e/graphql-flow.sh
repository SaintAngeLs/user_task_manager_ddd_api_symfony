#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://127.0.0.1:18081/api/graphql/}"

graphql() {
  local query="$1"
  local token="${2:-}"

  if [ -n "$token" ]; then
    curl -fsS "$BASE_URL" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $token" \
      -d "{\"query\": $query}"
  else
    curl -fsS "$BASE_URL" \
      -H "Content-Type: application/json" \
      -d "{\"query\": $query}"
  fi
}

echo "Checking GraphQL endpoint..."
graphql '"query { __typename }"' >/dev/null

echo "Importing users..."
graphql '"mutation { importUsers { success importedCount users { id username email name } } }"' > /tmp/import.json
cat /tmp/import.json
echo

IMPORTED_SUCCESS=$(php -r '$j=json_decode(file_get_contents("/tmp/import.json"), true); echo ($j["data"]["importUsers"]["success"] ?? false) ? "true" : "false";')
if [ "$IMPORTED_SUCCESS" != "true" ]; then
  echo "Import users failed"
  exit 1
fi

echo "Promoting admin..."
graphql '"mutation { promoteUserToAdmin(userId: \"1\") { id username isAdmin } }"' > /tmp/promote.json
cat /tmp/promote.json
echo

IS_ADMIN=$(php -r '$j=json_decode(file_get_contents("/tmp/promote.json"), true); echo ($j["data"]["promoteUserToAdmin"]["isAdmin"] ?? false) ? "true" : "false";')
if [ "$IS_ADMIN" != "true" ]; then
  echo "Promote user to admin failed"
  exit 1
fi

echo "Logging in..."
graphql '"mutation { loginUser(username: \"Bret\") { token user { id username isAdmin } } }"' > /tmp/login.json
cat /tmp/login.json
echo

TOKEN=$(php -r '$j=json_decode(file_get_contents("/tmp/login.json"), true); echo $j["data"]["loginUser"]["token"] ?? "";')
if [ -z "$TOKEN" ]; then
  echo "Login failed: token is empty"
  exit 1
fi

echo "Token acquired."

echo "Querying me..."
graphql '"query { me { id username email isAdmin } }"' "$TOKEN" > /tmp/me.json
cat /tmp/me.json
echo

ME_ID=$(php -r '$j=json_decode(file_get_contents("/tmp/me.json"), true); echo $j["data"]["me"]["id"] ?? "";')
if [ "$ME_ID" != "1" ]; then
  echo "Me query failed"
  exit 1
fi

echo "Creating task..."
graphql '"mutation { createTask(title: \"Prepare API docs\", description: \"Write GraphQL task docs\", assignedUserId: \"1\") { id title description status assignedUserId createdAt updatedAt } }"' "$TOKEN" > /tmp/create.json
cat /tmp/create.json
echo

TASK_ID=$(php -r '$j=json_decode(file_get_contents("/tmp/create.json"), true); echo $j["data"]["createTask"]["id"] ?? "";')
if [ -z "$TASK_ID" ]; then
  echo "Create task failed"
  exit 1
fi

echo "Task ID: $TASK_ID"

echo "Updating task status..."
graphql "\"mutation { updateTaskStatus(taskId: \\\"$TASK_ID\\\", status: \\\"in_progress\\\") { id title status updatedAt } }\"" "$TOKEN" > /tmp/update.json
cat /tmp/update.json
echo

UPDATED_STATUS=$(php -r '$j=json_decode(file_get_contents("/tmp/update.json"), true); echo $j["data"]["updateTaskStatus"]["status"] ?? "";')
if [ "$UPDATED_STATUS" != "in_progress" ]; then
  echo "Update task status failed"
  exit 1
fi

echo "Fetching my tasks..."
graphql '"query { myTasks { id title status assignedUserId } }"' "$TOKEN" > /tmp/mytasks.json
cat /tmp/mytasks.json
echo

TASK_FOUND=$(php -r '
$j=json_decode(file_get_contents("/tmp/mytasks.json"), true);
$tasks=$j["data"]["myTasks"] ?? [];
$found=false;
foreach ($tasks as $task) {
    if (($task["id"] ?? "") !== "") {
        $found=true;
        break;
    }
}
echo $found ? "true" : "false";
')
if [ "$TASK_FOUND" != "true" ]; then
  echo "My tasks query failed"
  exit 1
fi

echo "Fetching task history with retries because messenger is async..."

ATTEMPTS=10
SLEEP_SECONDS=2
HISTORY_COUNT=0

for i in $(seq 1 "$ATTEMPTS"); do
  graphql "\"query { taskHistory(taskId: \\\"$TASK_ID\\\") { eventType payload occurredAt } }\"" "$TOKEN" > /tmp/history.json
  cat /tmp/history.json
  echo

  HISTORY_COUNT=$(php -r '$j=json_decode(file_get_contents("/tmp/history.json"), true); echo count($j["data"]["taskHistory"] ?? []);')

  if [ "$HISTORY_COUNT" -ge 1 ]; then
    echo "Task history is available after attempt $i."
    break
  fi

  echo "Task history still empty, waiting ${SLEEP_SECONDS}s before retry $((i + 1))..."
  sleep "$SLEEP_SECONDS"
done

if [ "$HISTORY_COUNT" -lt 1 ]; then
  echo "Task history query failed after ${ATTEMPTS} attempts"
  exit 1
fi

echo "GraphQL flow test passed."