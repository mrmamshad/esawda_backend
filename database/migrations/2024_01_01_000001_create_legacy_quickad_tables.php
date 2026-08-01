<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Legacy Quickad v10.4 schema — 31 tables — recreated as a single Laravel
 * migration so a fresh Laravel install can bootstrap the same DB shape.
 *
 * Table names use the connection prefix (`ad_` by default) so the raw-PHP
 * install and the Laravel install can share one database during the
 * strangler-fig migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notification', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('sender_id')->nullable();
            $t->string('sender_name')->nullable();
            $t->integer('owner_id')->nullable();
            $t->string('owner_name')->nullable();
            $t->integer('product_id')->nullable();
            $t->string('product_title')->nullable();
            $t->string('type')->nullable();
            $t->string('message')->nullable();
            $t->boolean('recd')->default(0);
        });

        Schema::create('firebase_device_token', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->nullable();
            $t->string('device_id', 55)->nullable();
            $t->string('name')->nullable();
            $t->text('token')->nullable();
        });

        Schema::create('admins', function (Blueprint $t) {
            $t->increments('id');
            $t->string('username', 40)->nullable();
            $t->string('password_hash', 200)->nullable();
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->string('image')->default('default_user.png');
            $t->enum('permission', ['0', '1'])->default('1');
        });

        Schema::create('adsense', function (Blueprint $t) {
            $t->increments('id');
            $t->text('slug')->nullable();
            $t->text('size')->nullable();
            $t->string('provider_name')->nullable();
            $t->text('large_track_code')->nullable();
            $t->text('tablet_track_code')->nullable();
            $t->text('phone_track_code')->nullable();
            $t->enum('status', ['0', '1'])->default('0');
        });

        Schema::create('balance', function (Blueprint $t) {
            $t->increments('id');
            $t->double('current_balance', 9, 2)->nullable();
            $t->double('total_earning', 9, 2)->nullable();
            $t->double('total_withdrawal', 9, 2)->nullable();
        });

        Schema::create('catagory_main', function (Blueprint $t) {
            $t->increments('cat_id');
            $t->integer('cat_order')->nullable();
            $t->string('cat_name', 300)->nullable();
            $t->string('slug', 150)->nullable();
            $t->string('icon', 300)->default('fa-usd');
            $t->string('picture', 300)->nullable();
        });

        Schema::create('catagory_sub', function (Blueprint $t) {
            $t->increments('sub_cat_id');
            $t->integer('main_cat_id')->nullable();
            $t->string('sub_cat_name')->nullable();
            $t->string('slug', 150)->nullable();
            $t->mediumInteger('cat_order')->nullable();
            $t->enum('photo_show', ['0', '1'])->default('1');
            $t->enum('price_show', ['0', '1'])->default('1');
            $t->text('picture')->nullable();
        });

        Schema::create('category_translation', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('translation_id')->nullable();
            $t->string('lang_code', 10)->nullable();
            $t->string('category_type', 22)->nullable();
            $t->string('title')->nullable();
            $t->string('slug', 150)->nullable();
        });

        Schema::create('cities', function (Blueprint $t) {
            $t->increments('id');
            $t->string('country_code', 2)->nullable();
            $t->string('name', 200)->nullable();
            $t->string('asciiname', 200)->nullable();
            $t->float('latitude')->nullable();
            $t->float('longitude')->nullable();
            $t->char('feature_class', 1)->nullable();
            $t->string('feature_code', 10)->nullable();
            $t->string('subadmin1_code', 80)->nullable();
            $t->string('subadmin2_code', 20)->nullable();
            $t->bigInteger('population')->nullable();
            $t->string('time_zone', 100)->nullable();
            $t->tinyInteger('active')->unsigned()->default(1);
            $t->timestamps();
            $t->index('country_code');
            $t->index('name');
        });

        Schema::create('countries', function (Blueprint $t) {
            $t->increments('id');
            $t->char('code', 2)->nullable()->unique();
            $t->string('latitude', 100)->nullable();
            $t->string('longitude', 100)->nullable();
            $t->char('iso3', 3)->nullable();
            $t->integer('iso_numeric')->unsigned()->nullable();
            $t->char('fips', 2)->nullable();
            $t->string('name', 100)->nullable();
            $t->string('asciiname', 100)->nullable();
            $t->string('capital', 100)->nullable();
            $t->integer('area')->unsigned()->nullable();
            $t->integer('population')->unsigned()->nullable();
            $t->char('continent_code', 4)->nullable();
            $t->char('tld', 4)->nullable();
            $t->string('currency_code', 3)->nullable();
            $t->string('phone', 20)->nullable();
            $t->string('postal_code_format', 50)->nullable();
            $t->string('postal_code_regex', 200)->nullable();
            $t->string('languages', 50)->nullable();
            $t->string('neighbours', 50)->nullable();
            $t->string('equivalent_fips_code', 100)->nullable();
            $t->boolean('active')->default(1);
            $t->timestamps();
        });

        Schema::create('currencies', function (Blueprint $t) {
            $t->increments('id');
            $t->string('code', 3)->nullable()->unique();
            $t->string('name', 50)->nullable();
            $t->string('html_entity', 30)->nullable();
            $t->string('font_arial', 5)->nullable();
            $t->string('font_code2000', 5)->nullable();
            $t->string('unicode_decimal', 5)->nullable();
            $t->string('unicode_hex', 5)->nullable();
            $t->boolean('in_left')->default(0);
            $t->integer('decimal_places')->unsigned()->default(2);
            $t->string('decimal_separator', 10)->default('.');
            $t->string('thousand_separator', 10)->default(',');
            $t->timestamps();
        });

        Schema::create('custom_data', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('product_id')->nullable();
            $t->integer('field_id')->nullable();
            $t->string('field_type', 20)->nullable();
            $t->text('field_data')->nullable();
        });

        Schema::create('custom_fields', function (Blueprint $t) {
            $t->increments('custom_id');
            $t->integer('custom_order')->nullable()->index();
            $t->text('icon')->nullable();
            $t->longText('translation_lang')->nullable();
            $t->text('translation_name')->nullable();
            $t->string('custom_anycat')->nullable();
            $t->string('custom_catid')->nullable();
            $t->text('custom_subcatid')->nullable();
            $t->string('custom_title', 100)->nullable();
            $t->string('custom_type', 40)->nullable();
            $t->longText('custom_options')->nullable();
            $t->boolean('custom_required')->default(0);
            $t->string('custom_name', 40)->nullable();
            $t->string('custom_default', 200)->nullable();
            $t->integer('custom_min')->unsigned()->default(0);
            $t->integer('custom_max')->unsigned()->default(0);
        });

        Schema::create('custom_options', function (Blueprint $t) {
            $t->increments('option_id');
            $t->string('title')->nullable();
        });

        Schema::create('emailq', function (Blueprint $t) {
            $t->increments('q_id');
            $t->string('email')->nullable();
            $t->string('toname')->nullable();
            $t->string('subject')->nullable();
            $t->longText('body')->nullable();
        });

        Schema::create('languages', function (Blueprint $t) {
            $t->increments('id');
            $t->string('code', 10)->nullable();
            $t->string('direction', 3)->nullable();
            $t->string('name', 100)->nullable();
            $t->string('file_name', 20)->nullable();
            $t->boolean('active')->default(1);
            $t->boolean('default')->default(0);
        });

        Schema::create('logs', function (Blueprint $t) {
            $t->increments('log_id');
            $t->integer('log_date')->unsigned()->default(0);
            $t->string('log_summary', 100)->nullable();
            $t->longText('log_details')->nullable();
        });

        Schema::create('faq_entries', function (Blueprint $t) {
            $t->mediumIncrements('faq_id');
            $t->string('translation_lang', 10)->nullable();
            $t->integer('translation_of')->unsigned()->nullable();
            $t->integer('parent_id')->unsigned()->nullable();
            $t->smallInteger('faq_pid')->default(0);
            $t->mediumInteger('faq_weight')->default(0);
            $t->string('faq_title')->nullable();
            $t->mediumText('faq_content')->nullable();
            $t->boolean('active')->default(1);
            $t->index(['translation_lang', 'translation_of', 'parent_id']);
        });

        Schema::create('favads', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->nullable();
            $t->integer('product_id')->nullable();
        });

        Schema::create('login_attempts', function (Blueprint $t) {
            $t->integer('user_id')->nullable();
            $t->string('time', 30);
        });

        Schema::create('messages', function (Blueprint $t) {
            $t->increments('message_id');
            $t->string('from_id', 40)->nullable();
            $t->string('to_id', 50)->nullable();
            $t->string('from_uname', 225)->nullable();
            $t->string('to_uname')->nullable();
            $t->text('message_content')->nullable();
            $t->dateTime('message_date')->nullable();
            $t->boolean('recd')->default(0);
            $t->enum('seen', ['0', '1'])->default('0');
            $t->string('message_type')->nullable();
            $t->integer('post_id')->nullable();
        });

        Schema::create('mobile_numbers', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->nullable()->unique();
            $t->string('mobile_number', 20)->nullable();
            $t->string('verification_code', 20);
            $t->boolean('verified')->default(0);
        });

        Schema::create('notification', function (Blueprint $t) {
            $t->mediumInteger('user_id')->unsigned()->default(0);
            $t->mediumInteger('cat_id')->unsigned()->default(0)->index();
            $t->string('user_email')->nullable();
        });

        Schema::create('options', function (Blueprint $t) {
            $t->bigIncrements('option_id');
            $t->string('option_name', 191)->nullable()->unique();
            $t->longText('option_value')->nullable();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->increments('id');
            $t->string('translation_lang', 10)->nullable()->index();
            $t->integer('translation_of')->unsigned()->nullable()->index();
            $t->integer('parent_id')->unsigned()->nullable()->index();
            $t->enum('type', ['0', '1'])->default('0');
            $t->string('name', 100)->nullable();
            $t->string('slug', 100)->nullable();
            $t->string('title', 200)->nullable();
            $t->text('content')->nullable();
            $t->boolean('active')->default(1);
            $t->timestamp('created_at')->nullable();
            $t->timestamp('updated_at')->nullable();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->mediumIncrements('payment_id');
            $t->enum('payment_install', ['0', '1'])->default('0');
            $t->string('payment_title')->nullable();
            $t->string('payment_folder', 30)->nullable();
            $t->string('payment_desc')->nullable();
        });

        Schema::create('plans', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name')->default('');
            $t->text('badge')->nullable();
            $t->float('monthly_price')->nullable();
            $t->float('annual_price')->nullable();
            $t->float('lifetime_price')->nullable();
            $t->enum('recommended', ['yes', 'no'])->default('no');
            $t->text('settings');
            $t->text('taxes_ids')->nullable();
            $t->tinyInteger('status');
            $t->dateTime('date');
        });

        Schema::create('plan_options', function (Blueprint $t) {
            $t->increments('id');
            $t->string('title')->nullable();
            $t->longText('translation_lang')->nullable();
            $t->longText('translation_name')->nullable();
            $t->integer('position')->nullable();
            $t->boolean('active')->default(1);
        });

        Schema::create('product', function (Blueprint $t) {
            $t->increments('id');
            $t->enum('status', ['pending', 'active', 'rejected', 'expire'])->default('pending');
            $t->integer('user_id')->nullable();
            $t->enum('featured', ['0', '1'])->default('0');
            $t->enum('urgent', ['0', '1'])->default('0');
            $t->enum('highlight', ['0', '1'])->default('0');
            $t->string('product_name', 150)->nullable();
            $t->string('slug', 150)->nullable();
            $t->text('description')->nullable();
            $t->integer('category')->nullable();
            $t->integer('sub_category')->nullable();
            $t->integer('price')->default(0);
            $t->enum('negotiable', ['0', '1'])->default('0');
            $t->string('phone', 50)->nullable();
            $t->enum('hide_phone', ['0', '1'])->nullable();
            $t->text('location')->nullable();
            $t->char('city', 50)->nullable();
            $t->char('state', 50)->nullable();
            $t->char('country', 50)->nullable();
            $t->string('latlong')->nullable();
            $t->text('screen_shot')->nullable();
            $t->string('tag', 225)->nullable();
            $t->integer('view')->default(1);
            $t->dateTime('created_at')->nullable();
            $t->dateTime('updated_at')->nullable();
            $t->integer('expire_date')->default(0);
            $t->integer('featured_exp_date')->nullable();
            $t->integer('urgent_exp_date')->nullable();
            $t->integer('highlight_exp_date')->nullable();
            $t->enum('admin_seen', ['0', '1'])->default('0');
            $t->enum('emailed', ['0', '1'])->default('0');
            $t->enum('hide', ['0', '1'])->default('0');
        });

        Schema::create('product_resubmit', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('product_id')->nullable()->unique();
            $t->integer('user_id')->nullable();
            $t->enum('featured', ['0', '1'])->default('0');
            $t->enum('urgent', ['0', '1'])->default('0');
            $t->enum('highlight', ['0', '1'])->default('0');
            $t->string('product_name', 100)->nullable();
            $t->text('description')->nullable();
            $t->integer('category')->nullable();
            $t->integer('sub_category')->nullable();
            $t->integer('price')->nullable();
            $t->enum('negotiable', ['0', '1'])->default('0');
            $t->string('phone', 50)->nullable();
            $t->enum('hide_phone', ['0', '1'])->nullable();
            $t->text('location')->nullable();
            $t->char('city', 50)->nullable();
            $t->char('state', 50)->nullable();
            $t->char('country', 50)->nullable();
            $t->string('latlong')->nullable();
            $t->text('screen_shot')->nullable();
            $t->string('tag', 225)->nullable();
            $t->enum('status', ['pending', 'active', 'rejected', 'softreject'])->default('pending');
            $t->integer('view')->default(1);
            $t->dateTime('created_at')->nullable();
            $t->dateTime('updated_at')->nullable();
            $t->integer('featured_exp_date')->nullable();
            $t->integer('urgent_exp_date')->nullable();
            $t->integer('highlight_exp_date')->nullable();
            $t->text('comments')->nullable();
            $t->enum('admin_seen', ['0', '1'])->default('0');
        });

        Schema::create('reviews', function (Blueprint $t) {
            $t->increments('reviewID');
            $t->string('productID')->nullable();
            $t->integer('user_id')->nullable();
            $t->double('rating')->nullable();
            $t->mediumText('comments')->nullable();
            $t->date('date')->nullable();
            $t->integer('publish')->default(1);
        });

        Schema::create('subadmin1', function (Blueprint $t) {
            $t->increments('id');
            $t->string('code', 20)->nullable()->unique();
            $t->string('country_code', 2)->nullable()->index();
            $t->string('name', 200)->nullable()->index();
            $t->string('asciiname', 200)->nullable();
            $t->tinyInteger('active')->unsigned()->default(1)->index();
        });

        Schema::create('subadmin2', function (Blueprint $t) {
            $t->increments('id');
            $t->string('code', 20)->nullable()->unique();
            $t->string('country_code', 2)->nullable()->index();
            $t->string('subadmin1_code', 20)->nullable()->index();
            $t->string('name', 200)->nullable()->index();
            $t->string('asciiname', 200)->nullable();
            $t->tinyInteger('active')->unsigned()->default(1)->index();
        });

        Schema::create('taxes', function (Blueprint $t) {
            $t->increments('id');
            $t->string('internal_name', 64)->nullable();
            $t->string('name', 64)->nullable();
            $t->string('description', 256)->nullable();
            $t->decimal('value', 10, 2)->nullable();
            $t->enum('value_type', ['percentage', 'fixed'])->nullable();
            $t->enum('type', ['inclusive', 'exclusive'])->nullable();
            $t->enum('billing_type', ['personal', 'business', 'both'])->nullable();
            $t->text('countries')->nullable();
            $t->dateTime('datetime')->nullable();
        });

        Schema::create('testimonials', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name', 100)->nullable();
            $t->string('designation', 100)->nullable();
            $t->text('content');
            $t->string('image', 100)->nullable();
        });

        Schema::create('time_zones', function (Blueprint $t) {
            $t->increments('id');
            $t->string('country_code', 2)->nullable()->index();
            $t->string('time_zone_id', 40)->default('')->unique();
            $t->float('gmt')->nullable();
            $t->float('dst')->nullable();
            $t->float('raw')->nullable();
        });

        Schema::create('transaction', function (Blueprint $t) {
            $t->increments('id');
            $t->string('product_name', 225)->nullable();
            $t->integer('product_id')->nullable();
            $t->integer('seller_id')->nullable();
            $t->double('amount', 9, 2)->nullable();
            $t->double('base_amount', 9, 2)->nullable();
            $t->enum('featured', ['0', '1'])->default('0');
            $t->enum('urgent', ['0', '1'])->default('0');
            $t->enum('highlight', ['0', '1'])->default('0');
            $t->integer('transaction_time')->nullable();
            $t->enum('status', ['pending', 'success', 'failed', 'cancel'])->nullable();
            $t->string('payment_id')->nullable();
            $t->string('transaction_gatway')->nullable();
            $t->string('transaction_ip', 15)->nullable();
            $t->string('transaction_description')->nullable();
            $t->string('transaction_method', 20)->nullable();
            $t->enum('frequency', ['MONTHLY', 'YEARLY', 'LIFETIME'])->nullable();
            $t->text('billing')->nullable();
            $t->text('taxes_ids')->nullable();
        });

        Schema::create('upgrades', function (Blueprint $t) {
            $t->increments('upgrade_id');
            $t->string('sub_id', 16)->default('0');
            $t->integer('user_id')->unsigned()->default(0);
            $t->enum('pay_mode', ['one_time', 'recurring'])->default('one_time');
            $t->bigInteger('upgrade_lasttime')->unsigned()->default(0);
            $t->bigInteger('upgrade_expires')->unsigned()->default(0);
            $t->string('unique_id')->nullable();
            $t->string('invoice_id')->nullable();
            $t->string('paypal_subscription_id')->nullable();
            $t->string('paypal_profile_id')->nullable();
            $t->string('stripe_customer_id')->nullable();
            $t->string('stripe_subscription_id')->nullable();
            $t->string('authorizenet_subscription_id')->nullable();
            $t->integer('billing_day')->nullable();
            $t->integer('length')->nullable();
            $t->integer('interval')->nullable();
            $t->integer('trial_days')->nullable();
            $t->string('status')->nullable();
            $t->smallInteger('featured_duration')->nullable();
            $t->smallInteger('urgent_duration')->nullable();
            $t->smallInteger('highlight_duration')->nullable();
            $t->date('date_trial_ends')->nullable();
            $t->dateTime('date_canceled')->nullable();
            $t->timestamp('date_created')->useCurrent();
        });

        Schema::create('user', function (Blueprint $t) {
            $t->increments('id');
            $t->string('group_id', 16)->default('free');
            $t->string('username')->nullable();
            $t->enum('user_type', ['user', 'seller'])->nullable();
            $t->string('password_hash')->nullable();
            $t->string('forgot')->nullable();
            $t->string('confirm')->nullable();
            $t->string('email')->nullable();
            $t->enum('status', ['0', '1', '2'])->nullable();
            $t->integer('view')->nullable();
            $t->dateTime('created_at')->nullable();
            $t->dateTime('updated_at')->nullable();
            $t->string('name', 225)->nullable();
            $t->string('tagline')->nullable();
            $t->text('description')->nullable();
            $t->string('website')->nullable();
            $t->enum('sex', ['Male', 'Female', 'Other'])->nullable();
            $t->string('phone')->nullable();
            $t->string('postcode')->nullable();
            $t->string('address')->nullable();
            $t->string('country', 50)->nullable();
            $t->string('city', 225)->nullable();
            $t->string('image', 225)->default('default_user.png');
            $t->dateTime('lastactive')->nullable();
            $t->string('facebook')->nullable();
            $t->string('twitter')->nullable();
            $t->string('googleplus')->nullable();
            $t->string('instagram')->nullable();
            $t->string('linkedin')->nullable();
            $t->string('youtube')->nullable();
            $t->enum('oauth_provider', ['', 'facebook', 'google', 'twitter'])->nullable();
            $t->string('oauth_uid', 100)->nullable();
            $t->string('oauth_link')->nullable();
            $t->enum('online', ['0', '1'])->default('0');
            $t->enum('notify', ['0', '1'])->default('0');
            $t->string('notify_cat')->nullable();
        });

        Schema::create('user_options', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->nullable();
            $t->string('option_name', 191)->nullable();
            $t->longText('option_value')->nullable();
        });

        Schema::create('blog', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('author')->nullable();
            $t->string('title')->nullable();
            $t->text('description')->nullable();
            $t->string('image')->nullable();
            $t->text('tags')->nullable();
            $t->enum('status', ['publish', 'pending'])->default('publish');
            $t->dateTime('created_at')->nullable();
            $t->dateTime('updated_at')->nullable();
        });

        Schema::create('blog_categories', function (Blueprint $t) {
            $t->increments('id');
            $t->string('title', 50)->nullable();
            $t->string('slug', 50)->nullable();
            $t->integer('position')->default(0);
            $t->enum('active', ['0', '1'])->default('1');
        });

        Schema::create('blog_cat_relation', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('blog_id')->nullable();
            $t->integer('category_id')->nullable();
        });

        Schema::create('blog_comment', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('blog_id')->nullable();
            $t->integer('user_id')->nullable();
            $t->enum('is_admin', ['0', '1'])->default('0');
            $t->tinyText('name')->nullable();
            $t->string('email', 100)->nullable();
            $t->text('comment');
            $t->dateTime('created_at')->nullable();
            $t->enum('active', ['0', '1'])->default('1');
            $t->integer('parent')->default(0);
        });
    }

    public function down(): void
    {
        $tables = [
            'blog_comment', 'blog_cat_relation', 'blog_categories', 'blog',
            'user_options', 'user', 'upgrades', 'transaction', 'time_zones',
            'testimonials', 'taxes', 'subadmin2', 'subadmin1', 'reviews',
            'product_resubmit', 'product', 'plan_options', 'plans', 'payments',
            'pages', 'options', 'notification', 'mobile_numbers', 'messages',
            'login_attempts', 'favads', 'faq_entries', 'logs', 'languages',
            'emailq', 'custom_options', 'custom_fields', 'custom_data',
            'currencies', 'countries', 'cities', 'category_translation',
            'catagory_sub', 'catagory_main', 'balance', 'adsense', 'admins',
            'firebase_device_token', 'push_notification',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
