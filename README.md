# Task Lists

A multi-list to-do application built with Laravel 13, Livewire 4, and Flux UI. Create and manage multiple named to-do lists, share any list as a preset via a link, and let others import it as their own copy to customise.

Built as a hands-on learning project covering Laravel's core ecosystem — Eloquent, Policies, Middleware, Livewire reactive components, and OAuth authentication.

---

## Features

- **Multiple to-do lists** — create, name, and describe as many lists as you need
- **Reactive item management** — add, complete, and delete items instantly without page reloads
- **Shareable presets** — mark any list as a preset and generate a 7-day share link
- **Import flow** — anyone with a link can preview a shared list and import it as their own
- **Google OAuth** — sign in with Google or with email and password
- **Account linking** — signing in with Google automatically links to an existing account with the same email
- **Rate limiting** — share generation and imports are rate-limited per user
- **Security headers** — every response includes `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, and `Permissions-Policy` headers

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.4 |
| Reactive UI | Livewire 4 |
| UI Components | Livewire Flux |
| Styling | Tailwind CSS |
| Auth scaffolding | Laravel Breeze |
| OAuth | Laravel Socialite (Google) |
| Asset bundling | Vite |
| Database | SQLite (development) |
| Testing | Pest |

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm
- A Google Cloud project with OAuth 2.0 credentials ([setup guide](https://console.cloud.google.com))

---

## Installation

**1 — Clone the repository**

```bash
git clone https://github.com/FayRd/task-lists.git
cd task-lists
```

**2 — Install PHP dependencies**

```bash
composer install
```

**3 — Install Node dependencies**

```bash
npm install
```

**4 — Set up your environment file**

```bash
cp .env.example .env
php artisan key:generate
```

**5 — Add your Google OAuth credentials to `.env`**

```env
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

> In your Google Cloud Console, add `http://localhost:8000/auth/google/callback` as an authorised redirect URI.

**6 — Run database migrations**

```bash
php artisan migrate
```

**7 — Start the development server**

```bash
composer run dev
```

This starts the PHP server, Vite asset watcher, and queue worker in parallel. Visit `http://localhost:8000`.

---

## Project Structure

```
app/
  Http/
    Controllers/
      Auth/GoogleController.php   — Google OAuth redirect and callback
      ListShareController.php     — Share generation, preview, import, and claim
    Middleware/
      SecureHeaders.php           — Adds security headers to every response
    Requests/
      StoreListRequest.php        — Form request validation for list creation
  Models/
    User.php                      — Has many TodoLists
    TodoList.php                  — Belongs to User, has many TodoItems and one ListShare
    TodoItem.php                  — Belongs to TodoList
    ListShare.php                 — Belongs to TodoList, holds the share token
  Policies/
    TodoListPolicy.php            — Ownership checks for view, update, and delete

resources/views/
  components/                     — Reusable Blade components (buttons, inputs, cards)
  pages/todo-lists/
    index.blade.php               — Lists index — Livewire 4 single-file component
    show.blade.php                — List detail — Livewire 4 single-file component
  share/
    preview.blade.php             — Public share preview page

database/
  migrations/                     — Schema for todo_lists, todo_items, list_shares
  factories/
    TodoListFactory.php           — Faker factory for test data
```

---

## Usage

### Creating lists
Register or log in, then navigate to **My Lists** in the sidebar. Click **New List**, enter a name and optional description, and click **Create**.

### Managing items
Click any list to open it. Type in the input at the top and press **Enter** or click **Add**. Click the checkbox to complete an item, or **Remove** to delete it.

### Sharing a list
On any list's detail page, click **Share as preset**. A shareable URL will appear — copy and send it to anyone. The link expires after 7 days and can be regenerated at any time.

### Importing a shared list
Visit a share URL to see a read-only preview of the list and its items. Click **Import this list into my account** to clone it. If you're not logged in, you'll be redirected to login and then the import completes automatically.

---

## Security

- All list routes are protected by `auth` and `verified` middleware
- `TodoListPolicy` enforces ownership — users can only modify their own lists
- Share tokens are 48-character random strings stored in the database with a 7-day expiry
- Rate limiting is applied to share generation (10/hour) and imports (20/hour) per user
- Security headers are applied globally via `SecureHeaders` middleware
- Google OAuth account linking matches on email address — no duplicate accounts

---

## Running Tests

```bash
php artisan test
```

Or with Pest directly:

```bash
./vendor/bin/pest
```

---

## License

[MIT](LICENSE)
