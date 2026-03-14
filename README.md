# Task Manager API

A Symfony-based task manager API built with DDD structure, GraphQL communication, PostgreSQL, Doctrine, Messenger, and Docker.

## Stack

- Symfony
- GraphQL via OverblogGraphQLBundle
- PostgreSQL
- Doctrine ORM / Migrations
- Symfony Messenger
- Docker / Docker Compose
- pgAdmin

## Run with Docker

From the project root:

```bash
docker compose -f infrastructure.yml --env-file .env.docker --profile dev up --build
````

## Available services

* App: `http://127.0.0.1:18081`
* GraphQL endpoint: `http://127.0.0.1:18081/api/graphql/`
* pgAdmin: `http://127.0.0.1:18080`
* PostgreSQL host port: `127.0.0.1:15432`

## pgAdmin login

* Email: `info@itsharppro.com`
* Password: `admin123`

## PostgreSQL connection

From pgAdmin or any SQL client:

* Host: `postgres` inside Docker network
* Host: `127.0.0.1` from local machine
* Port: `5432` inside Docker network
* Port: `15432` from local machine
* Database: `task_manager`
* User: `task_manager_user`
* Password: `task_manager_password`

## GraphQL communication

The API is exposed through a single GraphQL endpoint:

```text
POST http://127.0.0.1:18081/api/graphql/
```

Recommended headers:

```http
Content-Type: application/json
Authorization: Bearer <token>
```

Authentication is token-based.
First log in with `loginUser`, then use the returned token in the `Authorization` header for protected operations.

---

# Example flow

## 1. Import users

### Mutation

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

### Example response

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

## 2. Promote user to admin

### Mutation

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

### Example response

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

## 3. Log in

### Mutation

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

### Example response

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

## 4. Get current user

### Query

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

### Example response

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

## 5. Create task

### Mutation

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

### Example response

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

## 6. Update task status

### Mutation

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

### Example response

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

## 7. Get my tasks

### Query

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

### Example response

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

## 8. Get task history

### Query

```graphql
query {
  taskHistory(taskId: "019cece8-097c-731d-8ae9-4ebe62958a7f") {
    eventType
    payload
    occurredAt
  }
}
```

### Example response

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

## Notes

* `importUsers` loads users from JSONPlaceholder into the local database.
* `promoteUserToAdmin` allows a selected user to gain admin privileges.
* `loginUser` returns an auth token.
* `me` resolves the currently authenticated user from the bearer token.
* `createTask` is intended for admin access.
* `myTasks` returns tasks assigned to the currently authenticated user.
* `taskHistory` returns persisted task lifecycle events from the event store.

## Stop containers

```bash
docker compose -f infrastructure.yml --env-file .env.docker down
```

To also remove volumes:

```bash
docker compose -f infrastructure.yml --env-file .env.docker down -v
```