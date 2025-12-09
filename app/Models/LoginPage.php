<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class LoginPage extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $connection = 'multi'; 
    protected $table = 'login_page';

    protected $fillable = [
        'username',
        'password',
    ];

    public $timestamps = true;

    // For dynamic DB switching
    public function setConnectionName($connection)
    {
        $this->connection = $connection;
    }
      // IMPORTANT for Auth::attempt()
    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getAuthIdentifierName()
{
    return 'username';
}

protected $rememberTokenName = false;

}