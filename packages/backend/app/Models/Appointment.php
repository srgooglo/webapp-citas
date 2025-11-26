<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class Appointment extends Model
{
    protected $fillable = [
        'host_user_id',
        'guest_user_id',
        'guest_user_name',
        'guest_user_telegram_id',
        'date',
        'title',
        'description',
    ];
    /**
     * @return void
     */
    public static function migrate(): void
    {
        if (!Capsule::schema()->hasTable('appointments')) {
            Capsule::schema()->create('appointments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('host_user_id');
                $table->unsignedBigInteger('guest_user_id')->nullable();
                $table->string('guest_user_name')->nullable();
                $table->string('guest_user_telegram_id')->nullable();
                $table->dateTime('date');
                $table->string('title');
                $table->string('description');
                $table->timestamps();

                $table->foreign('host_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('guest_user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    // Relaciones
    /**
     * @return BelongsTo<User,Appointment>
     */
    public function hostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }
    /**
     * @return BelongsTo<User,Appointment>
     */
    public function guestUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guest_user_id');
    }
}
