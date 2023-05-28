<?php

namespace App\Http\Livewire;

use App\Models\Funct;
use App\Models\Output;
use App\Models\Target;
use Livewire\Component;
use App\Models\Duration;
use App\Models\Standard;
use App\Models\Suboutput;
use App\Models\Percentage;
use App\Models\AccountType;
use App\Models\PrintInfo;
use Livewire\WithPagination;
use App\Models\SubPercentage;
use Illuminate\Support\Facades\Auth;
use App\Models\SubFunct;

class ArchiveLivewire extends Component
{
    use WithPagination;
    
    public $durations;

    public $viewed = false;

    public $duration;
    public $type;
    public $user_type;
    public $category;

    public $percentage;
    public $sub_percentages;

    public $search;

    public $print;
    public $printInfos = [];

    public $selectedTargets = [];

    public $approval;

    protected  $queryString = ['search'];

    protected $rules = [
        'printInfos.position' => ['nullable'],
        'printInfos.office' => ['nullable'],
    ];

    public function updated($property)
    {
        if ($property == 'search') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $durations = Duration::query();

        if ($this->search) {
            $search = $this->search;
            
            $results = preg_split('/\s+/', strtolower($search));

            foreach ($results as $result) {
                $durations->where(function ($query) use ($result) {
                    return $query->where('type', 'LIKE', "%{$result}%")->orwhere('duration_name', 'LIKE', "%{$result}%");
                });
            }

            if (str_contains($search, 'opcr')) {
                $durations->where('type', 'office');
            }
            if (str_contains($search, 'ipcr')) {
                $durations->where(function ($query) {
                    return $query->where('type', 'staff')->orwhere('type', 'faculty');
                });
            }
        }

        $durations->distinct();
        $this->durations = $durations->where('end_date', '<=', date('Y-m-d'))->get();

        if (isset($this->category)) {
            $functs = Funct::all();
            return view('components.archives-standard',[
                'functs' => $functs,
            ]);
        }
        if ($this->viewed) {
            $functs = Funct::all();
            return view('components.archives',[
                'functs' => $functs
            ]);
        }
        return view('livewire.archive-livewire');
    }

    public function viewed($duration_id, $type, $user_type, $category = null){
        $this->duration = Duration::find($duration_id);
        $this->type = $type;
        $this->user_type = $user_type;
        $this->category = $category;

        if ($type == 'opcr' && $user_type == 'office') {
            $this->percentage = Percentage::where('type', $type)->where('user_type', $user_type)->where('user_id', null)->where('duration_id', $duration_id)->first();
            $this->sub_percentages = SubPercentage::where('type', $type)->where('user_type', $user_type)->where('user_id', null)->where('duration_id', $duration_id)->get();
        } elseif ($type == 'ipcr' && $user_type == 'faculty') {
            $this->percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $this->duration->id)->first();
        } elseif ($type == 'ipcr' && $user_type == 'staff') {
            $this->percentage = Percentage::where('type', 'ipcr')->where('user_type', 'staff')->where('user_id', auth()->user()->id)->where('duration_id', $this->duration->id)->first();
        }

        $duration = Duration::orderBy('id', 'DESC')->where('type', $this->user_type)->where('start_date', '<=', date('Y-m-d'))->first();
        
        $this->approval = auth()->user()->approvals()->orderBy('id', 'DESC')->where('name', 'approval')->where('type', $this->type)->where('duration_id', $duration->id)->where('user_type', $this->user_type)->first();
        
