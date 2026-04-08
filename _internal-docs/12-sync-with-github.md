# 🧠 Big Picture Architecture

You are implementing:

```
User → Click "Connect GitHub"
     → GitHub OAuth screen
     → Redirect back with code
     → Exchange code for access token
     → Store token
     → Use token to call GitHub API (repos, commits, etc.)
```

---

# ✅ STEP 1: Create GitHub OAuth App

Go to:
👉 [https://github.com/settings/developers](https://github.com/settings/developers)

### Create new OAuth App:

* **Application Name**: GraceSoft Story
* **Homepage URL**:

  ```
  http://localhost:8000
  ```
* **Authorization callback URL**:

  ```
  http://localhost:8000/auth/github/callback
  ```

After creating:

👉 Copy:

* `Client ID`
* `Client Secret`

---

# ✅ STEP 2: Add ENV Variables

In `.env`:

```env
GITHUB_CLIENT_ID=your_client_id
GITHUB_CLIENT_SECRET=your_client_secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback
```

---

# ✅ STEP 3: Install Laravel Socialite

```bash
composer require laravel/socialite
```

---

# ✅ STEP 4: Configure services.php

```php
// config/services.php

'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT_URI'),
],
```

---

# ✅ STEP 5: Create Routes

```php
// routes/web.php

use App\Http\Controllers\Auth\GitHubController;

Route::get('/auth/github', [GitHubController::class, 'redirect']);
Route::get('/auth/github/callback', [GitHubController::class, 'callback']);
```

---

# ✅ STEP 6: Create Controller

```bash
php artisan make:controller Auth/GitHubController
```

---

# ✨ STEP 7: Redirect to GitHub

```php
use Laravel\Socialite\Facades\Socialite;

public function redirect()
{
    return Socialite::driver('github')
        ->scopes(['repo', 'read:user'])
        ->redirect();
}
```

### 🔥 Important scopes:

* `repo` → access private repos
* `read:user` → basic profile

---

# 🔁 STEP 8: Handle Callback

```php
use Illuminate\Support\Facades\Auth;
use App\Models\User;

public function callback()
{
    $githubUser = Socialite::driver('github')->user();

    $user = Auth::user(); // assuming already logged in

    // Save GitHub connection
    $user->update([
        'github_id' => $githubUser->getId(),
        'github_token' => $githubUser->token,
        'github_username' => $githubUser->getNickname(),
    ]);

    return redirect('/dashboard')->with('success', 'GitHub connected!');
}
```

---

# 🧱 STEP 9: Update Users Table

```bash
php artisan make:migration add_github_fields_to_users_table
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('github_id')->nullable();
    $table->text('github_token')->nullable();
    $table->string('github_username')->nullable();
});
```

```bash
php artisan migrate
```

---

# 🔐 STEP 10: Secure Token Storage (IMPORTANT)

Instead of plain text:

```php
use Illuminate\Support\Facades\Crypt;

$user->update([
    'github_token' => Crypt::encryptString($githubUser->token),
]);
```

---

# 🚀 STEP 11: Use GitHub API Programmatically

Now your app behaves like VS Code / Netlify.

Example: Fetch repos

```php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

$token = Crypt::decryptString($user->github_token);

$response = Http::withToken($token)
    ->get('https://api.github.com/user/repos');

$repos = $response->json();
```

---

# 📦 STEP 12: Sync Repositories (Your Story Core)

Create a job:

```bash
php artisan make:job SyncRepositories
```

```php
public function handle()
{
    $token = Crypt::decryptString($this->user->github_token);

    $repos = Http::withToken($token)
        ->get('https://api.github.com/user/repos')
        ->json();

    foreach ($repos as $repo) {
        // save to DB
    }
}
```

---

# 🔁 STEP 13: Add “Connect GitHub” Button

```html
<a href="/auth/github" class="btn btn-dark">
    Connect GitHub
</a>
```

---

# 🔄 STEP 14: Disconnect Flow

```php
public function disconnect()
{
    $user = Auth::user();

    $user->update([
        'github_id' => null,
        'github_token' => null,
    ]);

    return back()->with('success', 'Disconnected');
}
```

---

# ⚡ STEP 15: Advanced (What VS Code / Netlify ALSO Do)

## 1. Refresh Tokens (GitHub Apps only)

OAuth Apps don’t auto-refresh → token is long-lived.

## 2. Webhooks (VERY IMPORTANT for you)

Instead of polling:

👉 GitHub → sends events when:

* push
* commit
* repo changes

You should implement:

```bash
php artisan make:controller Webhook/GitHubWebhookController
```

---

## 3. Queue-Based Sync (You already doing this 👍)

* Sync repos → queue
* Sync commits → queue
* Labeling → async

---

## 4. Multi-account support

Later:

```php
github_accounts table
```

---

# 🧩 STEP 16: Recommended Structure (Clean Architecture)

```
app/
 ├── Services/
 │    └── GitHubService.php
 ├── Jobs/
 │    └── SyncRepositories.php
 ├── Actions/
 │    └── ConnectGitHubAccount.php
```

---

# 🔥 BONUS: Why This Matches VS Code / Netlify

They ALL use:

* OAuth flow (same as above)
* Store token
* Call GitHub API
* Use webhooks for updates

👉 You are literally building the same integration layer.

---

# 🧠 What You Should Build Next (GraceSoft Story Specific)

Since your product = **timeline from commits**

Next steps:

1. ✅ Repo sync
2. ✅ Commit sync
3. ✅ Webhook ingestion
4. ✅ Timeline builder
5. ✅ Label system (you already have)
