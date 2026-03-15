# Task Manager API

A Symfony-based task manager API built with Domain-Driven Design, GraphQL, PostgreSQL, Doctrine, Symfony Messenger, and Docker. The project supports task management workflows, user import from JSONPlaceholder, asynchronous task event processing, and local development through Docker Compose. It also includes unit tests, Docker smoke tests, GraphQL flow checks, and CI quality validation.

## Table of Contents

- [Stack](#stack)
- [Run with Docker](#run-with-docker)
- [Available Services](#available-services)
- [pgAdmin Login](#pgadmin-login)
- [PostgreSQL Connection](#postgresql-connection)
- [GraphQL Communication](#graphql-communication)
- [Example Flow](#example-flow)
  - [1. Import Users](#1-import-users)
  - [2. Promote User to Admin](#2-promote-user-to-admin)
  - [3. Log In](#3-log-in)
  - [4. Get Current User](#4-get-current-user)
  - [5. Create Task](#5-create-task)
  - [6. Update Task Status](#6-update-task-status)
  - [7. Get My Tasks](#7-get-my-tasks)
  - [8. Get Task History](#8-get-task-history)
- [Authorization and Edge Cases](#authorization-and-edge-cases)
  - [General Rules](#general-rules)
  - [Endpoint Rules](#endpoint-rules)
- [Tests](#tests)
  - [Run Unit Tests](#run-unit-tests)
  - [Run Docker E2E Smoke Tests](#run-docker-e2e-smoke-tests)
  - [Run Full GraphQL Flow Test](#run-full-graphql-flow-test)
- [Notes](#notes)
- [Stop Containers](#stop-containers)

## Stack

- Symfony
- GraphQL via OverblogGraphQLBundle
- PostgreSQL
- Doctrine ORM / Migrations
- Symfony Messenger
- Docker / Docker Compose
- pgAdmin
- PHPUnit
- PHP CS Fixer
- PHPStan
- GitHub Actions CI

## Run with Docker

From the project root, start the development profile:

```bash
docker compose -f infrastructure.yml --env-file .env.docker --profile dev up --build
````

For the production profile:

```bash
docker compose -f infrastructure.yml --env-file .env.docker --profile prod up --build
```

## Available Services

* App (dev): `http://127.0.0.1:18081`
* GraphQL endpoint (dev): `http://127.0.0.1:18081/api/graphql/`
* App (prod): `http://127.0.0.1:18082`
* GraphQL endpoint (prod): `http://127.0.0.1:18082/api/graphql/`
* pgAdmin: `http://127.0.0.1:18080`
* PostgreSQL host port: `127.0.0.1:15432`

## pgAdmin Login

* Email: `info@itsharppro.com`
* Password: `admin123`

## PostgreSQL Connection

From pgAdmin or any SQL client:

* Host inside Docker network: `postgres`
* Host from local machine: `127.0.0.1`
* Port inside Docker network: `5432`
* Port from local machine: `15432`
* Database: `task_manager`
* User: `task_manager_user`
* Password: `task_manager_password`

## GraphQL Communication

The API is exposed through a single GraphQL endpoint.

### Development

```text
POST http://127.0.0.1:18081/api/graphql/
```

### Production profile

```text
POST http://127.0.0.1:18082/api/graphql/
```

Recommended headers:

```http
Content-Type: application/json
Authorization: Bearer <token>
```

Authentication is token-based.

First log in with `loginUser`, then use the returned token in the `Authorization` header for protected operations.

---

## Example Flow

### 1. Import Users

#### Mutation

```graphql
mutation {
  importUsers {
    success
    importedCount
    users {
      id
      name
      email
      username
    }
  }
}
```

#### Example response

```json
{
  "data": {
    "importUsers": {
      "success": true,
      "importedCount": 10,
      "users": [
        {
          "id": "1",
          "name": "Leanne Graham",
          "email": "Sincere@april.biz",
          "username": "Bret"
        }
      ]
    }
  }
}
```

### 2. Promote User to Admin

#### Mutation

```graphql
mutation {
  promoteUserToAdmin(userId: "1") {
    id
    name
    username
    email
    isAdmin
  }
}
```

#### Example response

```json
{
  "data": {
    "promoteUserToAdmin": {
      "id": "1",
      "name": "Leanne Graham",
      "username": "Bret",
      "email": "Sincere@april.biz",
      "isAdmin": true
    }
  }
}
```

### 3. Log In

#### Mutation

```graphql
mutation {
  loginUser(username: "Bret") {
    token
    user {
      id
      name
      username
      email
      isAdmin
    }
  }
}
```

#### Example response

```json
{
  "data": {
    "loginUser": {
      "token": "eyJ1c2VySWQiOiIxIiwiaWF0IjoxNzczNTAxMTM4fQ==.2fde2017ec1f0f9337359dd78d48e5078ed3233a8e7ae7a537caf8cae6520cb0",
      "user": {
        "id": "1",
        "name": "Leanne Graham",
        "username": "Bret",
        "email": "Sincere@april.biz",
        "isAdmin": true
      }
    }
  }
}
```

Save the token and use it in the next requests:

```http
Authorization: Bearer eyJ1c2VySWQiOiIxIiwiaWF0IjoxNzczNTAxMTM4fQ==.2fde2017ec1f0f9337359dd78d48e5078ed3233a8e7ae7a537caf8cae6520cb0
```

### 4. Get Current User

#### Query

```graphql
query {
  me {
    id
    name
    username
    email
    isAdmin
  }
}
```

#### Example response

```json
{
  "data": {
    "me": {
      "id": "1",
      "name": "Leanne Graham",
      "username": "Bret",
      "email": "Sincere@april.biz",
      "isAdmin": true
    }
  }
}
```

### 5. Create Task

#### Mutation

```graphql
mutation {
  createTask(
    title: "Prepare API docs"
    description: "Write GraphQL task docs"
    assignedUserId: "1"
  ) {
    id
    title
    description
    status
    assignedUserId
    createdAt
    updatedAt
  }
}
```

#### Example response

```json
{
  "data": {
    "createTask": {
      "id": "019cece8-097c-731d-8ae9-4ebe62958a7f",
      "title": "Prepare API docs",
      "description": "Write GraphQL task docs",
      "status": "todo",
      "assignedUserId": "1",
      "createdAt": "2026-03-14T15:12:35+00:00",
      "updatedAt": "2026-03-14T15:12:35+00:00"
    }
  }
}
```

### 6. Update Task Status

#### Mutation

```graphql
mutation {
  updateTaskStatus(taskId: "019cece8-097c-731d-8ae9-4ebe62958a7f", status: "in_progress") {
    id
    title
    status
    updatedAt
  }
}
```

#### Example response

```json
{
  "data": {
    "updateTaskStatus": {
      "id": "019cece8-097c-731d-8ae9-4ebe62958a7f",
      "title": "Prepare API docs",
      "status": "in_progress",
      "updatedAt": "2026-03-14T15:13:10+00:00"
    }
  }
}
```

### 7. Get My Tasks

#### Query

```graphql
query {
  myTasks {
    id
    title
    status
    assignedUserId
  }
}
```

#### Example response

```json
{
  "data": {
    "myTasks": [
      {
        "id": "019cece8-097c-731d-8ae9-4ebe62958a7f",
        "title": "Prepare API docs",
        "status": "in_progress",
        "assignedUserId": "1"
      }
    ]
  }
}
```

### 8. Get Task History

#### Query

```graphql
query {
  taskHistory(taskId: "019cece8-097c-731d-8ae9-4ebe62958a7f") {
    eventType
    payload
    occurredAt
  }
}
```

#### Example response

```json
{
  "data": {
    "taskHistory": [
      {
        "eventType": "App\\Domain\\Task\\Event\\TaskCreatedEvent",
        "payload": "{\"taskId\":\"019cece8-097c-731d-8ae9-4ebe62958a7f\",\"title\":\"Prepare API docs\",\"status\":\"todo\",\"assignedUserId\":\"1\"}",
        "occurredAt": "2026-03-14T15:12:35+00:00"
      },
      {
        "eventType": "App\\Domain\\Task\\Event\\TaskStatusUpdatedEvent",
        "payload": "{\"taskId\":\"019cece8-097c-731d-8ae9-4ebe62958a7f\",\"previousStatus\":\"todo\",\"newStatus\":\"in_progress\"}",
        "occurredAt": "2026-03-14T15:13:10+00:00"
      }
    ]
  }
}
```

## Authorization and Edge Cases

### General Rules

The API uses bearer-token authentication for protected operations.

Protected endpoints require this header:

```http
Authorization: Bearer <token>
```

If the token is missing, malformed, invalid, or belongs to a user that no longer exists, the request is treated as unauthorized.

Access levels in the current application:

* **Guest**: not authenticated
* **Authenticated user**: logged in with a valid token
* **Admin**: authenticated user with `isAdmin = true`

Typical failure categories:

* **Unauthorized**: no valid token provided
* **Forbidden**: user is authenticated but does not have permission
* **Not found**: requested user or task does not exist
* **Validation or domain error**: invalid input or unsupported status transition

### Endpoint Rules

#### `importUsers`

**Access:** public in the current implementation.

**Behavior:**

* Fetches users from JSONPlaceholder
* Saves only users that do not already exist locally
* Returns `success`, `importedCount`, and imported user list

**Edge cases:**

* Running the mutation multiple times is safe
* If all users are already imported, `importedCount` may be `0`
* If the external API fails or returns an unexpected response, the mutation fails

#### `promoteUserToAdmin(userId)`

**Access:** public in the current implementation.

**Behavior:**

* Promotes the selected user to admin
* Persists the updated user record

**Edge cases:**

* If the user does not exist, the mutation fails
* Promoting an already-admin user is effectively idempotent
* This mutation is intentionally simple for the exercise and should not be treated as production-grade access control

#### `loginUser(username)`

**Access:** public.

**Behavior:**

* Finds a user by username
* Returns a signed token and the matched user

**Edge cases:**

* If the username is not found, the mutation fails
* The current login flow does not use passwords
* The returned token is required for protected operations

#### `me`

**Access:** authenticated user required.

**Behavior:**

* Resolves the current user from the bearer token

**Edge cases:**

* Missing `Authorization` header results in unauthorized behavior
* Invalid token signature results in unauthorized behavior
* A valid token for a deleted or missing user behaves as unauthorized

#### `createTask`

**Access:** admin only.

**Behavior:**

* Creates a task assigned to the provided user
* Persists the task
* Dispatches task domain events asynchronously

**Edge cases:**

* Guests are unauthorized
* Authenticated non-admin users are forbidden
* Invalid task data may fail validation or domain rules
* Task history may not appear immediately because events are processed asynchronously

#### `updateTaskStatus(taskId, status)`

**Access:** admin or assigned user.

**Behavior:**

* Finds the task
* Verifies access rights
* Applies the target status transition
* Saves the updated task
* Dispatches task events asynchronously

**Edge cases:**

* If the task does not exist, the mutation fails
* If the current user is neither admin nor the assigned user, the mutation is forbidden
* Invalid status values fail domain validation
* Unsupported transitions fail with a domain error
* History may appear with delay because Messenger processes events asynchronously

#### `myTasks`

**Access:** authenticated user required.

**Behavior:**

* Returns only tasks assigned to the current authenticated user

**Edge cases:**

* Guests are unauthorized
* Admin users still receive only tasks assigned to themselves in this query
* Use `allTasks` for a full task list

#### `allTasks`

**Access:** admin only.

**Behavior:**

* Returns all tasks in the system

**Edge cases:**

* Guests are unauthorized
* Authenticated non-admin users are forbidden

#### `taskHistory(taskId)`

**Access:** admin or assigned user.

**Behavior:**

* Finds the task first
* Verifies whether the current user can access history for that task
* Returns persisted task lifecycle events from the event store

**Edge cases:**

* If the task does not exist, the query fails
* Admin can view history for any task
* Regular users can view history only for their own tasks
* Guests are unauthorized
* Immediately after task creation or status update, history can be temporarily empty due to asynchronous event handling

## Tests

The project includes unit tests, Docker smoke tests, GraphQL flow checks, and CI quality validation.

### Run Unit Tests

From the `TaskManager` directory:

```bash
php bin/phpunit
```

For more readable output:

```bash
php bin/phpunit --testdox
```

### Run Docker E2E Smoke Tests

From the repository root, start the development environment:

```bash
docker compose -f infrastructure.yml --env-file .env.docker --profile dev up -d --build
```

Run the dev smoke test:

```bash
bash tests-e2e/dev-smoke.sh
```

This checks:

* GraphQL endpoint availability
* pgAdmin availability

Run the prod smoke test:

```bash
docker compose -f infrastructure.yml --env-file .env.docker --profile prod up -d --build
bash tests-e2e/prod-smoke.sh
```

This checks:

* GraphQL endpoint availability in the production profile

### Run Full GraphQL Flow Test

From the repository root:

```bash
bash tests-e2e/graphql-flow.sh
```

This script verifies the main application flow:

* import users
* promote a user to admin
* log in and obtain a bearer token
* query the authenticated user with `me`
* create a task
* update task status
* fetch current user tasks
* fetch task history

The task history check includes retries because task lifecycle events are processed asynchronously through Symfony Messenger.

## Notes

* `importUsers` loads users from JSONPlaceholder into the local database and skips already imported records
* `loginUser` returns a bearer token required for protected operations
* `me` resolves the currently authenticated user from the bearer token
* `createTask` is restricted to admin users
* `updateTaskStatus` is allowed for admins and for the user assigned to the task
* `myTasks` returns only tasks assigned to the currently authenticated user
* `allTasks` is restricted to admin users
* `taskHistory` returns persisted task lifecycle events from the event store
* Task history visibility is restricted:

  * admin can view history for all tasks
  * regular users can view history only for their own tasks
* Task lifecycle events are processed asynchronously through Symfony Messenger, so history may not be visible immediately after a mutation

## Stop Containers

```bash
docker compose -f infrastructure.yml --env-file .env.docker down
```

To also remove volumes:

```bash
docker compose -f infrastructure.yml --env-file .env.docker down -v
```
