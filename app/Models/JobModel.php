<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'jobs';

    protected $allowedFields = [
        'type',
        'payload',
        'status',
        'attempts',
        'created_at',
        'updated_at'
    ];
}