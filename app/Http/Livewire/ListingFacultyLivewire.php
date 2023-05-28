<?php

namespace App\Http\Livewire;

use App\Models\Funct;
use App\Models\Output;
use App\Models\Target;
use Livewire\Component;
use App\Models\Duration;
use App\Models\SubFunct;
use App\Models\Suboutput;
use App\Models\Percentage;
use Livewire\WithPagination;
use App\Models\SubPercentage;

class ListingFacultyLivewire extends Component
{
    use WithPagination;

    public $selected = 'output';
    public $required = false;

    public $percent = [];
    public $sub_percent = [];

    public $funct_id;
    public $sub_funct;
    public $sub_funct_id;
    public $output;
    public $output_id;
    public $suboutput;
    public $subput;
    public $target;
    public $target_id;

    public $filter = '';

    public $hasMultipleRating = false;

    protected $listeners = ['percentage', 'resetIntput'];

    protected $rules = [
        'percent.core' => ['required_if:selected,percent'],
        'percent.strategic' => ['required_if:selected,percent'],
        'percent.support' => ['required_if:selected,percent'],

        'sub_funct' => ['required_if:selected,sub_funct'],
        'output' => ['required_if:selected,output'],
        'output_id' => ['nullable', 'required_if:selected,output_id'],
        'suboutput' => ['required_if:selected,suboutput'],
        'subput' => ['nullable', 'required_if:selected,target_id'],
        'target' => ['required_if:selected,target'],
    ];

    protected $messages = [
        'percent.core.required_if' => 'Core Percentage cannot be null',
        'percent.strategic.required_if' => 'Strategic Percentage cannot be null',
        'percent.support.required_if' => 'Support Percentage cannot be null',

        'sub_funct.required_if' => 'Sub Function cannot be null',
        'output.required_if' => 'Output cannot be null',
        'output_id.required_if' => 'Output cannot be null',
        'suboutput.required_if' => 'Suboutput cannot be null',
        'subput.required_if' => 'Suboutput/Output cannot be null',
        'target.required_if' => 'Target cannot be null',
    ];
    
    public function updated($property)
    {
        $this->validateOnly($property);
    }


