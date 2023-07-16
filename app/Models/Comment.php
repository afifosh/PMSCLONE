<?php

namespace App\Models;

use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Comments\Models\Comment as ModelsComment;

class Comment extends ModelsComment
{
    use HasFactory;
    use HasLogs;
}
