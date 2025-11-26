<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class User extends Model
{
    protected $fillable = [
        "email",
        "name",
        "pwd_hash",
        "avatar",
        "telegram_id",
        "is_premium",
        "google_calendar_token",
    ];
    protected $hidden = ["pwd_hash", "telegram_id", "email"];
    public $timestamps = false;

    // Define tabla si no existe
    public static function migrate(): void
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->id();
                $table->string("email")->unique();
                $table->string("name")->unique();
                $table->string("pwd_hash")->nullable();
                $table->string("avatar")->nullable();
                $table->string("telegram_id")->nullable();
                $table->boolean("is_premium")->default(false);
                $table->string("google_calendar_token")->nullable();
            });
        }
    }
}
