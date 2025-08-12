<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class contact extends Model
{
    use HasFactory;

    protected $keyType = 'string'; //set the key type to UUID ---> “Hey, my primary key (id) is not a number — it’s a string.”
    public $incrementing = false; //disable auto incrementing

    public static function boot(): void{
        parent::boot(); // always call the parent version first
        // auto generate UUID when creating contact
        static::creating(function($model){
            $model->id = Str::uuid();
        });
    }
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'birth_date',
    ];
    // Fillable is To prevent mass assignment vulnerabilities.
    // Without it, someone could insert unexpected fields (like is_admin, role, etc.) into your database.
}



/**
 * 🔧 The boot() method is a special static method in Laravel's Eloquent model.
 * It is automatically called when the model class is loaded.
 * 
 * This method is commonly used to hook into Eloquent model events such as:
 * - creating
 * - updating
 * - deleting
 * 
 * public static function boot(): void
 * {
 *     // ✅ Always call the parent::boot() method first to ensure
 *     // the base model's boot logic is executed properly.
 *     parent::boot();
 * 
 *     // ⚙️ Registering a 'creating' event listener.
 *     // This event is triggered before a model is created in the database.
 *     // The callback receives the model instance ($model) that is being created.
 *     // 
 *     // Here, we assign a UUID to the model's 'id' field before it is saved.
 *     // Str::uuid() generates a Universally Unique Identifier.
 *     static::creating(function ($model) {
 *         $model->id = Str::uuid();
 *     });
 * }
 */
