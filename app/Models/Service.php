<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_service
 * @property string|null $deskripsi
 * @property float $harga_default
 * @property int|null $durasi_estimasi
 * @property bool $is_active
 * @property bool $is_bookable
 */
class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_service', 'deskripsi', 'harga_default', 'durasi_estimasi', 'is_active', 'is_bookable',
    ];

    protected function casts(): array
    {
        return [
            'harga_default' => 'decimal:2',
            'is_active' => 'boolean',
            'is_bookable' => 'boolean',
        ];
    }
}
