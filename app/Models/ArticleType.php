<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleType extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function article(): HasMany
    {
        return $this->hasMany(Article::class);
    }

}