    public function mount() {
        $this->duration = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        if ($this->duration) {
            $this->percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $this->duration->id)->first();
        }
    }

    public function render()
    {
        if ($this->duration) {
            $this->subFuncts = SubFunct::where('type', 'ipcr')->where('added_by', auth()->user()->id)->where('user_type', 'faculty')->where('duration_id', $this->duration->id)->get();
            $this->outputs = Output::where('type', 'ipcr')->where('added_by', auth()->user()->id)->where('user_type', 'faculty')->where('duration_id', $this->duration->id)->get();
        }

        return view('livewire.listing-faculty-livewire', [
            'functs' => Funct::paginate(1)
        ]);
    }

    /////////////////////////// SUBFUNCTION/OUTPUT/SUBOUTPUT/TARGET CONFIGURATION ///////////////////////////

    public function selectIpcr($type, $id, $category = null) {
        $this->selected = $type;
        switch($type) {
            case 'sub_funct':
                $this->sub_funct_id = $id;
                if ($category) {
                    $data = SubFunct::find($id);

                    $this->sub_funct = $data->sub_funct;
                }
                break; 
            case 'output':
                $this->output_id = $id;
                if ($category) {
                    $data = Output::find($id);

                    $this->output = $data->output;
                }
                break; 
            case 'suboutput':
                $this->suboutput_id = $id;
                if ($category) {
                    $data = Suboutput::find($id);

                    $this->suboutput = $data->suboutput;
                }
                break; 
            case 'target':
                $this->target_id = $id;
                if ($category) {
                    $data = Target::find($id);

                    $this->target = $data->target;
                    $this->required = $data->required;
                    $this->hasMultipleRating = $data->hasMultipleRating;
                }
                break;
        }
    }

    public function saveIpcr() {
        $this->validate();

        switch (str_replace(url('/'), '', url()->previous())) {
            case '/ipcr/listing/faculty':
                $this->funct_id = 1;
                $code = 'CF';
                break;
            case '/ipcr/listing/faculty?page=2':
                $this->funct_id = 2;
                $code = 'STF';
                break;
            case '/ipcr/listing/faculty?page=3':
                $this->funct_id = 3;
                $code = 'SF';
                break;
            default:
                $this->funct_id = 0;
                break;
        };

        switch ($this->selected) {
            case 'sub_funct':
                SubFunct::create([
                    'sub_funct' => $this->sub_funct,
                    'type' => 'ipcr',
                    'user_type' => 'faculty',
                    'funct_id' => $this->funct_id,
                    'duration_id' => $this->duration->id,
                    'added_by' => auth()->user()->id,
                    'filter' => $this->filter
                ]);
                break;
            case 'output':
                if ($this->sub_funct_id) {
                    Output::create([
                        'code' => $code,
                        'output' => $this->output,
                        'type' => 'ipcr',
                        'user_type' => 'faculty',
                        'sub_funct_id' => $this->sub_funct_id,
                        'duration_id' => $this->duration->id,
                        'added_by' => auth()->user()->id,
                        'filter' => $this->filter
                    ]);
                    break;
                }
                Output::create([
                    'code' => $code,
                    'output' => $this->output,
                    'type' => 'ipcr',
                    'user_type' => 'faculty',
                    'funct_id' => $this->funct_id,
                    'duration_id' => $this->duration->id,
                    'added_by' => auth()->user()->id,
                    'filter' => $this->filter
                ]);
                break;
            case 'suboutput':
                Suboutput::create([
                    'suboutput' => $this->suboutput,
                    'output_id' => $this->output_id,
                    'duration_id' => $this->duration->id,
                    'added_by' => auth()->user()->id
                ]);
                break;
            case 'target':                
                $subput = explode(',', $this->subput);

                if ($subput[0] == 'output') {
                    Target::create([
                        'target' => $this->target,
                        'output_id' => $subput[1],
                        'required' => $this->required,
                        'duration_id' => $this->duration->id,
                        'added_by' => auth()->user()->id,
                        'hasMultipleRating' => $this->hasMultipleRating,
                    ]);
                } elseif ($subput[0] == 'suboutput') {
                    Target::create([
                        'target' => $this->target,
                        'suboutput_id' => $subput[1],
                        'required' => $this->required,
                        'duration_id' => $this->duration->id,
                        'added_by' => auth()->user()->id,
                        'hasMultipleRating' => $this->hasMultipleRating,
                    ]);
                }
                break;
        }

        $this->dispatchBrowserEvent('toastify', [
            'message' => "Added Successfully",
            'color' => "#435ebe",
        ]);

        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    public function updateIpcr() {

        $this->validate();

        switch ($this->selected) {
            case 'sub_funct':
                SubFunct::where('id', $this->sub_funct_id)->update([
                    'sub_funct' => $this->sub_funct
                ]);
                break;
            case 'output':
                Output::where('id', $this->output_id)->update([
                    'output' => $this->output
                ]);
                break;
            case 'suboutput':
                Suboutput::where('id', $this->suboutput_id)->update([
                    'suboutput' => $this->suboutput
                ]);
                break;
            case 'target':  
                Target::where('id', $this->target_id)->update([
                    'target' => $this->target,
                    'required' => $this->required,
                    'hasMultipleRating' => $this->hasMultipleRating,
                ]);
                break;
        }

        $this->dispatchBrowserEvent('toastify', [
            'message' => "Updated Successfully",
            'color' => "#28ab55",
        ]);

        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    public function delete() {
        switch ($this->selected) {
            case 'sub_funct':
                SubFunct::where('id',$this->sub_funct_id)->delete();
                break;
            case 'output':
                Output::where('id',$this->output_id)->delete();
                break;
            case 'suboutput':
                Suboutput::where('id',$this->suboutput_id)->delete();
                break;
            case 'target':  
                Target::where('id',$this->target_id)->delete();
                break;
        }

        $this->dispatchBrowserEvent('toastify', [
            'message' => "Deleted Successfully",
            'color' => "#f3616d",
        ]);

        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    /////////////////////////// SUBFUNCTION/OUTPUT/SUBOUTPUT/TARGET CONFIGURATION END ///////////////////////////

//--------------------------------------------------------------------------------------------------------------------------------//

    /////////////////////////// PERCENTAGE CONFIGURATION ///////////////////////////

    public function percentage($category = null) {
        $this->selected = 'percent';

        if ($category) {
            $this->percent = $this->percentage;

            foreach (SubPercentage::where('type', 'ipcr')->where('user_type', 'faculty')->get() as $sub_percentage) {
                $this->sub_percent[$sub_percentage->sub_funct_id] = $sub_percentage->value;
            }
        }
    }
    
    public function checkPercentage() {

        if (array_sum([$this->percent['core'], $this->percent['strategic'], $this->percent['support']]) != 100) {
            return false;
        }

        return true;
    }

    public function savePercentage() {
        
        $this->validate();

        if (!$this->checkPercentage()) {
            return $this->dispatchBrowserEvent('toastify', [
                'message' => "Percentage is not equal to 100",
                'color' => "#f3616d",
            ]);
        }

        Percentage::create([
            'core' => $this->percent['core'],
            'strategic' => $this->percent['strategic'],
            'support' => $this->percent['support'],
            'type' => 'ipcr',
            'user_type' => 'faculty',
            'duration_id' => $this->duration->id,
        ]);

        $this->dispatchBrowserEvent('toastify', [
            'message' => "Added Successfully",
            'color' => "#435ebe",
        ]);

        $this->mount();
        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    public function updatePercentage() {
        
        $this->validate();

        if (!$this->checkPercentage()) {
            return $this->dispatchBrowserEvent('toastify', [
                'message' => "Percentage is not equal to 100",
                'color' => "#f3616d",
            ]);
        }

        Percentage::where('id', $this->percent['id'])->update([
            'core' => $this->percent['core'],
            'strategic' => $this->percent['strategic'],
            'support' => $this->percent['support'],
        ]);


        $this->dispatchBrowserEvent('toastify', [
            'message' => "Updated Successfully",
            'color' => "#28ab55",
        ]);

        $this->mount();
        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }

    /////////////////////////// PERCENTAGE CONFIGURATION END ///////////////////////////

    
    public function resetInput(){
        $this->required = false;
        $this->percent = [];
        $this->sub_percent = [];    
        $this->funct_id = '';
        $this->sub_funct = '';
        $this->sub_funct_id = '';
        $this->output = '';
        $this->output_id = '';
        $this->suboutput = '';
        $this->subput = '';
        $this->target = '';
        $this->target_id = '';
    }

    public function closeModal()
    {
        $this->resetInput();
        $this->dispatchBrowserEvent('close-modal');
    }
}
