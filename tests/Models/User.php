<?php

namespace RickDBCN\FilamentEmail\Tests\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, FilamentUser
{
    use Authenticatable;
    use Authorizable;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    protected $guarded = [];

    public $timestamps = false;

    protected $table = 'users';

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
