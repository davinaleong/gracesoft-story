# Schemas from Other GraceSoft Products for Syncing

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 120);
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('stripe_customer_id')->nullable()->unique();
            $table->timestamps();

            $table->index('owner_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 80);
            $table->string('slug', 40)->unique();
            $table->string('stripe_price_id')->nullable()->unique();
            $table->string('stripe_product_id')->nullable();
            $table->unsignedInteger('max_users')->default(1);
            $table->unsignedInteger('max_items')->nullable();
            $table->unsignedInteger('max_replies')->nullable();
            $table->timestamps();
        });

        $now = now();
        $envOrNull = static function (string $key): ?string {
            $value = trim((string) env($key, ''));

            return $value !== '' ? $value : null;
        };

        DB::table('plans')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Free',
                'slug' => 'free',
                'stripe_price_id' => null,
                'stripe_product_id' => null,
                'max_users' => 1,
                'max_items' => 50,
                'max_replies' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Growth',
                'slug' => 'growth',
                'stripe_price_id' => $envOrNull('STRIPE_GROWTH_PRICE_ID'),
                'stripe_product_id' => $envOrNull('STRIPE_GROWTH_PRODUCT_ID'),
                'max_users' => 5,
                'max_items' => 500,
                'max_replies' => 2000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Pro',
                'slug' => 'pro',
                'stripe_price_id' => $envOrNull('STRIPE_PRO_PRICE_ID'),
                'stripe_product_id' => $envOrNull('STRIPE_PRO_PRODUCT_ID'),
                'max_users' => 20,
                'max_items' => 5000,
                'max_replies' => 20000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('plan_id');
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('status', 50)->default('active');
            $table->timestamp('current_period_end')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
            $table->foreign('plan_id')->references('id')->on('plans')->restrictOnDelete();
            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type', 120);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_webhook_events');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('two_factor_enabled_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('two_factor_enabled_at');
        });
    }
};
```
