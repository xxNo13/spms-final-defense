<?php

namespace App\Http\Livewire;

use Livewire\Component;

class MessageTtmaLivewire extends Component
{
    public $ttma;
    
    public function render()
    {
        return view('livewire.message-ttma-livewire');
    }

    public function exports($file_path, $default_name) {
        return response()->download(public_path('uploads/'.$file_path), $default_name);
    }
}
