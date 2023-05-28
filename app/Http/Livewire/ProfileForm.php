<?php

namespace App\Http\Livewire;

use App\Models\Office;
use Livewire\Component;
use App\Models\AccountType;
use App\Models\FacultyPosition;
use App\Actions\Fortify\UpdateUserProfileInformation;

class ProfileForm extends Component
{
    public $state = [];
    public $isHead = [];
    public $offices;
    public $office;
    public $account_types;
    public $account_type;
    public $institute;
    public $isProgramChair = [];

    public $faculty_position_id;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->offices = Office::orderBy('office_name', 'ASC')->get();

        $this->account_types = AccountType::orderBy('account_type', 'ASC')->get();

        $this->state = auth()->user()->withoutRelations()->toArray();

        $this->office = auth()->user()->offices->pluck('id')->toArray();

        $this->account_type = auth()->user()->account_types->pluck('id')->toArray();

        $this->institute = auth()->user()->institutes->pluck('id')->first();

        if (auth()->user()->facultyPosition) {
            $this->faculty_position_id = auth()->user()->facultyPosition->id;
        }

        foreach (auth()->user()->offices as $office) {
            $this->isHead[$office->id] = $office->pivot->isHead;
        }

        foreach (auth()->user()->institutes as $institute) {
            $this->isProgramChair[$institute->id] = $institute->pivot->isProgramChair;
        }
    }

    public function render()
    {
        $institutes = [];
        $faculty_positions = [];

        if (isset($this->account_type[0])) {
            foreach ($this->account_type as $account_type_id) {
                $account_type = AccountType::find($account_type_id);

                if (str_contains(strtolower($account_type->account_type), 'faculty')) {
                    foreach (FacultyPosition::all() as $fac_position) {
                        array_push($faculty_positions, $fac_position);
                    }
                }
            }

            array_unique($faculty_positions);
        }

        if (isset($this->office[0])) {
            foreach ($this->office as $office_id) {
                $office = Office::find($office_id);

                foreach ($office->institutes as $institute) {
                    array_push($institutes, $institute);
                }
            }

            array_unique($institutes);
        }

        $i = false;
        foreach ($institutes as $institute) {
            if ($institute->id == $this->institute) {
                $i = true;
                break;
            }
        }
        if (!$i) {
            $this->institute = null;
        }

        return view('livewire.profile-form', [
            'institutes' => $institutes,
            'faculty_positions' => $faculty_positions
        ]);
    }

    public function updateProfileInformation(UpdateUserProfileInformation $updater)
    {
        $this->state['office'] = $this->office;
        $this->state['account_type'] = $this->account_type;
        $this->state['isHead'] = $this->isHead;
        $this->state['institute'] = $this->institute;
        $this->state['isProgramChair'] = $this->isProgramChair;
        $this->state['faculty_position_id'] = $this->faculty_position_id;

        $this->resetErrorBag();

        $updater->update(auth()->user(), $this->state);
        $this->resetInput();
            
        $this->dispatchBrowserEvent('toastify', [
            'message' => "Profile Information Saved",
            'color' => "#28ab55",
        ]);

        return redirect(request()->header('Referer'));
    }

    public function resetInput() {
        $this->state = [];
        $this->isHead = [];
        $this->offices = "";
        $this->office = "";
        $this->account_types = "";
        $this->account_type = "";
        $this->institute = "";
        $this->isProgramChair = '';
        $this->faculty_position_id = '';
    }
}
