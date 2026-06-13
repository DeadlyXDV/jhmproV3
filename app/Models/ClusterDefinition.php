<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property string|null $description
 * @property string $color_hex
 * @property string|null $icon
 * @property string|null $action_suggestion
 * @property array|null $centroid
 */
class ClusterDefinition extends Model
{
    protected $fillable = ['label', 'description', 'color_hex', 'icon', 'action_suggestion', 'centroid'];

    protected function casts(): array
    {
        return ['centroid' => 'array'];
    }
}
