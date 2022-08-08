<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    use HasFactory;
    public $incrementing  = true;
    protected $table      = 'tb_user';
    protected $primaryKey = 'id';
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = ['nama', 'hp', 'email', 'password', 'foto', 'role', 'wilayah', 'cabang', 'outlet'];
}
