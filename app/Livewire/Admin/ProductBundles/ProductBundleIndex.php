<?php

namespace App\Livewire\Admin\ProductBundles;

use Livewire\Component;

class ProductBundleIndex extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.product-bundles.index')
            ->layout('layouts.admin');
    }
}
