<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nama_bengkel
 * @property string $contact_person
 * @property string $no_hp
 * @property string|null $alamat
 * @property string|null $catatan
 */
class Partner extends Model
{
    protected $fillable = ['nama_bengkel', 'contact_person', 'no_hp', 'alamat', 'catatan'];

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