        $this->viewed = true;
    }

    public function print() {
        $this->print = 'faculty';

        $this->printInfos = PrintInfo::where('user_id', auth()->user()->id)
                                ->where('duration_id', $this->duration->id)
                                ->where('type', 'faculty')
                                ->first();
        if($this->printInfos) {
            $this->printInfos->toArray();
        }
    }

    public function submitPrint() {
        $printInfo = PrintInfo::where('user_id', auth()->user()->id)
                            ->where('duration_id', $this->duration->id)
                            ->where('type', 'faculty')
                            ->first();

        if ($printInfo) {
            PrintInfo::where('id', $printInfo->id)->update([
                'position' => $this->printInfos['position'],
                'office' => $this->printInfos['office']
            ]);
        } else {
            PrintInfo::create([
                'position' => $this->printInfos['position'],
                'office' => $this->printInfos['office'],
                'type' => 'faculty',
                'duration_id' => $this->duration->id,
                'user_id' => auth()->user()->id
            ]);
        }

        $this->dispatchBrowserEvent('close-modal');
    }

    public function getIpcr() {
        $sub_funct = '';
        $sub_funct_id = 0;
        $storesub_funct_id = 0;
        $output = '';
        $output_id = 0;
        $storeOutput_id = 0;
        $suboutput = '';
        $suboutput_id = 0;
        $storeSuboutput_id = 0;
        $target = '';
        $eff_5 = null;
        $eff_4 = null;
        $eff_3 = null;
        $eff_2 = null;
        $eff_1 = null;
        $qua_5 = null;
        $qua_4 = null;
        $qua_3 = null;
        $qua_2 = null;
        $qua_1 = null;
        $time_5 = null;
        $time_4 = null;
        $time_3 = null;
        $time_2 = null;
        $time_1 = null;

        $duration = Duration::orderBy('id', 'DESC')->where('type', $this->user_type)->where('start_date', '<=', date('Y-m-d'))->first();


        foreach($this->selectedTargets as $targ){
            $target = Target::find($targ);
            $standard = $target->standards()->where('user_id', auth()->user()->id)->first();
            if (isset($standard->eff_5)) {
                $eff_5 = $standard->eff_5;
            }
            if (isset($standard->eff_4)) {
                $eff_4 = $standard->eff_4;
            }
            if (isset($standard->eff_3)) {
                $eff_3 = $standard->eff_3;
            }
            if (isset($standard->eff_2)) {
                $eff_2 = $standard->eff_2;
            }
            if (isset($standard->eff_1)) {
                $eff_1 = $standard->eff_1;
            }
            if (isset($standard->qua_5)) {
                $qua_5 = $standard->qua_5;
            }
            if (isset($standard->qua_4)) {
                $qua_4 = $standard->qua_4;
            }
            if (isset($standard->qua_3)) {
                $qua_3 = $standard->qua_3;
            }
            if (isset($standard->qua_2)) {
                $qua_2 = $standard->qua_2;
            }
            if (isset($standard->qua_1)) {
                $qua_1 = $standard->qua_1;
            }
            if (isset($standard->time_5)) {
                $time_5 = $standard->time_5;
            }
            if (isset($standard->time_4)) {
                $time_4 = $standard->time_4;
            }
            if (isset($standard->time_3)) {
                $time_3 = $standard->time_3;
            }
            if (isset($standard->time_2)) {
                $time_2 = $standard->time_2;
            }
            if (isset($standard->time_1)) {
                $time_1 = $standard->time_1;
            }
            
            if (isset($target->output_id) && $output_id == $target->output_id) {
                $targ = Target::create([
                    'target' => $target->target,
                    'type' => $target->type,
                    'user_type' => $target->user_type,
                    'output_id' => $storeOutput_id,
                    'duration_id' => $duration->id,
                ]);
                auth()->user()->targets()->attach($targ->id);
                if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                    Standard::create([
                        'eff_5' => $eff_5,
                        'eff_4' => $eff_4,
                        'eff_3' => $eff_3,
                        'eff_2' => $eff_2,
                        'eff_1' => $eff_1,
                        'qua_5' => $qua_5,
                        'qua_4' => $qua_4,
                        'qua_3' => $qua_3,
                        'qua_2' => $qua_2,
                        'qua_1' => $qua_1,
                        'time_5' => $time_5,
                        'time_4' => $time_4,
                        'time_3' => $time_3,
                        'time_2' => $time_2,
                        'time_1' => $time_1,
                        'target_id' => $targ->id,
                        'user_id' => Auth::user()->id,
                        'duration_id' => $duration->id
                    ]);
                }
            } elseif (isset($target->suboutput_id) && $suboutput_id == $target->suboutput_id) {
                $targ = Target::create([
                    'target' => $target->target,
                    'type' => $target->type,
                    'user_type' => $target->user_type,
                    'suboutput_id' => $storeSuboutput_id,
                    'duration_id' => $duration->id,
                ]);
                auth()->user()->targets()->attach($targ->id);
                if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                    Standard::create([
                        'eff_5' => $eff_5,
                        'eff_4' => $eff_4,
                        'eff_3' => $eff_3,
                        'eff_2' => $eff_2,
                        'eff_1' => $eff_1,
                        'qua_5' => $qua_5,
                        'qua_4' => $qua_4,
                        'qua_3' => $qua_3,
                        'qua_2' => $qua_2,
                        'qua_1' => $qua_1,
                        'time_5' => $time_5,
                        'time_4' => $time_4,
                        'time_3' => $time_3,
                        'time_2' => $time_2,
                        'time_1' => $time_1,
                        'target_id' => $targ->id,
                        'user_id' => Auth::user()->id,
                        'duration_id' => $duration->id
                    ]);
                }
            } else {
                if(isset($target->output_id)) {
                    $output = Output::find($target->output_id);
                    $output_id = $output->id;

                    if(isset($output->sub_funct_id) && $sub_funct_id == $output->sub_funct_id) {
                        $storeOutput = Output::create([
                            'code' => $output->code,
                            'output' => $output->output,
                            'type' => $output->type,
                            'user_type' => $output->user_type,
                            'sub_funct_id' => $storesub_funct_id,
                            'duration_id' => $duration->id
                        ]);
                        $storeOutput_id = $storeOutput->id;
                        auth()->user()->outputs()->attach($storeOutput->id);
    
                        $targ = Target::create([
                            'target' => $target->target,
                            'type' => $target->type,
                            'user_type' => $target->user_type,
                            'output_id' => $storeOutput->id,
                            'duration_id' => $duration->id,
                        ]);
                        auth()->user()->targets()->attach($targ->id);
                        if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                            Standard::create([
                                'eff_5' => $eff_5,
                                'eff_4' => $eff_4,
                                'eff_3' => $eff_3,
                                'eff_2' => $eff_2,
                                'eff_1' => $eff_1,
                                'qua_5' => $qua_5,
                                'qua_4' => $qua_4,
                                'qua_3' => $qua_3,
                                'qua_2' => $qua_2,
                                'qua_1' => $qua_1,
                                'time_5' => $time_5,
                                'time_4' => $time_4,
                                'time_3' => $time_3,
                                'time_2' => $time_2,
                                'time_1' => $time_1,
                                'target_id' => $targ->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                        }
                    } else {
                        if (isset($output->sub_funct_id)) {
                            $sub_funct = SubFunct::find($output->sub_funct_id);
                            $sub_funct_id = $sub_funct->id;

                            $storesub_funct = SubFunct::create([
                                'sub_funct' => $sub_funct->sub_funct,
                                'funct_id' => $sub_funct->funct_id,
                                'user_id' => Auth::user()->id,
                                'type' => $sub_funct->type,
                                'user_type' => $sub_funct->user_type,
                                'duration_id' => $duration->id
                            ]);
                            $storesub_funct_id = $storesub_funct->id;
                            auth()->user()->sub_functs()->attach($storesub_funct->id);

                            $sub_percent = SubPercentage::where('sub_funct_id', $sub_funct_id)->where('user_id', auth()->user()->id)->first();
                    
                            SubPercentage::create([
                                'value' => $sub_percent->value,
                                'sub_funct_id' => $storesub_funct->id, 
                                'type' => $sub_percent->type,
                                'user_type' => $sub_percent->user_type,
                                'user_id' => auth()->user()->id,
                                'duration_id' => $duration->id,
                            ]);

                            $storeOutput = Output::create([
                                'code' => $output->code,
                                'output' => $output->output,
                                'type' => $output->type,
                                'user_type' => $output->user_type,
                                'sub_funct_id' => $storesub_funct_id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            $storeOutput_id = $storeOutput->id;
                            auth()->user()->outputs()->attach($storeOutput->id);
        
                            $targ = Target::create([
                                'target' => $target->target,
                                'type' => $target->type,
                                'user_type' => $target->user_type,
                                'output_id' => $storeOutput->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            auth()->user()->targets()->attach($targ->id);
                            if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                                Standard::create([
                                    'eff_5' => $eff_5,
                                    'eff_4' => $eff_4,
                                    'eff_3' => $eff_3,
                                    'eff_2' => $eff_2,
                                    'eff_1' => $eff_1,
                                    'qua_5' => $qua_5,
                                    'qua_4' => $qua_4,
                                    'qua_3' => $qua_3,
                                    'qua_2' => $qua_2,
                                    'qua_1' => $qua_1,
                                    'time_5' => $time_5,
                                    'time_4' => $time_4,
                                    'time_3' => $time_3,
                                    'time_2' => $time_2,
                                    'time_1' => $time_1,
                                    'target_id' => $targ->id,
                                    'user_id' => Auth::user()->id,
                                    'duration_id' => $duration->id
                                ]);
                            }
                        } else {
                            $storeOutput = Output::create([
                                'code' => $output->code,
                                'output' => $output->output,
                                'type' => $output->type,
                                'user_type' => $output->user_type,
                                'funct_id' => $output->funct_id,
                                'duration_id' => $duration->id
                            ]);
                            $storeOutput_id = $storeOutput->id;
                            auth()->user()->outputs()->attach($storeOutput->id);
        
                            $targ = Target::create([
                                'target' => $target->target,
                                'type' => $target->type,
                                'user_type' => $target->user_type,
                                'output_id' => $storeOutput->id,
                                'duration_id' => $duration->id,
                            ]);
                            auth()->user()->targets()->attach($targ->id);
                            if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                                Standard::create([
                                    'eff_5' => $eff_5,
                                    'eff_4' => $eff_4,
                                    'eff_3' => $eff_3,
                                    'eff_2' => $eff_2,
                                    'eff_1' => $eff_1,
                                    'qua_5' => $qua_5,
                                    'qua_4' => $qua_4,
                                    'qua_3' => $qua_3,
                                    'qua_2' => $qua_2,
                                    'qua_1' => $qua_1,
                                    'time_5' => $time_5,
                                    'time_4' => $time_4,
                                    'time_3' => $time_3,
                                    'time_2' => $time_2,
                                    'time_1' => $time_1,
                                    'target_id' => $targ->id,
                                    'user_id' => Auth::user()->id,
                                    'duration_id' => $duration->id
                                ]);
                            }
                        }
                    }
                    
                } elseif (isset($target->suboutput_id)) {
                    $suboutput = Suboutput::find($target->suboutput_id);
                    $suboutput_id = $suboutput->id;
                    $output = Output::find($suboutput->output_id);

                    if(isset($output->sub_funct_id) && $sub_funct_id == $output->sub_funct_id) {
                        $storeOutput = Output::create([
                            'code' => $output->code,
                            'output' => $output->output,
                            'type' => $output->type,
                            'user_type' => $output->user_type,
                            'sub_funct_id' => $storesub_funct_id,
                            'user_id' => Auth::user()->id,
                            'duration_id' => $duration->id
                        ]);
                        $storeOutput_id = $storeOutput->id;
                        auth()->user()->outputs()->attach($storeOutput->id);
    
                        $storeSuboutput = Suboutput::create([
                            'suboutput' => $suboutput->suboutput,
                            'type' => $suboutput->type,
                            'user_type' => $suboutput->user_type,
                            'output_id' => $storeOutput->id,
                            'user_id' => Auth::user()->id,
                            'duration_id' => $duration->id
                        ]);
                        $storeSuboutput_id = $storeSuboutput->id;
                        auth()->user()->suboutputs()->attach($storeSuboutput->id);
    
                        $targ = Target::create([
                            'target' => $target->target,
                            'type' => $target->type,
                            'user_type' => $target->user_type,
                            'suboutput_id' => $storeSuboutput->id,
                            'user_id' => Auth::user()->id,
                            'duration_id' => $duration->id,
                        ]);
                        auth()->user()->targets()->attach($targ->id);
                        if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                            Standard::create([
                                'eff_5' => $eff_5,
                                'eff_4' => $eff_4,
                                'eff_3' => $eff_3,
                                'eff_2' => $eff_2,
                                'eff_1' => $eff_1,
                                'qua_5' => $qua_5,
                                'qua_4' => $qua_4,
                                'qua_3' => $qua_3,
                                'qua_2' => $qua_2,
                                'qua_1' => $qua_1,
                                'time_5' => $time_5,
                                'time_4' => $time_4,
                                'time_3' => $time_3,
                                'time_2' => $time_2,
                                'time_1' => $time_1,
                                'target_id' => $targ->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                        }
                    } else {
                        if (isset($output->sub_funct_id)) {
                            $sub_funct = SubFunct::find($output->sub_funct_id);
                            $sub_funct_id = $sub_funct->id;

                            $storesub_funct = SubFunct::create([
                                'sub_funct' => $sub_funct->sub_funct,
                                'funct_id' => $sub_funct->funct_id,
                                'user_id' => Auth::user()->id,
                                'type' => $sub_funct->type,
                                'user_type' => $sub_funct->user_type,
                                'duration_id' => $duration->id
                            ]);
                            $storesub_funct_id = $storesub_funct->id;
                            auth()->user()->sub_functs()->attach($storesub_funct->id);

                            $sub_percent = SubPercentage::where('sub_funct_id', $sub_funct_id)->where('user_id', auth()->user()->id)->first();
                    
                            SubPercentage::create([
                                'value' => $sub_percent->value,
                                'sub_funct_id' => $storesub_funct->id, 
                                'type' => $sub_percent->type,
                                'user_type' => $sub_percent->user_type,
                                'user_id' => auth()->user()->id,
                                'duration_id' => $duration->id,
                            ]);

                            $storeOutput = Output::create([
                                'code' => $output->code,
                                'output' => $output->output,
                                'type' => $output->type,
                                'user_type' => $output->user_type,
                                'sub_funct_id' => $storesub_funct_id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            $storeOutput_id = $storeOutput->id;
                            auth()->user()->outputs()->attach($storeOutput->id);
        
                            $storeSuboutput = Suboutput::create([
                                'suboutput' => $suboutput->suboutput,
                                'type' => $suboutput->type,
                                'user_type' => $suboutput->user_type,
                                'output_id' => $storeOutput->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            $storeSuboutput_id = $storeSuboutput->id;
                            auth()->user()->suboutputs()->attach($storeSuboutput->id);
        
                            $targ = Target::create([
                                'target' => $target->target,
                                'type' => $target->type,
                                'user_type' => $target->user_type,
                                'suboutput_id' => $storeSuboutput->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id,
                            ]);
                            auth()->user()->targets()->attach($targ->id);
                            if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                                Standard::create([
                                    'eff_5' => $eff_5,
                                    'eff_4' => $eff_4,
                                    'eff_3' => $eff_3,
                                    'eff_2' => $eff_2,
                                    'eff_1' => $eff_1,
                                    'qua_5' => $qua_5,
                                    'qua_4' => $qua_4,
                                    'qua_3' => $qua_3,
                                    'qua_2' => $qua_2,
                                    'qua_1' => $qua_1,
                                    'time_5' => $time_5,
                                    'time_4' => $time_4,
                                    'time_3' => $time_3,
                                    'time_2' => $time_2,
                                    'time_1' => $time_1,
                                    'target_id' => $targ->id,
                                    'user_id' => Auth::user()->id,
                                    'duration_id' => $duration->id
                                ]);
                            }
                        } else {
                            $storeOutput = Output::create([
                                'code' => $output->code,
                                'output' => $output->output,
                                'type' => $output->type,
                                'user_type' => $output->user_type,
                                'funct_id' => $output->funct_id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            $storeOutput_id = $storeOutput->id;
                            auth()->user()->outputs()->attach($storeOutput->id);
        
                            $storeSuboutput = Suboutput::create([
                                'suboutput' => $suboutput->suboutput,
                                'type' => $suboutput->type,
                                'user_type' => $suboutput->user_type,
                                'output_id' => $storeOutput->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id
                            ]);
                            $storeSuboutput_id = $storeSuboutput->id;
                            auth()->user()->suboutputs()->attach($storeSuboutput->id);
        
                            $targ = Target::create([
                                'target' => $target->target,
                                'type' => $target->type,
                                'user_type' => $target->user_type,
                                'suboutput_id' => $storeSuboutput->id,
                                'user_id' => Auth::user()->id,
                                'duration_id' => $duration->id,
                            ]);
                            auth()->user()->targets()->attach($targ->id);
                            if ($eff_5 || $eff_4 || $eff_3 || $eff_2 || $eff_1 || $qua_5 || $qua_4 || $qua_3 || $qua_2 || $qua_1 || $time_5 || $time_4 || $time_3 || $time_2 || $time_1) {
                                Standard::create([
                                    'eff_5' => $eff_5,
                                    'eff_4' => $eff_4,
                                    'eff_3' => $eff_3,
                                    'eff_2' => $eff_2,
                                    'eff_1' => $eff_1,
                                    'qua_5' => $qua_5,
                                    'qua_4' => $qua_4,
                                    'qua_3' => $qua_3,
                                    'qua_2' => $qua_2,
                                    'qua_1' => $qua_1,
                                    'time_5' => $time_5,
                                    'time_4' => $time_4,
                                    'time_3' => $time_3,
                                    'time_2' => $time_2,
                                    'time_1' => $time_1,
                                    'target_id' => $targ->id,
                                    'user_id' => Auth::user()->id,
                                    'duration_id' => $duration->id
                                ]);
                            }
                        }
                    }
                }
            }
        }

        $this->viewed = false;

        $this->dispatchBrowserEvent('toastify', [
            'message' => "Added Successfully",
            'color' => "#435ebe",
        ]);

        $this->dispatchBrowserEvent('close-modal');
    }

    public function closeModal(){
        $this->dispatchBrowserEvent('close-modal'); 
    }
}
