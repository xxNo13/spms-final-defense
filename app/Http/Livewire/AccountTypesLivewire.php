<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AccountType;
use App\Models\FacultyPosition;

class AccountTypesLivewire extends Component
{
    public $account_type_selected;
    public $faculty_position_id;

    public function render()
    {
        $faculty_positions = [];

        if (isset($this->account_type_selected[0])) {
            foreach ($this->account_type_selected as $account_type_id) {
                $account_type = AccountType::find($account_type_id);

                if (str_contains(strtolower($account_type->account_type), 'faculty')) {
                    foreach (FacultyPosition::all() as $fac_position) {
                        array_push($faculty_positions, $fac_position);
                    }
                }
            }

            array_unique($faculty_positions);
        }

        return view('livewire.account-types-livewire', [
            'account_types' => AccountType::all(),
            'faculty_positions' => $faculty_positions
        ]);
    }
}
