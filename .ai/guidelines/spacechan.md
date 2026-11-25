# spacechan

A small Laravel 13 school project for a backend (Laravel) course, built with Blade, Tailwind 4 via Vite, SQLite and Pest. It will never have more than about 30 users.

The project started from the course starter pack. Login, register and profile pages (the `Auth\` controllers and `Userzone\ProfileController`) already work, so don't rebuild them. The Debugbar is installed for checking queries.

The course lessons are in `course/`, a local folder that isn't in git, and the grading criteria are in `course/00-assessment-criteria.md`. When unsure how to build something, follow the matching lesson.

## Keep it simple

The user presents this project and can be asked about any line of it, so every line has to be easy to explain. Understandable beats clever, complete or "best practice".

- Use the plainest Laravel way that works, as the lessons show it. Don't add service classes, repositories, actions, DTOs, interfaces, events, observers or custom traits unless the user asks.
- Don't guard against things that can't realistically happen with a handful of known users. Skip rate limiting, retries, soft deletes, locking and defensive checks on data the app controls, unless asked.
- Use plain HTML forms and server-rendered Blade pages. No Livewire, Inertia or JS frameworks.
- Styling only needs to look tidy with Tailwind utility classes. Visual polish is not a goal.
- Don't add composer or npm packages without asking. `spatie/laravel-medialibrary` is already installed.
- Keep diffs small. If a task starts sprawling, or a request would add a lot of complexity, stop and offer the simpler version first.
- After each change, sum it up in two or three plain sentences the user could repeat to the teacher. Name the Laravel concept involved (for example, "eager loading with `with()` to avoid N+1 queries").

## Naming

Full list: `course/09-wb-10-naming-conventions.md`.

- Models are one singular word in PascalCase: `Post`, and `Orderitem` rather than `OrderItem`. Tables and view folders are plural and lowercase (`posts`, `resources/views/posts/`). Controllers are named `PostController`.
- Columns are lowercase and don't repeat the model name: `text` inside `Comment`, not `comment_text`. Foreign keys are `modelname_id`, written in full.
- `belongsTo` methods are singular (`author()`), while `hasMany` and `belongsToMany` methods are plural (`posts()`). Always define the inverse too. A pivot table is named after both models, singular and in alphabetical order (`role_user`).
- A kind of user (admin, author, moderator) is never its own model. It is the `User` model with a column such as `is_admin`.

## Zones and routes

Every page belongs to one zone: public, user (logged in) or admin. See `course/04-wb-03-three-access-levels.md`.

- `routes/web.php` keeps the starter pack's commented blocks (public, authentication, user zone) and adds an admin block last. Every route has a name, and views link with `route()`.
- Use `Route::resource()` with the seven standard method names (`->only()` or `->except()` for partial ones), or a single-action controller for a one-off page. An action that doesn't fit the seven, such as approve or publish, becomes a second resource with its own small controller instead of an eighth method. Publishing a post is storing a publication.
- Public controllers (`App\Http\Controllers\PostController`) usually only have `index` and `show`.
- User-zone controllers live in `App\Http\Controllers\Userzone\` with views in `resources/views/userzone/<plural>/`. Their routes go inside the existing `auth` group as `Route::prefix('user')->name('userzone.')`.
- Admin controllers live in `App\Http\Controllers\Admin\` with views in `resources/views/admin/<plural>/`. Their routes go in `Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])`. `admin` is the `EnsureUserIsAdmin` middleware, registered as an alias in `bootstrap/app.php`.

## Models and controllers

- Create models with `php artisan make:model Post -mf`. A model class stays small: `HasFactory`, `protected $guarded = [];`, its relationships and its business rules.
- A business rule is one method on the model, for example `public function canChange(User $user): bool`. The controller checks it first with `if (! $post->canChange(auth()->user())) { abort(403); }`, and the view calls the same method to hide the button.
- Controller methods follow the five steps in `course/38-ct-07-controller-flow-synthesis.md` (authorise, load, validate, change, respond) and skip the steps that don't apply. Use route-model binding (`show(Post $post)`) and `$request->validate([...])` with rules in array syntax. Redirect to a named route after every change.
- The owner of a new record comes from `auth()->id()`, never from a form field.
- List pages load the relationships they display with `with()`. Keep `Model::preventLazyLoading(! app()->isProduction())` on in `AppServiceProvider`, and add it if it's missing. Check the queries in the Debugbar or with the `debug-using-debugbar` skill.

## Views

- One view per controller method: `PostController@index` renders `resources/views/posts/index.blade.php`.
- Public pages use a class-based layout component (`<x-site-layout title="...">`) with separate header and footer components. The site menu is defined once, in the layout component class, and shown in both the header and the footer. User-zone pages use the starter pack's `<x-app-layout>`, and auth pages use `<x-guest-layout>`.
- Form inputs are file-based components (`<x-form-text-input label="Title" name="title" />`) that show the label, the `old()` input and the `@error` message. Forms use `@csrf`, plus `@method('PUT')` or `@method('DELETE')` where needed.
- Lists use `@forelse` with an empty state. After a change, redirect with `->with('success', '...')`. The layout shows `success` and `error` flash messages.
- Images use Media Library as in `course/69-md-04-store-images-with-media-library.md`. Show a fallback image only where a page expects an image, such as a thumbnail. When an optional image is missing, leave it out.

## Database and seeding

- Every model has a migration and a factory. `DatabaseSeeder` seeds everything through factories, in foreign-key order. It always creates an admin user with `admin@example.com` / `password`.
- `php artisan migrate:fresh --seed` must work on a fresh clone at all times, because that's how the app gets installed. Run it after changing migrations, factories or seeders.
- The SQLite database is `database/database.sqlite`. Sessions and cache use files, and the queue uses the database. Mail goes to the log at `storage/logs/laravel.log`.

## Tests

See `course/86-ts-05-organise-your-tests.md` and `course/87-ag-09-judge-the-tests-an-agent-writes.md`.

- Write one Pest file per controller, named after it and sorted into folders by zone: `tests/Feature/PostControllerTest.php`, `tests/Feature/Admin/PostControllerTest.php`. Use `RefreshDatabase` and factories. Leave the starter pack's `tests/Feature/Auth` and `ProfileTest` as they are.
- Name each test as a sentence: `it('refuses to update the post of another user')`.
- For each page, cover three things: it works for whoever may use it, it's closed to whoever may not, and bad input comes back with errors. Assert that the input is rejected, not the exact message text.
- Don't keep tests that only repeat the code, test Laravel itself, or can't fail. When a test fails, fix the code or ask. Never change the test just to make it pass.

## Running it

- `composer run dev` serves the app at http://localhost:8000 and runs the Vite, queue and log processes.
- `composer run setup` does the first-time install: dependencies, `.env`, app key, migrations and the asset build.

## Version control (jj)

This is a colocated jj and git repo. Use `jj`, not `git`, for anything that changes history or the working copy.

- Work in small logical pieces. If describing a piece needs the word "and", it's probably two pieces. When a piece is done, run `jj describe -m "<message>"`, then `jj new` before starting the next one.
- Commit messages are one short line, all lowercase and imperative, with no prefix, emoji, body or trailers. For example: `add post model`, `validate the reply form`, `eager load authors on the board page`.
- The user curates history themselves. Don't squash, split, rebase, abandon, push or move bookmarks unless asked.
