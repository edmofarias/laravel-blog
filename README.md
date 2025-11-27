# Blog API

A small Laravel API project for posts and comments.

## Requirements

- PHP 8.2+
- Composer
- MySQL (or another supported database)

## Quick setup (local / Windows)

1. Clone the repository
   ```sh
   git clone https://github.com/edmofarias/laravel-blog
   ```

2. Install PHP dependencies
   ```sh
   composer install
   ```

3. Create environment file
   - macOS / Linux:
     ```sh
     cp .env.example .env
     ```

5. Configure the database
   - Edit the .env file and update DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD.

6. Run migrations (and optionally seed)
   ```sh
   php artisan migrate
   php artisan db:seed   # optional
   ```

7. Run the app locally
   ```sh
   php artisan serve
   ```
   By default the app will be available at http://127.0.0.1:8000.

## Running tests

- Use the Artisan test runner:
  ```sh
  php artisan test
  ```

## API endpoints (examples)

Auth
- POST /api/register — register a new user (public)
- POST /api/login — login and receive token (public)
- POST /api/logout — logout (protected: auth:sanctum)

Posts (public)
- GET /api/posts — list posts
- GET /api/posts/{id} — show a single post
- GET /api/posts/users/{userId} — list posts for a specific user

Posts (protected — require auth:sanctum)
- POST /api/posts — create a new post
- PUT /api/posts/{id} — update a post
- DELETE /api/posts/{id} — delete a post

Comments
- GET /api/posts/{postId}/comments — list comments for a post (public)
- POST /api/posts/{postId}/comments — create a comment for a post (protected)
- PUT /api/comments/{id} — update a comment (protected)
- DELETE /api/comments/{id} — delete a comment (protected)
