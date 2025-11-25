<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\UserDetails as UserDetailsModel;

#[Layout('components.layouts.app'), Title('Detail User')]

class UserDetails extends Component
    {
     public $user;
     
     public function mount($id)
     {
         $this->user = UserDetailsModel::where('user_id', $id)->firstOrFail();
        }
        
        public function render()
        {
            return view('livewire.user-details');
        }
    }

    