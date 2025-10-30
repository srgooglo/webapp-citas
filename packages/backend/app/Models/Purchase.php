<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class Purchase extends Model
{
    protected $fillable = ['user_id', 'amount', 'status'];
    public $timestamps = true;

    // Define la tabla si no existe
    public static function migrate(): void
    {
        if (!Capsule::schema()->hasTable('purchases')) {
            Capsule::schema()->create('purchases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 8, 2);
                $table->string('status')->default('pending');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
