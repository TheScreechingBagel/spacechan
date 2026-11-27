# Spacechan

## Imageboard for Harbour Space

An imageboard is an online forum where images are central to posts, often alongside text and discussion. ([Wikipedia, “Imageboard”](https://en.wikipedia.org/wiki/Imageboard))

## what it does

- browse boards, threads, and replies
- create threads and replies with optional images; login required to post
- delete your own threads and replies
- admins manage boards and edit threads and replies
- replies bump threads to the top

```mermaid
---
config:
  class:
    hideEmptyMembersBox: true
---
classDiagram
    direction LR
    class User {
        id
        name
        email
        password
        is_admin
        threads()
        posts()
    }
    class Board {
        id
        slug
        title
        description?
        threads()
        canDelete()
    }
    class Thread {
        id
        board_id FK
        user_id FK
        name?
        subject?
        text
        bumped_at
        board()
        user()
        posts()
        isOwnedBy(user)
        canDelete(user)
    }
    class Post {
        id
        thread_id FK
        user_id FK
        name?
        text
        thread()
        user()
        isOwnedBy(user)
        canDelete(user)
    }
    class Media {
        <<Media Library>>
    }

    Board --> Thread : hasMany threads()
    Thread --> Board : belongsTo board()
    User --> Thread : hasMany threads()
    Thread --> User : belongsTo user()
    Thread --> Post : hasMany posts()
    Post --> Thread : belongsTo thread()
    User --> Post : hasMany posts()
    Post --> User : belongsTo user()
    Thread ..> Media : image
    Post ..> Media : image
```

Each class lists its columns, then its methods: the relationships and the business rules. `?` marks an optional column and `FK` a foreign key. Timestamps are left out. `Media` stands for Media Library's own table: a thread or a post can have one optional image.

<details>
<summary>Database tables (ER diagram)</summary>

```mermaid
erDiagram
    Board ||--o{ Thread : "threads() / board()"
    User ||--o{ Thread : "threads() / user()"
    User ||--o{ Post : "posts() / user()"
    Thread ||--o{ Post : "posts() / thread()"
    Thread ||--o| Media : "image"
    Post ||--o| Media : "image"

    User {
        id id PK
        string name
        string email UK
        string password
        boolean is_admin
    }
    Board {
        id id PK
        string slug UK
        string title
        string description "nullable"
    }
    Thread {
        id id PK
        id board_id FK
        id user_id FK
        string name "nullable"
        string subject "nullable"
        text text
        datetime bumped_at
    }
    Post {
        id id PK
        id thread_id FK
        id user_id FK
        string name "nullable"
        text text
    }
    Media {
        id id PK
        string model_type "Thread or Post"
        id model_id
        string collection_name "image"
        string file_name
    }
```

Each line is one relationship, labelled with its `hasMany` and `belongsTo` methods. `Media` is Media Library's table: it points to a thread or a post through `model_type` and `model_id` (a polymorphic relationship), and the `image` collection holds at most one image. Timestamps are left out.

</details>

## Run

```sh
composer run setup
php artisan migrate:fresh --seed
composer run dev
```

Open [http://localhost:8000](http://localhost:8000).

Seeded accounts, both with the password `password`:

- Admin: `Admin` — `admin@example.com`
- Test: `Test User` — `test@example.com`

## next additions

- guest posting
- poster ids per thread
- sage replies and bump limits
