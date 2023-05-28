<div>
    @if (isset($selected))
        {{-- Add Output/Suboutput/Target Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddOSTModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Output/Suboutput/Target</h4>
                    </div>
                    <form wire:submit.prevent="{{ (isset($type) && $type == 'office') ? 'saveOpcr' : 'saveIpcr' }}">
                        @csrf
                        <div class="modal-body">
                            
                            @if (isset($userType) && ($userType == 'listing'))
                                <div class="mt-3 form-group">
                                    <select wire:model="filter" class="form-select">
                                        <option value="">None</option>
                                        <option value="academic">Academic</option>
                                        <option value="admin">Admin</option>
                                        <option value="research">Research</option>
                                    </select>
                                </div>

                                <hr>
                            @endif

                            <div class="mt-3 form-group d-flex justify-content-around">
                                <div class="form-check form-switch">
                                    <input wire:change="$emit('resetInput')" type="radio" class="form-check-input" id="output"
                                        value="sub_funct" name="selected" wire:model="selected">
                                    <label class="form-check-label" for="sub_funct">
                                        Sub Function
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input wire:change="$emit('resetInput')" type="radio" class="form-check-input" id="output"
                                        value="output" name="selected" wire:model="selected">
                                    <label class="form-check-label" for="output">
                                        Output
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input wire:change="$emit('resetInput')" type="radio" class="form-check-input" id="suboutput"
                                        value="suboutput" name="selected" wire:model="selected">
                                    <label class="form-check-label" for="suboutput">
                                        Suboutput
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input wire:change="$emit('resetInput')" type="radio" class="form-check-input" id="target"
                                        value="target" name="selected" wire:model="selected">
                                    <label class="form-check-label" for="target">
                                        Success Indicator
                                    </label>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="mt-3">
                                @if ($selected == 'sub_funct')
                                    <label>Sub Function: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Sub Function" class="form-control"
                                            wire:model.defer="sub_funct">
                                        @error('sub_funct')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'output')
                                    <label>Sub Function (Optional): </label>
                                    <div class="form-group">
                                        <select placeholder="Sub Function" class="form-control"
                                            wire:model.defer="sub_funct_id">
                                            <option value="" selected>Select a Sub Function</option>
                                            @if ($duration)
                                                @if (isset($userType) && ($userType == 'listing' || $userType == 'listingFaculty'))
                                                    @if (isset($subFuncts))
                                                        @foreach ($subFuncts->where('filter', 'LIKE', $filter)->where('funct_id', $currentPage) as $sub_funct)
                                                            <option value="{{ $sub_funct->id }}">{{ $sub_funct->sub_funct }}</option>
                                                        @endforeach
                                                    @endif
                                                @elseif (isset($userType) && $userType == 'faculty')
                                                    @foreach (auth()->user()->sub_functs()->where('added_by', null)->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'faculty')->where('funct_id', $currentPage)->get() as $sub_funct)
                                                        <option value="{{ $sub_funct->id }}">{{ $sub_funct->sub_funct }}</option>
                                                    @endforeach
                                                @else
                                                    @if (isset($type) && $type == 'office')
                                                        @foreach (auth()->user()->sub_functs()->where('duration_id', $duration->id)->where('type', 'opcr')->where('user_type', 'office')->where('funct_id', $currentPage)->get() as $sub_funct)
                                                            <option value="{{ $sub_funct->id }}">{{ $sub_funct->sub_funct }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach (auth()->user()->sub_functs()->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'staff')->where('funct_id', $currentPage)->get() as $sub_funct)
                                                            <option value="{{ $sub_funct->id }}">{{ $sub_funct->sub_funct }}</option>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endif
                                        </select>
                                    </div>
                                    <label>Output: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Output" class="form-control" name="output"
                                            wire:model.defer="output">
                                        @error('output')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'suboutput')
                                    <label>Output: </label>
                                    <div class="form-group">
                                        <select placeholder="Output" class="form-control" wire:model.defer="output_id"
                                            required>
                                            <option value="" selected>Select an output</option>
                                            @php
                                                $number = 1;
                                                switch($currentPage) {
                                                    case 1:
                                                        $code = "CF";
                                                        break;
                                                    case 2:
                                                        $code = "STF";
                                                        break;
                                                    case 3:
                                                        $code = "SF";
                                                        break;
                                                }
                                            @endphp
                                            @if ($duration)
                                                @if (isset($userType) && ($userType == 'listing' || $userType == 'listingFaculty'))
                                                    @if (isset($outputs))
                                                        @foreach ($outputs->where('filter', 'LIKE', $filter)->where('code', $code) as $output)
                                                            @forelse ($output->targets as $target)
                                                            @empty
                                                                <option value="{{ $output->id }}"> 
                                                                    {{ $output->output }}
                                                                </option>
                                                            @endforelse
                                                        @endforeach
                                                    @endif
                                                @elseif (isset($userType) && $userType == 'faculty')
                                                    @foreach (auth()->user()->outputs()->where('added_by', null)->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'faculty')->where('code', $code)->get() as $output)
                                                        @forelse ($output->targets as $target)
                                                        @empty
                                                            <option value="{{ $output->id }}"> 
                                                                {{ $output->output }}
                                                            </option>
                                                        @endforelse
                                                    @endforeach
                                                @else
                                                    @if (isset($type) && $type == 'office')
                                                        @foreach (auth()->user()->outputs()->where('duration_id', $duration->id)->where('type', 'opcr')->where('user_type', 'office')->where('code', $code)->get() as $output)
                                                            @forelse ($output->targets as $target)
                                                            @empty
                                                                <option value="{{ $output->id }}"> 
                                                                    {{ $output->output }}
                                                                </option>
                                                            @endforelse
                                                        @endforeach
                                                    @else  
                                                        @foreach (auth()->user()->outputs()->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'staff')->where('code', $code)->get() as $output)
                                                            @forelse ($output->targets as $target)
                                                            @empty
                                                                <option value="{{ $output->id }}"> 
                                                                    {{ $output->output }}
                                                                </option>
                                                            @endforelse
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endif
                                        </select>
                                        @error('output_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>Suboutput: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Suboutput" class="form-control"
                                            name="suboutput" wire:model.defer="suboutput">
                                        @error('suboutput')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'target')
                                    <label>Output/Suboutput: </label>
                                    <div class="form-group">
                                        <select placeholder="Output/Suboutput" class="form-control" wire:model.defer="subput"
                                            required>
                                            <option value="" selected>Select an/a Output/Suboutput</option>s
                                            @php
                                                $number = 1;
                                                switch($currentPage) {
                                                    case 1:
                                                        $code = "CF";
                                                        break;
                                                    case 2:
                                                        $code = "STF";
                                                        break;
                                                    case 3:
                                                        $code = "SF";
                                                        break;
                                                }
                                            @endphp
                                            @if ($duration)
                                                @if (isset($userType) && ($userType == 'listing' || $userType == 'listingFaculty'))
                                                    @if (isset($outputs))
                                                        @foreach ($outputs->where('filter', 'LIKE', $filter)->where('code', $code) as $output)
                                                            @forelse ($output->suboutputs as $suboutput)
                                                                <option value="suboutput, {{ $suboutput->id }}">
                                                                    {{ $suboutput->output->output }} -
                                                                    {{ $suboutput->suboutput }}
                                                                </option>
                                                            @empty
                                                                    <option value="output, {{ $output->id }}">
                                                                        {{ $output->output }}
                                                                    </option>
                                                            @endforelse
                                                        @endforeach
                                                    @endif
                                                @elseif (isset($userType) && $userType == 'faculty')
                                                    @foreach (auth()->user()->outputs()->where('added_by', null)->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'faculty')->where('code', $code)->get() as $output)
                                                        @forelse ($output->suboutputs as $suboutput)
                                                            <option value="suboutput, {{ $suboutput->id }}">
                                                                {{ $suboutput->output->output }} -
                                                                {{ $suboutput->suboutput }}
                                                            </option>
                                                        @empty
                                                                <option value="output, {{ $output->id }}">
                                                                    {{ $output->output }}
                                                                </option>
                                                        @endforelse
                                                    @endforeach
                                                @else
                                                    @if (isset($type) && $type == 'office')
                                                        @foreach (auth()->user()->outputs()->where('duration_id', $duration->id)->where('type', 'opcr')->where('user_type', 'office')->where('code', $code)->get() as $output)
                                                            @forelse ($output->suboutputs as $suboutput)
                                                                <option value="suboutput, {{ $suboutput->id }}">
                                                                    {{ $suboutput->output->output }} -
                                                                    {{ $suboutput->suboutput }}
                                                                </option>
                                                            @empty
                                                                    <option value="output, {{ $output->id }}">
                                                                        {{ $output->output }}
                                                                    </option>
                                                            @endforelse
                                                        @endforeach
                                                    @else
                                                        @foreach (auth()->user()->outputs()->where('duration_id', $duration->id)->where('type', 'ipcr')->where('user_type', 'staff')->where('code', $code)->get() as $output)
                                                            @forelse ($output->suboutputs as $suboutput)
                                                                <option value="suboutput, {{ $suboutput->id }}">
                                                                    {{ $suboutput->output->output }} -
                                                                    {{ $suboutput->suboutput }}
                                                                </option>
                                                            @empty
                                                                    <option value="output, {{ $output->id }}">
                                                                        {{ $output->output }}
                                                                    </option>
                                                            @endforelse
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endif
                                        </select>
                                        @error('subput')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>Success Indicator: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Success Indicator" class="form-control"
                                            name="target" wire:model.defer="target">
                                        @error('target')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group hstack gap-2">
                                        @if (isset($userType) && $userType == 'listingFaculty')
                                                <input type="checkbox" class="form-check-glow form-check-input form-check-primary"
                                                    name="required" wire:model.defer="required">
                                                <label>Required to all Faculty</label>
                                        @endif
                                        @if (!isset($type) || (isset($type) && $type != 'office'))
                                            <input type="checkbox" class="form-check-glow form-check-input form-check-primary"
                                                name="hasMultipleRating" wire:model.defer="hasMultipleRating">
                                            <label>Has Multple Rating</label>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Output/Suboutput/Target Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditOSTModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Output/Suboutput/Target</h4>
                    </div>
                    <form wire:submit.prevent="{{ (isset($type) && $type == 'office') ? 'updateOpcr' : 'updateIpcr' }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mt-3">
                                @if ($selected == 'sub_funct')
                                    <label>Sub Function: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Sub Function" class="form-control"
                                            wire:model.defer="sub_funct">
                                        @error('sub_funct')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'output')
                                    <label>Output: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Output" class="form-control" name="output"
                                            wire:model.defer="output">
                                        @error('output')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'suboutput')
                                    <label>Suboutput: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Suboutput" class="form-control"
                                            name="suboutput" wire:model.defer="suboutput">
                                        @error('suboutput')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @elseif ($selected == 'target')
                                    <label>Success Indicator: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Success Indicator" class="form-control"
                                            name="target" wire:model.defer="target">
                                        @error('target')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group hstack gap-2">
                                        @if (isset($userType) && $userType == 'listingFaculty')
                                                <input type="checkbox" class="form-check-glow form-check-input form-check-primary"
                                                    name="required" wire:model.defer="required">
                                                <label>Required to all Faculty</label>
                                        @endif
                                        @if (!isset($type) || (isset($type) && $type != 'office'))
                                            <input type="checkbox" class="form-check-glow form-check-input form-check-primary"
                                                name="hasMultipleRating" wire:model.defer="hasMultipleRating">
                                            <label>Has Multple Rating</label>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="DeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Delete Modal</h4>
                </div>
                <form wire:submit.prevent="delete">
                    @csrf
                    <div class="modal-body">
                        <p>You sure you want to delete?</p>
                        <p>Can't recover data once you delete it!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-danger ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Delete</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Rating Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddRatingModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Rating {{ isset($targetName) ? '- ' . $targetName : '' }}</h4>
                </div>

                <form wire:submit.prevent="saveRating('{{ 'add' }}')">
                    @csrf
                    <div class="modal-body">
                        @if (isset($targetOutput))
                            <label>Output Finished (Target Output is "{{ $targetOutput }}"): </label>
                            <div class="form-group">
                                <input type="number" placeholder="Output Finished" class="form-control"
                                    wire:model.defer="output_finished">
                                @error('output_finished')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <label>Actual Accomplishment:</label>
                        <div class="form-group">
                            <textarea cols="30" rows="10" placeholder="Actual Accomplishment" class="form-control"
                                    wire:model.defer="accomplishment" style="height: 100px;"></textarea>
                            @error('accomplishment')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Quality: </label>
                        <div class="form-group">
                            <select class="form-control" wire:model.defer="quality">
                                <option value="">Quality</option>
                                @if (isset($selectedTarget) && ($standard = $selectedTarget->standards()->first()))
                                    @if (!empty($standard->qua_1)) 
                                        <option value="1">1 - {{ $standard->qua_1 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_2)) 
                                        <option value="2">2 - {{ $standard->qua_2 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_3)) 
                                        <option value="3">3 - {{ $standard->qua_3 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_4))
                                        <option value="4">4 - {{ $standard->qua_4 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_5))
                                        <option value="5">5 - {{ $standard->qua_5 }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('quality')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Timeliness: </label>
                        <div class="form-group">
                            <select class="form-control" wire:model.defer="timeliness">
                                <option value="">Timeliness</option>
                                @if (isset($selectedTarget) && ($standard = $selectedTarget->standards()->first()))
                                    @if (!empty($standard->time_1)) 
                                        <option value="1">1 - {{ $standard->time_1 }}</option>
                                    @endif
                                    @if (!empty($standard->time_2)) 
                                        <option value="2">2 - {{ $standard->time_2 }}</option>
                                    @endif
                                    @if (!empty($standard->time_3)) 
                                        <option value="3">3 - {{ $standard->time_3 }}</option>
                                    @endif
                                    @if (!empty($standard->time_4))
                                        <option value="4">4 - {{ $standard->time_4 }}</option>
                                    @endif
                                    @if (!empty($standard->time_5))
                                        <option value="5">5 - {{ $standard->time_5 }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('timeliness')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Rating Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditRatingModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Rating {{ isset($targetName) ? '- ' . $targetName : '' }}</h4>
                </div>

                <form wire:submit.prevent="saveRating('{{ 'edit' }}')">
                    @csrf
                    <div class="modal-body">
                        @if (isset($targetOutput))
                            <label>Output Finished (Target Output is "{{ $targetOutput }}"): </label>
                            <div class="form-group">
                                <input type="number" placeholder="Output Finished" class="form-control"
                                    wire:model.defer="output_finished">
                                @error('output_finished')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                        <label>Actual Accomplishment:</label>
                        <div class="form-group">
                            <textarea cols="30" rows="10" placeholder="Actual Accomplishment" class="form-control"
                                    wire:model.defer="accomplishment" style="height: 100px;"></textarea>
                            @error('accomplishment')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Quality: </label>
                        <div class="form-group">
                            <select class="form-control" wire:model.defer="quality">
                                <option value="">Quality</option>
                                @if (isset($selectedTarget) && ($standard = $selectedTarget->standards()->first()))
                                    @if (!empty($standard->qua_1)) 
                                        <option value="1">1 - {{ $standard->qua_1 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_2)) 
                                        <option value="2">2 - {{ $standard->qua_2 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_3)) 
                                        <option value="3">3 - {{ $standard->qua_3 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_4))
                                        <option value="4">4 - {{ $standard->qua_4 }}</option>
                                    @endif
                                    @if (!empty($standard->qua_5))
                                        <option value="5">5 - {{ $standard->qua_5 }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('quality')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Timeliness: </label>
                        <div class="form-group">
                            <select class="form-control" wire:model.defer="timeliness">
                                <option value="">Timeliness</option>
                                @if (isset($selectedTarget) && ($standard = $selectedTarget->standards()->first()))
                                    @if (!empty($standard->time_1)) 
                                        <option value="1">1 - {{ $standard->time_1 }}</option>
                                    @endif
                                    @if (!empty($standard->time_2)) 
                                        <option value="2">2 - {{ $standard->time_2 }}</option>
                                    @endif
                                    @if (!empty($standard->time_3)) 
                                        <option value="3">3 - {{ $standard->time_3 }}</option>
                                    @endif
                                    @if (!empty($standard->time_4))
                                        <option value="4">4 - {{ $standard->time_4 }}</option>
                                    @endif
                                    @if (!empty($standard->time_5))
                                        <option value="5">5 - {{ $standard->time_5 }}</option>
                                    @endif
                                @endif
                            </select>
                            @error('timeliness')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Standard Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddStandardModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Standard</h4>
                </div>
                <div class="text-center">
                    <h5>Important Notice: Leave Blank if Not Rated/NR</h5>
                </div>
                <form wire:submit.prevent="save('{{ 'add' }}')">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex justify-content-around gap-2">
                            <div class="w-100 text-center">Quality: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Efficiency: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Timeliness: </div>
                        </div>
                        <hr>
                        @php
                            $effs = [];
                            $quas = [];
                            $times = [];
                            if (isset($standardValue)) {
                                $effs = preg_split('/\r\n|\r|\n/', $standardValue->efficiency);
                                $quas = preg_split('/\r\n|\r|\n/', $standardValue->quality);
                                $times = preg_split('/\r\n|\r|\n/', $standardValue->timeliness);
                            }
                            arsort($effs, SORT_NUMERIC);
                            asort($quas);
                            asort($times);
                        @endphp
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" id="qua_5" wire:loading="disabled" wire:model="qua_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_5')
                                <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                            <script>

                                $('#qua_5').on('change', function () {
                                        var data = $('#qua_5').select2("val");
                                        @this.set('qua_5', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#qua_5").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                    </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" id="eff_5" wire:loading="disabled" wire:model="eff_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_5')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eff_5').on('change', function () {
                                        var data = $('#eff_5').select2("val");
                                        @this.set('eff_5', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eff_5').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_5" id="time_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_5')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#time_5').on('change', function () {
                                        var data = $('#time_5').select2("val");
                                        @this.set('time_5', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#time_5").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="qua_4" id="qua_4" style="width:100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_4')
                                <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                            <script>

                                    $('#qua_4').on('change', function () {
                                        var data = $('#qua_4').select2("val");
                                        @this.set('qua_4', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#qua_4").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="eff_4" id="eff_4" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_4')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#eff_4').on('change', function () {
                                        var data = $('#eff_4').select2("val");
                                        @this.set('eff_4', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#eff_4").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_4" id="time_4" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_4')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#time_4').on('change', function () {
                                        var data = $('#time_4').select2("val");
                                        @this.set('time_4', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#time_4").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="qua_3" id="qua_3" style="width:100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_3')
                                <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                            <script>

                                    $('#qua_3').on('change', function () {
                                        var data = $('#qua_3').select2("val");
                                        @this.set('qua_3', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#qua_3").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="eff_3" id="eff_3" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_3')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#eff_3').on('change', function () {
                                        var data = $('#eff_3').select2("val");
                                        @this.set('eff_3', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#eff_3").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_3" id="time_3" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_3')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#time_3').on('change', function () {
                                        var data = $('#time_3').select2("val");
                                        @this.set('time_3', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#time_3").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="qua_2" id="qua_2" style="width:100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#qua_2').on('change', function () {
                                        var data = $('#qua_2').select2("val");
                                        @this.set('qua_2', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#qua_2").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="eff_2" id="eff_2" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#eff_2').on('change', function () {
                                        var data = $('#eff_2').select2("val");
                                        @this.set('eff_2', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#eff_2").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_2" id="time_2" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#time_2').on('change', function () {
                                        var data = $('#time_2').select2("val");
                                        @this.set('time_2', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#time_2").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="qua_1" id="qua_1" style="width:100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#qua_1').on('change', function () {
                                        var data = $('#qua_1').select2("val");
                                        @this.set('qua_1', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#qua_1").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="eff_1" id="eff_1" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#eff_1').on('change', function () {
                                        var data = $('#eff_1').select2("val");
                                        @this.set('eff_1', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#eff_1").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_1" id="time_1" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>

                                    $('#time_1').on('change', function () {
                                        var data = $('#time_1').select2("val");
                                        @this.set('time_1', data);
                                    });
                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#time_1").select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#AddStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Edit Standard Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditStandardModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Standard</h4>
                </div>
                <form wire:submit.prevent="save('{{ 'edit' }}')">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex justify-content-around gap-2">
                            <div class="w-100 text-center">Quality: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Efficiency: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Timeliness: </div>
                        </div>
                        <hr>
                        @php
                            $effs = [];
                            $quas = [];
                            $times = [];
                            if (isset($standardValue)) {
                                $effs = preg_split('/\r\n|\r|\n/', $standardValue->efficiency);
                                $quas = preg_split('/\r\n|\r|\n/', $standardValue->quality);
                                $times = preg_split('/\r\n|\r|\n/', $standardValue->timeliness);
                            }
                            arsort($effs, SORT_NUMERIC);
                            asort($quas);
                            asort($times);
                        @endphp
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" id="equa_5" wire:loading="disabled" wire:model="qua_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_5')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#equa_5').on('change', function () {
                                        var data = $('#equa_5').select2("val");
                                        @this.set('qua_5', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#equa_5').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" id="eeff_5" wire:loading="disabled" wire:model="eff_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_5')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eeff_5').on('change', function () {
                                        var data = $('#eeff_5').select2("val");
                                        @this.set('eff_5', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eeff_5').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">5:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_5" id="etime_5" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_5')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#etime_5').on('change', function () {
                                        var data = $('#etime_5').select2("val");
                                        @this.set('time_5', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#etime_5').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" id="equa_4" wire:loading="disabled" wire:model="qua_4" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_4')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#equa_4').on('change', function () {
                                        var data = $('#equa_4').select2("val");
                                        @this.set('qua_4', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#equa_4').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" id="eeff_4" wire:loading="disabled" wire:model="eff_4" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_4')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eeff_4').on('change', function () {
                                        var data = $('#eeff_4').select2("val");
                                        @this.set('eff_4', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eeff_4').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">4:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_4" id="etime_4" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_4')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#etime_4').on('change', function () {
                                        var data = $('#etime_4').select2("val");
                                        @this.set('time_4', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#etime_4').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" id="equa_3" wire:loading="disabled" wire:model="qua_3" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_3')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#equa_3').on('change', function () {
                                        var data = $('#equa_3').select2("val");
                                        @this.set('qua_3', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#equa_3').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" id="eeff_3" wire:loading="disabled" wire:model="eff_3" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_3')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eeff_3').on('change', function () {
                                        var data = $('#eeff_3').select2("val");
                                        @this.set('eff_3', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eeff_3').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">3:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_3" id="etime_3" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_3')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#etime_3').on('change', function () {
                                        var data = $('#etime_3').select2("val");
                                        @this.set('time_3', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#etime_3').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" id="equa_2" wire:loading="disabled" wire:model="qua_2" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#equa_2').on('change', function () {
                                        var data = $('#equa_2').select2("val");
                                        @this.set('qua_2', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#equa_2').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" id="eeff_2" wire:loading="disabled" wire:model="eff_2" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eeff_2').on('change', function () {
                                        var data = $('#eeff_2').select2("val");
                                        @this.set('eff_2', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eeff_2').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">2:</h5>
                                    <select type="text" class="form-control" wire:loading="disabled" wire:model="time_2" id="etime_2" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_2')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#etime_2').on('change', function () {
                                        var data = $('#etime_2').select2("val");
                                        @this.set('time_2', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#etime_2').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                        <div class="d-flex justify-content-around gap-2">
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control" id="equa_1" wire:loading="disabled" wire:model="qua_1" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($quas as $qua)
                                            <option value="{{ $qua }}">{{ $qua }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('qua_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#equa_1').on('change', function () {
                                        var data = $('#equa_1').select2("val");
                                        @this.set('qua_1', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#equa_1').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control" id="eeff_1" wire:loading="disabled" wire:model="eff_1" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($effs as $eff)
                                            <option value="{{ $eff }}">{{ $eff }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('eff_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eeff_1').on('change', function () {
                                        var data = $('#eeff_1').select2("val");
                                        @this.set('eff_1', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#eeff_1').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <div class="vr"></div>
                            <div class="max-w-33 w-100" wire:ignore>
                                <div class="hstack gap-4">
                                    <h5 class="my-auto">1:</h5>
                                    <select type="text" class="form-control"  wire:loading="disabled" wire:model="time_1" id="etime_1" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($times as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('time_1')
                                    <p class="text-danger text-center">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#etime_1').on('change', function () {
                                        var data = $('#etime_1').select2("val");
                                        @this.set('time_1', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $('#etime_1').select2({
                                                width: 'resolve',
                                                placeholder: "",
                                                allowClear: true,
                                                tags: true,
                                                dropdownParent: $("#EditStandardModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($users))
        {{-- Add TTMA Modal --}}
        <div wire:ignore data-bs-backdrop="static"  class="modal fade text-left" id="AddTTMAModal" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Assignment</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Subject: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Subject" class="form-control"
                                    wire:model.defer="subject">
                                @error('subject')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Action Officer: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="users_id" id="users_id" class="form-select" wire:loading="disabled" wire:model="users_id" multiple="multiple">
                                    <option></option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#users_id').on('change', function () {
                                        var data = $('#users_id').select2("val");
                                        @this.set('users_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#users_id").select2({
                                                multiple: true,
                                                placeholder: "Select an Action Officer.",
                                                dropdownParent: $("#AddTTMAModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <label>Output: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Output" class="form-control" wire:model.defer="output">
                                @error('output')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Date Deadline: </label>
                            <div class="form-group">
                                <input type="date" placeholder="Date Deadline" class="form-control" wire:model.defer="deadline" min="{{ date('Y-m-d') }}">
                                @error('deadline')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit TTMA Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditTTMAModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Assignment</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Subject: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Subject" class="form-control"
                                    wire:model.defer="subject">
                                @error('subject')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Action Officer: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="users_id" id="eusers_id" class="form-select" wire:loading="disabled" wire:model="users_id" multiple="multiple">
                                    <option></option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @push ('script')
                                <script>
                                    $('#eusers_id').on('change', function () {
                                        var data = $('#eusers_id').select2("val");
                                        @this.set('users_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#eusers_id").select2({
                                                multiple: true,
                                                placeholder: "Select an Action Officer.",
                                                dropdownParent: $("#EditTTMAModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                            <label>Output: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Output" class="form-control" wire:model.defer="output">
                                @error('output')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Date Deadline: </label>
                            <div class="form-group">
                                <input type="date" placeholder="Date Deadline" class="form-control" wire:model.defer="deadline" min="{{ date('Y-m-d') }}">
                                @error('deadline')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @if (isset($institutes))
            {{-- Add Committee Modal --}}
            <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddCommitteeModal" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel33" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel33">Add Committee</h4>
                        </div>
                        
                            <form wire:submit.prevent="save('add')">
                                <div class="modal-body">
                                    <label>Committee Name: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Committee Name" class="form-control" wire:model.defer="name">
                                        @error('name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>User: </label>
                                    <div class="form-group" wire:ignore>
                                        <select style="width: 100%;" name="user_id" id="user_id" class="form-select" wire:loading="disabled" wire:model="user_id">
                                            <option></option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>Committee Type: </label>
                                    <div class="form-group">
                                        <select class="form-select" wire:model="committee_type">
                                            <option value="">Select a Commitee Type</option>
                                            <option value="eval_committee">Evaluation Committee</option>
                                            <option value="review_committee">Review Committee</option>
                                        </select>
                                    </div>
                                    @if (isset($type) && $type == 'faculty')
                                        <label>Institute: </label>
                                        <div class="form-group">
                                            <select class="form-select" wire:model="committee_institute">
                                                <option value="">Select a Institute</option>
                                                @foreach ($institutes as $institute)
                                                    <option value="{{ $institute->id }}">{{ (mb_substr($institute->office_abbr, 0, 1) == "O") ? substr($institute->office_abbr, 1) : $institute->office_abbr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @push ('script')
                                        <script>
                                            $('#user_id').on('change', function () {
                                                var data = $('#user_id').select2("val");
                                                @this.set('user_id', data);
                                            });

                                            document.addEventListener("livewire:load", function (event) {
                                                Livewire.hook('message.processed', function (message, component){
                                                    $("#user_id").select2({
                                                        placeholder: "Select a User.",
                                                        dropdownParent: $("#AddCommitteeModal")
                                                    });
                                                });
                                            });
                                        </script>
                                    @endpush
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Close</span>
                                    </button>
                                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Save</span>
                                    </button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
            
            {{-- Edit Committee Modal --}}
            <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditCommitteeModal" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel33" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel33">Edit Committee</h4>
                        </div>
                        
                            <form wire:submit.prevent="save('edit')">
                                <div class="modal-body">
                                    <label>Committee Name: </label>
                                    <div class="form-group">
                                        <input type="text" placeholder="Committee Name" class="form-control" wire:model.defer="name">
                                        @error('name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>User: </label>
                                    <div class="form-group" wire:ignore>
                                        <select style="width: 100%;" name="euser_id" id="euser_id" class="form-select" wire:loading="disabled" wire:model="user_id">
                                            <option></option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <label>Committee Type: </label>
                                    <div class="form-group">
                                        <select class="form-select" wire:model="committee_type">
                                            <option value="">Select a Commitee Type</option>
                                            <option value="eval_committee">Evaluation Committee</option>
                                            <option value="review_committee">Review Committee</option>
                                        </select>
                                    </div>
                                    @if (isset($type) && $type == 'faculty')
                                        <label>Institute: </label>
                                        <div class="form-group">
                                            <select class="form-select" wire:model="committee_institute">
                                                <option value="">Select a Institute</option>
                                                @foreach ($institutes as $institute)
                                                    <option value="{{ $institute->id }}">{{ (mb_substr($institute->office_abbr, 0, 1) == "O") ? substr($institute->office_abbr, 1) : $institute->office_abbr }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @push ('script')
                                        <script>
                                            $('#euser_id').on('change', function () {
                                                var data = $('#euser_id').select2("val");
                                                @this.set('user_id', data);
                                            });

                                            document.addEventListener("livewire:load", function (event) {
                                                Livewire.hook('message.processed', function (message, component){
                                                    $("#euser_id").select2({
                                                        placeholder: "Select a User.",
                                                        dropdownParent: $("#EditCommitteeModal")
                                                    });
                                                });
                                            });
                                        </script>
                                    @endpush
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                        <i class="bx bx-x d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Close</span>
                                    </button>
                                    <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                        <i class="bx bx-check d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Save</span>
                                    </button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Comment Modal --}}
    <div wire:ignore.self  class="modal fade text-left" id="CommentModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Comment</h4>
                    <button type="button" class="btn btn-light rounded-circle" data-bs-dismiss="modal"><i class="bi bi-fullscreen-exit"></i></button>
                </div>
                <form wire:submit.prevent="declined">
                    @csrf
                    <div class="modal-body">
                        <label>Comment: </label>
                        <div class="form-group">
                            @if (isset($selectedTargetId))
                                <textarea placeholder="Comment" class="form-control"
                                    wire:model="comment.{{ $selectedTargetId }}" style="height: 100px;">
                                </textarea>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Done Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="DoneModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Done</h4>
                </div>
                <form wire:submit.prevent="done">
                    @csrf
                    <div class="modal-body">
                        <p>Mark Assignment as Done?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Done</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($offices))
        {{-- Add Office Modal --}}
        <div wire:ignore data-bs-backdrop="static"  class="modal fade text-left" id="AddOfficeModal" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Office</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Office Name: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Office Name" class="form-control" wire:model.defer="office_name">
                                @error('office_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office Abbreviation: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Office Abbreviation" class="form-control" wire:model.defer="office_abbr">
                                @error('abr')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office which it belongs: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="parent_id" id="parent_id" class="form-select"  wire:loading="disabled" wire:model="parent_id" >
                                    <option></option>
                                    @foreach ($offices as $office) 
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (isset($offices))
                            @push ('script')
                                <script>
                                    $('#parent_id').on('change', function () {
                                        var data = $('#parent_id').select2("val");
                                        @this.set('parent_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#parent_id").select2({
                                                placeholder: "Select an Office which it belongs.",
                                                dropdownParent: $("#AddOfficeModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        @endif
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Office Modal --}}
        <div wire:ignore data-bs-backdrop="static"  class="modal fade text-left" id="EditOfficeModal" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Office</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Office Name: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Office Name" class="form-control" wire:model.defer="office_name">
                                @error('office_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office Abbreviation: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Office Abbreviation" class="form-control" wire:model.defer="office_abbr">
                                @error('abr')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office which it belongs: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="edit_parent_id" id="edit_parent_id" class="form-select" wire:loading="disabled" wire:model="parent_id" >
                                    <option></option>
                                    @foreach ($offices as $office) 
                                        <option value="{{ $office->id }}" @if (isset($parentId) && $parentId == $office->id) selected @endif>{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (isset($offices))
                            @push ('script')
                                <script>
                                    $('#edit_parent_id').on('change', function () {
                                        var data = $('#edit_parent_id').select2("val");
                                        @this.set('parent_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#edit_parent_id").select2({
                                                placeholder: "Select an Office which it belongs.",
                                                dropdownParent: $("#EditOfficeModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        @endif
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Add Institute Modal --}}
        <div wire:ignore data-bs-backdrop="static"  class="modal fade text-left" id="AddInstituteModal" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Course</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Course Name: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Course Name" class="form-control" wire:model.defer="institute_name">
                                @error('institute_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office which it belongs: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="office_id" id="office_id" class="form-select"  wire:loading="disabled" wire:model="office_id" >
                                    <option></option>
                                    @foreach ($offices as $office) 
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (isset($offices))
                            @push ('script')
                                <script>
                                    $('#office_id').on('change', function () {
                                        var data = $('#office_id').select2("val");
                                        @this.set('office_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#office_id").select2({
                                                placeholder: "Select an Office which it belongs.",
                                                dropdownParent: $("#AddInstituteModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        @endif
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Institute Modal --}}
        <div wire:ignore data-bs-backdrop="static"  class="modal fade text-left" id="EditInstituteModal" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Course</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Course Name: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Course Name" class="form-control" wire:model.defer="institute_name">
                                @error('institute_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Office which it belongs: </label>
                            <div class="form-group" wire:ignore>
                                <select style="width: 100%;" name="edit_office_id" id="edit_office_id" class="form-select" wire:loading="disabled" wire:model="office_id" >
                                    <option></option>
                                    @foreach ($offices as $office) 
                                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (isset($offices))
                            @push ('script')
                                <script>
                                    $('#edit_office_id').on('change', function () {
                                        var data = $('#edit_office_id').select2("val");
                                        @this.set('office_id', data);
                                    });

                                    document.addEventListener("livewire:load", function (event) {
                                        Livewire.hook('message.processed', function (message, component){
                                            $("#edit_office_id").select2({
                                                placeholder: "Select an Office which it belongs.",
                                                dropdownParent: $("#EditInstituteModal")
                                            });
                                        });
                                    });
                                </script>
                            @endpush
                        @endif
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Account Type Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddAccountTypeModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Account Type</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <label>Account Type: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Account Type" class="form-control"
                                wire:model.defer="account_type">
                                @error('account_type')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Account Type Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditAccountTypeModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Account Type</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <label>Account Type: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Account Type" class="form-control"
                                wire:model.defer="account_type">
                                @error('account_type')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Duration Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddDurationModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Duration</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <h5>IMPORT NOTICE!<br />You can't add, edit or delete semester duration if already started.</h5>


                        <label>Semester Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Semester Name" class="form-control" wire:model.defer="duration_name">
                                @error('duration_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>

                        <label>Start Date: </label>
                        <div class="form-group">
                            <input type="date" placeholder="Start Date" class="form-control"
                                wire:change="startChanged" wire:model.defer="start_date" min="{{ date('Y-m-d') }}">
                                @error('start_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>

                        <label>End Date: </label>
                        <div class="form-group">
                            <input type="date" placeholder="End Date" class="form-control"
                                wire:model.defer="end_date"
                                @if (isset($startDate)) min="{{ $startDate }}"
                                @else
                                    min="{{ date('Y-m-d') }}" @endif>
                                @error('end_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Duration Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditDurationModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Duration</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <h5>IMPORT NOTICE!<br />You can't add, edit or delete semester duration if already started.</h5>


                        <label>Semester Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Semester Name" class="form-control" wire:model.defer="duration_name">
                                @error('duration_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        
                        <label>Start Date: </label>
                        <div class="form-group">
                            <input type="date" placeholder="Start Date" class="form-control"
                                wire:model.defer="start_date" min="{{ date('Y-m-d') }}">
                                @error('start_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>

                        <label>End Date: </label>
                        <div class="form-group">
                            <input type="date" placeholder="End Date" class="form-control"
                                wire:model.defer="end_date" min="{{ date('Y-m-d') }}">
                                @error('end_date')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Percentage Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddPercentageModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Percentage</h4>
                </div>
                <form wire:submit.prevent="savePercentage">
                    @csrf
                    <div class="modal-body">
                        <label>Core Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Core Function" class="form-control"
                                wire:model.defer="percent.core">
                                @error('percent.core')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 1)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input required type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <label>Strategic Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Strategic Function" class="form-control"
                                wire:model.defer="percent.strategic">
                                @error('percent.strategic')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 2)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <label>Support Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Support Function" class="form-control"
                                wire:model.defer="percent.support">
                                @error('percent.support')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 3)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Percentage Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditPercentageModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Percentage</h4>
                </div>
                <form wire:submit.prevent="updatePercentage">
                    @csrf
                    <div class="modal-body">
                        @error('sub_percent')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                        <label>Core Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Core Function" class="form-control"
                                wire:model.defer="percent.core">
                                @error('percent.core')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 1)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input required type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <label>Strategic Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Strategic Function" class="form-control"
                                wire:model.defer="percent.strategic">
                                @error('percent.strategic')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 2)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <label>Support Function %: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Support Function" class="form-control"
                                wire:model.defer="percent.support">
                                @error('percent.support')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        @if ((isset($subFuncts) && isset($userType) && ($userType != 'listing' && $userType != 'listingFaculty')) || (isset($subFuncts) && !isset($userType)))
                            <div class="d-flex gap-3" style="height: 100%;">
                                <div class="vr"></div>
                                
                                <div class="">
                                    @foreach ($subFuncts as $sub_funct)
                                        @if ($sub_funct->funct_id == 3)
                                            <label>{{ $sub_funct->sub_funct }} %: </label>
                                            <div class="form-group">
                                                <input type="text" placeholder="{{ $sub_funct->sub_funct }}" class="form-control"
                                                    wire:model.defer="sub_percent.{{ $sub_funct->id }}">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Training Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddTrainingModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Training</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <label>Training Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Training Name" class="form-control"
                            wire:model.defer="training_name">
                            @error('training_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Links: </label>
                        <div class="form-group">
                            <textarea placeholder="Links" class="form-control"
                                wire:model.defer="links">
                            </textarea>
                            @error('links')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Keywords (Seperated with ,): </label>
                        <div class="form-group">
                            <textarea placeholder="Keywords" class="form-control"
                                wire:model.defer="keywords">
                            </textarea>
                            @error('keywords')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Training Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditTrainingModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Update Training</h4>
                </div>
                <form wire:submit.prevent="update">
                    @csrf
                    <div class="modal-body">
                        <label>Training Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Training Name" class="form-control"
                            wire:model.defer="training_name">
                            @error('training_name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Links: </label>
                        <div class="form-group">
                            <textarea placeholder="Links" class="form-control"
                                wire:model.defer="links">
                            </textarea>
                            @error('links')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        <label>Keywords (Seperate with ,): </label>
                        <div class="form-group">
                            <textarea placeholder="Keywords" class="form-control"
                                wire:model.defer="keywords">
                            </textarea>
                            @error('keywords')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Score Equivalent Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditScoreEqModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Update Score Equivalent</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex justify-content-around gap-2">
                            <div class="w-100 text-center">Equivalent: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Score From: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Score To: </div>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100">
                                <div class="fs-5">Outstanding: </div>
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="out_from">
                                @error('out_from')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="out_to">
                                @error('out_to')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100">
                                <div class="fs-5">Very Satsifactory: </div>
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="verysat_from">
                                @error('verysat_from')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="verysat_to">
                                @error('verysat_to')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100">
                                <div class="fs-5">Satisfactory: </div>
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="sat_from">
                                @error('sat_from')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="sat_to">
                                @error('sat_to')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100">
                                <div class="fs-5">Unatisfactory: </div>
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="unsat_from">
                                @error('unsat_from')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="unsat_to">
                                @error('unsat_to')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100">
                                <div class="fs-5">Poor: </div>
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="poor_from">
                                @error('poor_from')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr"></div>
                            <div class="gap-4 w-100 form-group">
                                <input type="text" class="form-control" wire:model.defer="poor_to">
                                @error('poor_to')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Standard Values Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditStandardValueModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Update Standard Values</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <div class="d-flex justify-content-around gap-2">
                            <div class="w-100 text-center">Quality: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Efficiency: </div>
                            <div class="vr"></div>
                            <div class="w-100 text-center">Timeliness: </div>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-around gap-2">
                            <div class="gap-4 w-100 form-group">
                                <textarea type="text" class="form-control" style="height: 150px" wire:model.defer="quality"></textarea>
                                @error('quality')
                                <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr my-2"></div>
                            <div class="gap-4 w-100 form-group">
                                <textarea type="text" class="form-control" style="height: 150px" wire:model.defer="efficiency"></textarea>
                                @error('efficiency')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="vr my-2"></div>
                            <div class="gap-4 w-100 form-group">
                                <textarea type="text" class="form-control" style="height: 150px" wire:model.defer="timeliness"></textarea>
                                @error('timeliness')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <span class="fst-italic fw-lighter">Use Next Line for an additional choices.</span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Add Target Output Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddTargetOutputModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Target Output</h4>
                </div>
                <form wire:submit.prevent="{{ (isset($type) && $type == 'office') ? "saveOpcr" : "saveIpcr" }}">
                    @csrf
                    <div class="modal-body">
                        <label>Target Output: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Target Output" class="form-control"
                            wire:model.defer="target_output">
                            @error('target_output')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        @if (isset($type) && $type == 'office')
                            @if ($targetAllocated == null || $targetAllocated == $targetID)
                                @if ($output = auth()->user()->outputs()->where('id', $selectedOutput)->first())
                                    @foreach (auth()->user()->targets()->where('output_id', $output->id)->get() as $target)
                                        @if ($target->pivot->target_allocated == null)
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model.defer="allocatedTargetSelected.{{$target->id}}" {{ ($target->id == $targetID) ? "disabled" : "" }}>
                                                <label class="form-check-label">{{ $target->target }}</label>
                                            </div> 
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                            <label>Alloted Budget: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Alloted Budget" class="form-control"
                                wire:model.defer="alloted_budget" {{ ($targetAllocated == null || $targetAllocated == $targetID) ? "" : "disabled" }}>
                                @error('alloted_budget')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Responsible Person/Office: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Responsible Person/Office" class="form-control"
                                wire:model.defer="responsible">
                                @error('responsible')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Target Output Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditTargetOutputModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Target Output</h4>
                </div>
                <form wire:submit.prevent="{{ (isset($type) && $type == 'office') ? "updateOpcr" : "updateIpcr" }}">
                    @csrf
                    <div class="modal-body">
                        <label>Target Output: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Target Output" class="form-control"
                            wire:model.defer="target_output">
                            @error('target_output')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                        @if (isset($type) && $type == 'office')
                            @if ($targetAllocated == null || $targetAllocated == $targetID)
                                @if ($output = auth()->user()->outputs()->where('id', $selectedOutput)->first())
                                    @foreach (auth()->user()->targets()->where('output_id', $output->id)->get() as $target)
                                        @if ($target->pivot->target_allocated == null || $target->pivot->target_allocated == $targetID)
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model.defer="allocatedTargetSelected.{{$target->id}}" {{ ($target->id == $targetID) ? "disabled" : "" }}>
                                                <label class="form-check-label">{{ $target->target }}</label>
                                            </div> 
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                            <label>Alloted Budget: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Alloted Budget" class="form-control"
                                wire:model.defer="alloted_budget" {{ ($targetAllocated == null || $targetAllocated == $targetID) ? "" : "disabled" }}>
                                @error('alloted_budget')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <label>Responsible Person/Office: </label>
                            <div class="form-group">
                                <input type="text" placeholder="Responsible Person/Office" class="form-control"
                                wire:model.defer="responsible">
                                @error('responsible')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Configure Number of Units Deloading --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="FacultyCorePercentageModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Configure Number of Units Deloading</h4>
                </div>
                <form wire:submit.prevent="updateSubPercentage">
                    @csrf
                    <div class="modal-body">
                        <label>Number of Units Deloading: </label>
                        <div class="form-group">
                            <input type="number" placeholder="Deloading" class="form-control"
                            wire:model.defer="deloading">
                            @error('deloading')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Print Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="PrintModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Print Modal</h4>
                </div>
                
                    <form action="
                        @if (isset($print))
                            @switch($print)
                                @case('office')
                                    {{ route('print.opcr', ['id' => auth()->user()->id]) }}
                                    @break
                                @case('faculty')
                                    {{ route('print.ipcr.faculty', ['id' => auth()->user()->id]) }}
                                    @break
                                @case('staff')
                                    {{ route('print.ipcr.staff', ['id' => auth()->user()->id]) }}
                                    @break
                            @endswitch
                        @endif
                        "
                        method="GET" target="_blank">
                        @csrf
                        @method('GET')
                        <div class="modal-body">
                            <label>Title/Position: </label>
                            <div class="form-group">
                                <input type="text" wire:model.defer="printInfos.position" placeholder="Title/Position" class="form-control" name="title" require>
                            </div>
                            <label>Office: </label>
                            <div class="form-group">
                                <input type="text" wire:model.defer="printInfos.office" placeholder="Office" class="form-control" name="office" require>
                            </div>
                            @if (isset($durationId))
                                <input type="hidden" name="duration_id" value="{{ $durationId }}">
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:click="submitPrint" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    {{-- Print Comment Modal --}}
    <div wire:ignore.self  class="modal fade text-left" id="PrintCommentModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Comment</h4>
                    <button type="button" class="btn btn-light rounded-circle" data-bs-dismiss="modal"><i class="bi bi-fullscreen-exit"></i></button>
                </div>
                <form wire:submit.prevent="declined">
                    @csrf
                    <div class="modal-body">
                        <label>Comment: </label>
                        <div class="form-group">
                            <textarea placeholder="Comment" class="form-control"
                                wire:model.defer="printComment" style="height: 100px;">
                            </textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (isset($itteration))
        {{-- Add Files Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddFilesModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Add Documents</h4>
                    </div>
                    <form wire:submit.prevent="uploadFiles">
                        @csrf
                        <div class="modal-body">
                            <label>File: </label>
                            <div class="form-group" x-data="{ isUploading: false, progress: 0 }"
                                    x-on:livewire-upload-start="isUploading = true"
                                    x-on:livewire-upload-finish="isUploading = false"
                                    x-on:livewire-upload-error="isUploading = false"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <input  type="file" accept=".pdf,.doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" placeholder="Training Name" class="form-control"
                                wire:model.defer="files" id="files.{{ $itteration }}" multiple>
                                <div class="progress progress-sm rounded my-2" x-show="isUploading">
                                    <div class="progress-bar" role="progressbar" :style="`width: ${ progress }%`"></div>
                                </div>
                                @error('files')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <hr>
                            <div class="vstack gap-2">
                                @if (isset($targetFiles))
                                    @foreach ($targetFiles as $file)
                                        <div class="hstack gap-3">
                                            <a href="#" wire:click.prevent="deleteFile({{ $file->id }})"><i class="bi bi-x"></i></a>
                                            <div class="btn icon icon-left btn-secondary">
                                                <i class="bi bi-file-earmark-text"></i>
                                                {{ $file->file_default_name }}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Add Files Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditPrintImageModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Edit Print Images</h4>
                    </div>
                    <form wire:submit.prevent="save">
                        @csrf
                        <div class="modal-body">
                            <label>Header: </label>
                            <div class="form-group">
                                <input  type="file" accept=".jpg,.png,.jpeg" placeholder="Header" class="form-control"
                                wire:model.defer="header" id="header.{{ $itteration }}">
                                @error('header')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (isset($printImage->header_link))
                                <div class="text-center">
                                    <img src="uploads/{{ $printImage->header_link }}" style="max-height: 50px" alt="">
                                </div>
                            @endif
                            <hr>
                            <label>Footer: </label>
                            <div class="form-group">
                                <input  type="file" accept=".jpg,.png,.jpeg" placeholder="Header" class="form-control"
                                wire:model.defer="footer" id="footer.{{ $itteration }}">
                                @error('footer')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (isset($printImage->footer_link))
                                <div class="text-center">
                                    <img src="uploads/{{ $printImage->footer_link }}" style="max-height: 50px" alt="">
                                </div>
                            @endif
                            <hr>
                            <label>Form: </label>
                            <div class="form-group">
                                <input  type="file" accept=".jpg,.png,.jpeg" placeholder="Header" class="form-control"
                                wire:model.defer="form" id="form.{{ $itteration }}">
                                @error('form')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            @if (isset($printImage->form_link))
                                <div class="text-center">
                                    <img src="uploads/{{ $printImage->form_link }}" style="max-height: 50px" alt="">
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Faculty Position Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="AddFacultyPositionModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Add Faculty Rank</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <label>Rank Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Position Name" class="form-control"
                                wire:model.defer="position_name">
                                @error('position_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        <label>Target per Function: </label>
                        <div class="form-group">
                            <input type="number" placeholder="Target per Function" class="form-control"
                                wire:model.defer="target_per_function">
                                @error('target_per_function')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Faculty Position Modal --}}
    <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="EditFacultyPositionModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel33" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel33">Edit Faculty Rank</h4>
                </div>
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="modal-body">
                        <label>Rank Name: </label>
                        <div class="form-group">
                            <input type="text" placeholder="Position Name" class="form-control"
                                wire:model.defer="position_name">
                                @error('position_name')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                        <label>Target per Function: </label>
                        <div class="form-group">
                            <input type="number" placeholder="Target per Function" class="form-control"
                                wire:model.defer="target_per_function">
                                @error('target_per_function')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="closeModal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Close</span>
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
