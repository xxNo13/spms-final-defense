<div>
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Office Performance Commitment and Review</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">OPCR</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-3">
            
        {{-- Message for declining --}}
        <div wire:ignore class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11">
            @if (isset($review_user[0]) && $review_user[0]['message'])
                @foreach ($review_user as $id => $user)
                    <div id="reviewToast{{$id}}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                        <div class="toast-header">
                            <strong class="me-auto">{{ $user['name'] }} Comment/s:</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <strong class="me-auto"><?php echo nl2br($user['message']) ?></strong>
                        </div>
                    </div>
                    @push ('script')
                        <script>
                            var data = "reviewToast" + "<?php echo $id ?>";
                            new bootstrap.Toast(document.getElementById(data)).show();
                        </script>
                    @endpush
                @endforeach
            @endif
            @if ($approve_user && $approve_user['message']) 
                <div id="approveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                    <div class="toast-header">
                        <strong class="me-auto">{{ $approve_user['name'] }} Comment/s:</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <strong class="me-auto"><?php echo nl2br($approve_user['message']) ?></strong>
                    </div>
                </div>
                @push ('script')
                    <script>
                        new bootstrap.Toast(document.getElementById('approveToast')).show();
                    </script>
                @endpush
            @endif
        </div>
    

        @foreach ($functs as $funct)
            @php
                $number = 1;
            @endphp
            <div class="">
                <div class="hstack mb-3 gap-2">
                    <h4>
                        {{ $funct->funct }}
                        @if (isset($percentage))
                            @switch($funct->funct)
                                @case('Core Function')
                                    {{ $percentage->core }}%
                                    @break
                                @case('Strategic Function')
                                    {{ $percentage->strategic }}%
                                    @break
                                @case('Support Function')
                                    {{ $percentage->support }}%
                                    @break
                            @endswitch
                        @endif
                    </h4>
                    <div class="ms-auto hstack gap-3">

                        @if ($duration && $duration->end_date >= date('Y-m-d'))
                            @if (!$assess || $assess->approve_status != 1)
                                @if (!$percentage)
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#AddPercentageModal" title="Add Percentage" wire:click="percentage">
                                        Add Percentage
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                        data-bs-target="#EditPercentageModal" title="Edit Percentage" wire:click="$emit('percentage', 'edit')">
                                        Edit Percentage
                                    </button>
                                @endif
                            @endif

                            @if ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1)))   
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#AddOSTModal" title="Add Output/Suboutput/Target">
                                    Add OST
                                </button>
                                <button type="button" class="btn btn-outline-secondary" title="Add Output/Suboutput/Target" wire:click="add">
                                    Get Available OST
                                </button>
                            @endif
                            @if ($hasTargetOutput && (!$approval || (isset($approval->approve_status) && $approval->approve_status != 1)))
                                <button type="button" class="btn btn-outline-info" title="Save OPCR" wire:click="submit('approval')">
                                    Submit
                                </button>
                            @endif
                            @if ($hasRating && ($approval && (isset($approval->approve_status) && $approval->approve_status == 1)) && (!$assess || (isset($assess->approve_status) && $assess->approve_status != 1)))
                                <button type="button" class="btn btn-outline-info" title="Save OPCR" wire:click="submit('assess')">
                                    Submit
                                </button>
                            @endif
                        @endif
        
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#PrintModal" title="Print OPCR" wire:click="print">
                            <i class="bi bi-printer"></i>
                        </button>
                    </div>
                </div>
                @if ($duration)
                    @foreach (auth()->user()->sub_functs()->where('funct_id', $funct->id)->where('type', 'opcr')->where('user_type', 'office')->where('duration_id', $duration->id)->get() as $sub_funct)
                        <div>
                            <h5>
                                @if ($sub_funct->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('sub_funct', 0, {{$sub_funct->id}}, 'edit')">Edit</a>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal"  wire:click="selectOpcr('sub_funct', 0, {{$sub_funct->id}})">Delete</a>
                                    </div>
                                @endif
                                {{ $sub_funct->sub_funct }}
                                @if ($sub_percentage = auth()->user()->sub_percentages()->where('sub_funct_id', $sub_funct->id)->first())
                                    {{ $sub_percentage->value }}%
                                @endif
                            </h5>

                            @foreach (auth()->user()->outputs()->where('sub_funct_id', $sub_funct->id)->where('type', 'opcr')->where('user_type', 'office')->where('duration_id', $duration->id)->get() as $output)
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            @if ($output->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('output', 0, {{$output->id}}, 'edit')">Edit</a>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('output', 0, {{$output->id}})">Delete</a>
                                                </div>
                                            @endif
                                            {{ $output->code }} {{ $number++ }} - {{ $output->output }}
                                        </h4>
                                        <p class="text-subtitle text-muted"></p>
                                    </div>

                                    @forelse (auth()->user()->suboutputs()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $suboutput)
                                        
                                        <div class="card-body">
                                            <h6>
                                                @if ($suboutput->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('suboutput', 0, {{$suboutput->id}}, 'edit')">Edit</a>
                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('suboutput', 0, {{$suboutput->id}})">Delete</a>
                                                    </div>
                                                @endif
                                                {{ $suboutput->suboutput }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="accordion accordion-flush"
                                                id="{{ 'suboutput' }}{{ $suboutput->id }}">
                                                <div class="row">
                                                    @foreach (auth()->user()->targets()->where('suboutput_id', $suboutput->id)->where('duration_id', $duration->id)->get() as $target)
                                                        <div class="col-12 col-sm-4 d-flex">
                                                            <span class="my-auto">
                                                                @if ($target->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('target', 0, {{$target->id}}, 'edit')">Edit</a>
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('target', 0, {{$target->id}})">Delete</a>
                                                                    </div>
                                                                @endif
                                                            </span>
                                                            <div wire:ignore.self
                                                                class="accordion-button collapsed gap-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                                aria-expanded="true"
                                                                aria-controls="{{ 'target' }}{{ $target->id }}"
                                                                role="button">
                                                                @if (auth()->user()->ratings()->where('target_id', $target->id)->first())
                                                                    <span class="my-auto">
                                                                        <i class="bi bi-check2"></i>
                                                                    </span>
                                                                @endif
                                                                {{ $target->target }}
                                                            </div>  
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @foreach (auth()->user()->targets()->where('suboutput_id', $suboutput->id)->where('duration_id', $duration->id)->get() as $target)

                                                    <div wire:ignore.self
                                                        id="{{ 'target' }}{{ $target->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="flush-headingOne"
                                                        data-bs-parent="#{{ 'suboutput' }}{{ $suboutput->id }}">
                                                        <div class="accordion-body table-responsive">
                                                            <table class="table table-lg text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <td rowspan="2">Target Output</td>
                                                                        <td rowspan="2">Alloted Budget</td>
                                                                        <td rowspan="2">Responsible Person/Office</td>
                                                                        <td rowspan="2">Actual
                                                                            Accomplishment</td>
                                                                        <td colspan="4">Rating</td>
                                                                        <td rowspan="2">Remarks</td>
                                                                        <td rowspan="2">Actions</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Q</td>
                                                                        <td>E</td>
                                                                        <td>T</td>
                                                                        <td>A</td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        @if (isset($target->pivot->target_output))
                                                                            <td style="white-space: nowrap;">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                                    <div class="dropdown-menu">
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditTargetOutputModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')">Edit</a>
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}})">Delete</a>
                                                                                    </div>
                                                                                @endif
                                                                                {{ $target->pivot->target_output }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $target->pivot->alloted_budget }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $target->pivot->responsible }}
                                                                            </td>
                                                                        @else
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                <td colspan="3" class="text-center ">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        <button type="button" class="btn icon btn-primary" data-bs-toggle="modal"
                                                                                            data-bs-target="#AddTargetOutputModal" wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')" title="Add Target Output">
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            @else
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                            @endif
                                                                        @endif
                            
                                                                        @forelse ($target->ratings as $rating)
                                                                            @if ($rating->user_id == auth()->user()->id) 
                                                                                <td>{{ $rating->accomplishment }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->quality)
                                                                                    {{ $rating->quality }}
                                                                                    @else
                                                                                    NR
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->efficiency)
                                                                                        {{ $rating->efficiency }}
                                                                                    @else
                                                                                        NR
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->timeliness)
                                                                                        {{ $rating->timeliness }}
                                                                                    @else
                                                                                        NR
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-nowrap">{{ $rating->average }}
                                                                                </td>
                                                                                <td>{{ $rating->remarks }}
                                                                                </td>
                                                                                <td class="">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-success"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#EditRatingModal"
                                                                                                wire:click="editRating({{ $rating->id }})"
                                                                                                title="Edit Rating">
                                                                                                <i class="bi bi-pencil-square"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-danger"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#DeleteModal"
                                                                                                title="Delete Rating"
                                                                                                wire:click="rating({{ 0 }}, {{ $rating->id }})">
                                                                                                <i class="bi bi-trash"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-secondary"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#AddFilesModal"
                                                                                                title="Add Documents"
                                                                                                wire:click="file({{ $rating->id }})">
                                                                                                <i class="bi bi-paperclip"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        <div class="dropup-center dropup">
                                                                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <i class="bi bi-file-earmark-check"></i>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu p-2 border-1" style="width: 600px">
                                                                                                <li>
                                                                                                    <div class="row border-bottom">
                                                                                                        <div class="col-3 border-end">
                                                                                                            <div class="row">
                                                                                                                <div class="col-12 text-center text-nowrap">
                                                                                                                    <small><strong>Old Score</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>Q</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>E</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>T</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>A</strong></small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-3 border-end">
                                                                                                            <div class="row">
                                                                                                                <div class="col-12 text-center text-nowrap">
                                                                                                                    <small><strong>New Score</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>Q</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>E</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>T</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>A</strong></small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-3 text-center text-nowrap border-end">
                                                                                                            <small><strong>Updated By</strong></small>
                                                                                                        </div>
                                                                                                        <div class="col-3 text-center text-nowrap">
                                                                                                            <small><strong>Updated At</strong></small>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                    @foreach ($rating->score_logs()->where('user_id', auth()->user()->id)->get() as $score_log)
                                                                                                        <li>
                                                                                                            <div class="row">
                                                                                                                <div class="col-3 border-end">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_qua ? $score_log->old_qua : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_eff ? $score_log->old_eff : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_time ? $score_log->old_time : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_ave }}</small>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-3 border-end">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_qua ? $score_log->new_qua : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_eff ? $score_log->new_eff : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_time ? $score_log->new_time : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_ave }}</small>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap border-end">
                                                                                                                    <small>{{ $score_log->updatedBy->name }}</small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small>{{ $score_log->updated_at->diffForHumans() }}</small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                @break
                                                                            @elseif ($loop->last)
                                                                                <td colspan="6"></td>
                                                                                <td class="">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-primary"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#AddRatingModal"
                                                                                                wire:click="rating({{ $target->id }})"
                                                                                                title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                                <i class="bi bi-plus"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                            @endif
                                                                        @empty
                                                                            <td colspan="6"></td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddRatingModal"
                                                                                            wire:click="rating({{ $target->id }})"
                                                                                            title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endforelse
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                @endforeach
                                            </div>
                                        </div>
                                    @empty
                                        <div class="card-body">
                                            <div class="accordion accordion-flush"
                                                id="{{ 'output' }}{{ $output->id }}">
                                                <div class="row">
                                                    @foreach (auth()->user()->targets()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $target)
                                                        <div class="col-12 col-sm-4 d-flex">
                                                            <span class="my-auto">
                                                                @if ($target->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('target', 0, {{$target->id}}, 'edit')">Edit</a>
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('target', 0, {{$target->id}})">Delete</a>
                                                                    </div>
                                                                @endif
                                                            </span>
                                                            <div wire:ignore.self
                                                                class="accordion-button collapsed gap-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                                aria-expanded="true"
                                                                aria-controls="{{ 'target' }}{{ $target->id }}"
                                                                role="button">
                                                                @if (auth()->user()->ratings()->where('target_id', $target->id)->first())
                                                                    <span class="my-auto">
                                                                        <i class="bi bi-check2"></i>
                                                                    </span>
                                                                @endif
                                                                {{ $target->target }}
                                                            </div> 
                                                        </div> 
                                                    @endforeach
                                                </div>

                                                @foreach (auth()->user()->targets()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $target)

                                                    <div wire:ignore.self
                                                        id="{{ 'target' }}{{ $target->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="flush-headingOne"
                                                        data-bs-parent="#{{ 'output' }}{{ $output->id }}">
                                                        <div class="accordion-body table-responsive">
                                                            <table class="table table-lg text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <td rowspan="2">Target Output</td>
                                                                        <td rowspan="2">Alloted Budget</td>
                                                                        <td rowspan="2">Responsible Person/Office</td>
                                                                        <td rowspan="2">Actual
                                                                            Accomplishment</td>
                                                                        <td colspan="4">Rating</td>
                                                                        <td rowspan="2">Remarks</td>
                                                                        <td rowspan="2">Actions</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Q</td>
                                                                        <td>E</td>
                                                                        <td>T</td>
                                                                        <td>A</td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        @if (isset($target->pivot->target_output))
                                                                            <td style="white-space: nowrap;">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                    <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                                    <div class="dropdown-menu">
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditTargetOutputModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')">Edit</a>
                                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}})">Delete</a>
                                                                                    </div>
                                                                                @endif
                                                                                {{ $target->pivot->target_output }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $target->pivot->alloted_budget }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $target->pivot->responsible }}
                                                                            </td>
                                                                        @else
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                <td colspan="3" class="text-center ">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        <button type="button" class="btn icon btn-primary" data-bs-toggle="modal"
                                                                                            data-bs-target="#AddTargetOutputModal" wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')" title="Add Target Output">
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            @else
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                            @endif
                                                                        @endif
                            
                                                                        @forelse ($target->ratings as $rating)
                                                                            @if ($rating->user_id == auth()->user()->id) 
                                                                                <td>{{ $rating->accomplishment }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->quality)
                                                                                    {{ $rating->quality }}
                                                                                    @else
                                                                                    NR
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->efficiency)
                                                                                        {{ $rating->efficiency }}
                                                                                    @else
                                                                                        NR
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if ($rating->timeliness)
                                                                                        {{ $rating->timeliness }}
                                                                                    @else
                                                                                        NR
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-nowrap">{{ $rating->average }}
                                                                                </td>
                                                                                <td>{{ $rating->remarks }}
                                                                                </td>
                                                                                <td class="">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-success"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#EditRatingModal"
                                                                                                wire:click="editRating({{ $rating->id }})"
                                                                                                title="Edit Rating">
                                                                                                <i class="bi bi-pencil-square"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-danger"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#DeleteModal"
                                                                                                title="Delete Rating"
                                                                                                wire:click="rating({{ 0 }}, {{ $rating->id }})">
                                                                                                <i class="bi bi-trash"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-secondary"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#AddFilesModal"
                                                                                                title="Add Documents"
                                                                                                wire:click="file({{ $rating->id }})">
                                                                                                <i class="bi bi-paperclip"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                        <div class="dropup-center dropup">
                                                                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                <i class="bi bi-file-earmark-check"></i>
                                                                                            </button>
                                                                                            <ul class="dropdown-menu p-2 border-1" style="width: 600px">
                                                                                                <li>
                                                                                                    <div class="row border-bottom">
                                                                                                        <div class="col-3 border-end">
                                                                                                            <div class="row">
                                                                                                                <div class="col-12 text-center text-nowrap">
                                                                                                                    <small><strong>Old Score</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>Q</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>E</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>T</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>A</strong></small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-3 border-end">
                                                                                                            <div class="row">
                                                                                                                <div class="col-12 text-center text-nowrap">
                                                                                                                    <small><strong>New Score</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>Q</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>E</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>T</strong></small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small><strong>A</strong></small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-3 text-center text-nowrap border-end">
                                                                                                            <small><strong>Updated By</strong></small>
                                                                                                        </div>
                                                                                                        <div class="col-3 text-center text-nowrap">
                                                                                                            <small><strong>Updated At</strong></small>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </li>
                                                                                                    @foreach ($rating->score_logs()->where('user_id', auth()->user()->id)->get() as $score_log)
                                                                                                        <li>
                                                                                                            <div class="row">
                                                                                                                <div class="col-3 border-end">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_qua ? $score_log->old_qua : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_eff ? $score_log->old_eff : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_time ? $score_log->old_time : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-danger' }}">
                                                                                                                            <small>{{ $score_log->old_ave }}</small>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-3 border-end">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_qua ? $score_log->new_qua : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_eff ? $score_log->new_eff : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_time ? $score_log->new_time : 'NR' }}</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-success' }}">
                                                                                                                            <small>{{ $score_log->new_ave }}</small>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap border-end">
                                                                                                                    <small>{{ $score_log->updatedBy->name }}</small>
                                                                                                                </div>
                                                                                                                <div class="col-3 text-center text-nowrap">
                                                                                                                    <small>{{ $score_log->updated_at->diffForHumans() }}</small>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                @break
                                                                            @elseif ($loop->last)
                                                                                <td colspan="6"></td>
                                                                                <td class="">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-primary"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#AddRatingModal"
                                                                                                wire:click="rating({{ $target->id }})"
                                                                                                title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                                <i class="bi bi-plus"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                            @endif
                                                                        @empty
                                                                            <td colspan="6"></td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddRatingModal"
                                                                                            wire:click="rating({{ $target->id }})"
                                                                                            title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endforelse
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                        <hr>
                    @endforeach
                    <div>
                        @foreach (auth()->user()->outputs()->where('funct_id', $funct->id)->where('type', 'opcr')->where('user_type', 'office')->where('duration_id', $duration->id)->get() as $output)
                            
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        @if ($output->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                            <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('output', 0, {{$output->id}}, 'edit')">Edit</a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('output', 0, {{$output->id}})">Delete</a>
                                            </div>
                                        @endif
                                        {{ $output->code }} {{ $number++ }} - {{ $output->output }}
                                    </h4>
                                    <p class="text-subtitle text-muted"></p>
                                </div>

                                @forelse (auth()->user()->suboutputs()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $suboutput)
                                    
                                    <div class="card-body">
                                        <h6>
                                            @if ($suboutput->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('suboutput', 0, {{$suboutput->id}}, 'edit')">Edit</a>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('suboutput', 0, {{$suboutput->id}})">Delete</a>
                                                </div>
                                            @endif
                                            {{ $suboutput->suboutput }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="accordion accordion-flush"
                                            id="{{ 'suboutput' }}{{ $suboutput->id }}">
                                            <div class="row">
                                                @foreach (auth()->user()->targets()->where('suboutput_id', $suboutput->id)->where('duration_id', $duration->id)->get() as $target)
                                                    <div class="col-12 col-sm-4 d-flex">
                                                        <span class="my-auto">
                                                            @if ($target->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('target', 0, {{$target->id}}, 'edit')">Edit</a>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('target', 0, {{$target->id}})">Delete</a>
                                                                </div>
                                                            @endif
                                                        </span>
                                                        <div wire:ignore.self
                                                            class="accordion-button collapsed gap-2"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                            aria-expanded="true"
                                                            aria-controls="{{ 'target' }}{{ $target->id }}"
                                                            role="button">
                                                            @if (auth()->user()->ratings()->where('target_id', $target->id)->first())
                                                                <span class="my-auto">
                                                                    <i class="bi bi-check2"></i>
                                                                </span>
                                                            @endif
                                                            {{ $target->target }}
                                                        </div> 
                                                    </div> 
                                                @endforeach
                                            </div>

                                            @foreach (auth()->user()->targets()->where('suboutput_id', $suboutput->id)->where('duration_id', $duration->id)->get() as $target)

                                                <div wire:ignore.self
                                                    id="{{ 'target' }}{{ $target->id }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#{{ 'suboutput' }}{{ $suboutput->id }}">
                                                    <div class="accordion-body table-responsive">
                                                        <table class="table table-lg text-center">
                                                            <thead>
                                                                <tr>
                                                                    <td rowspan="2">Target Output</td>
                                                                    <td rowspan="2">Alloted Budget</td>
                                                                    <td rowspan="2">Responsible Person/Office</td>
                                                                    <td rowspan="2">Actual
                                                                        Accomplishment</td>
                                                                    <td colspan="4">Rating</td>
                                                                    <td rowspan="2">Remarks</td>
                                                                    <td rowspan="2">Actions</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Q</td>
                                                                    <td>E</td>
                                                                    <td>T</td>
                                                                    <td>A</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    @if (isset($target->pivot->target_output))
                                                                        <td style="white-space: nowrap;">
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                                <div class="dropdown-menu">
                                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditTargetOutputModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')">Edit</a>
                                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}})">Delete</a>
                                                                                </div>
                                                                            @endif
                                                                            {{ $target->pivot->target_output }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $target->pivot->alloted_budget }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $target->pivot->responsible }}
                                                                        </td>
                                                                    @else
                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                            <td colspan="3" class="text-center ">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    <button type="button" class="btn icon btn-primary" data-bs-toggle="modal"
                                                                                        data-bs-target="#AddTargetOutputModal" wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')" title="Add Target Output">
                                                                                        <i class="bi bi-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                        @endif
                                                                    @endif
                        
                                                                    @forelse ($target->ratings as $rating)
                                                                        @if ($rating->user_id == auth()->user()->id) 
                                                                            <td>{{ $rating->accomplishment }}
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->quality)
                                                                                {{ $rating->quality }}
                                                                                @else
                                                                                NR
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->efficiency)
                                                                                    {{ $rating->efficiency }}
                                                                                @else
                                                                                    NR
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->timeliness)
                                                                                    {{ $rating->timeliness }}
                                                                                @else
                                                                                    NR
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-nowrap">{{ $rating->average }}
                                                                            </td>
                                                                            <td>{{ $rating->remarks }}
                                                                            </td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-success"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#EditRatingModal"
                                                                                            wire:click="editRating({{ $rating->id }})"
                                                                                            title="Edit Rating">
                                                                                            <i class="bi bi-pencil-square"></i>
                                                                                        </button>
                                                                                        <button type="button"
                                                                                            class="btn icon btn-danger"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#DeleteModal"
                                                                                            title="Delete Rating"
                                                                                            wire:click="rating({{ 0 }}, {{ $rating->id }})">
                                                                                            <i class="bi bi-trash"></i>
                                                                                        </button>
                                                                                        <button type="button"
                                                                                            class="btn icon btn-secondary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddFilesModal"
                                                                                            title="Add Documents"
                                                                                            wire:click="file({{ $rating->id }})">
                                                                                            <i class="bi bi-paperclip"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                    <div class="dropup-center dropup">
                                                                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                            <i class="bi bi-file-earmark-check"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu p-2 border-1" style="width: 600px">
                                                                                            <li>
                                                                                                <div class="row border-bottom">
                                                                                                    <div class="col-3 border-end">
                                                                                                        <div class="row">
                                                                                                            <div class="col-12 text-center text-nowrap">
                                                                                                                <small><strong>Old Score</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>Q</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>E</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>T</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>A</strong></small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-3 border-end">
                                                                                                        <div class="row">
                                                                                                            <div class="col-12 text-center text-nowrap">
                                                                                                                <small><strong>New Score</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>Q</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>E</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>T</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>A</strong></small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-3 text-center text-nowrap border-end">
                                                                                                        <small><strong>Updated By</strong></small>
                                                                                                    </div>
                                                                                                    <div class="col-3 text-center text-nowrap">
                                                                                                        <small><strong>Updated At</strong></small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </li>
                                                                                                @foreach ($rating->score_logs()->where('user_id', auth()->user()->id)->get() as $score_log)
                                                                                                    <li>
                                                                                                        <div class="row">
                                                                                                            <div class="col-3 border-end">
                                                                                                                <div class="row">
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_qua ? $score_log->old_qua : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_eff ? $score_log->old_eff : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_time ? $score_log->old_time : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_ave }}</small>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="col-3 border-end">
                                                                                                                <div class="row">
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_qua ? $score_log->new_qua : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_eff ? $score_log->new_eff : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_time ? $score_log->new_time : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_ave }}</small>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap border-end">
                                                                                                                <small>{{ $score_log->updatedBy->name }}</small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small>{{ $score_log->updated_at->diffForHumans() }}</small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            @break
                                                                        @elseif ($loop->last)
                                                                            <td colspan="6"></td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddRatingModal"
                                                                                            wire:click="rating({{ $target->id }})"
                                                                                            title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    @empty
                                                                        <td colspan="6"></td>
                                                                        <td class="">
                                                                            <div class="hstack align-items-center justify-content-center gap-2">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#AddRatingModal"
                                                                                        wire:click="rating({{ $target->id }})"
                                                                                        title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                        <i class="bi bi-plus"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    @endforelse
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="card-body">
                                        <div class="accordion accordion-flush"
                                            id="{{ 'output' }}{{ $output->id }}">
                                            <div class="row">
                                                @foreach (auth()->user()->targets()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $target)
                                                    <div class="col-12 col-sm-4 d-flex">
                                                        <span class="my-auto">
                                                            @if ($target->added_by == null && ($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditOSTModal" wire:click="selectOpcr('target', 0, {{$target->id}}, 'edit')">Edit</a>
                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal" wire:click="selectOpcr('target', 0, {{$target->id}})">Delete</a>
                                                                </div>
                                                            @endif
                                                        </span>
                                                        <div wire:ignore.self
                                                            class="accordion-button collapsed gap-2"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                            aria-expanded="true"
                                                            aria-controls="{{ 'target' }}{{ $target->id }}"
                                                            role="button">
                                                            @if (auth()->user()->ratings()->where('target_id', $target->id)->first())
                                                                <span class="my-auto">
                                                                    <i class="bi bi-check2"></i>
                                                                </span>
                                                            @endif
                                                            {{ $target->target }}
                                                        </div>
                                                    </div>  
                                                @endforeach
                                            </div>

                                            @foreach (auth()->user()->targets()->where('output_id', $output->id)->where('duration_id', $duration->id)->get() as $target)

                                                <div wire:ignore.self
                                                    id="{{ 'target' }}{{ $target->id }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-headingOne"
                                                    data-bs-parent="#{{ 'output' }}{{ $output->id }}">
                                                    <div class="accordion-body table-responsive">
                                                        <table class="table table-lg text-center">
                                                            <thead>
                                                                <tr>
                                                                    <td rowspan="2">Target Output</td>
                                                                    <td rowspan="2">Alloted Budget</td>
                                                                    <td rowspan="2">Responsible Person/Office</td>
                                                                    <td rowspan="2">Actual
                                                                        Accomplishment</td>
                                                                    <td colspan="4">Rating</td>
                                                                    <td rowspan="2">Remarks</td>
                                                                    <td rowspan="2">Actions</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Q</td>
                                                                    <td>E</td>
                                                                    <td>T</td>
                                                                    <td>A</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    @if (isset($target->pivot->target_output))
                                                                        <td style="white-space: nowrap;">
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                                <i class="bi bi-three-dots-vertical" data-bs-toggle="dropdown" style="cursor: pointer;"></i>
                                                                                <div class="dropdown-menu">
                                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#EditTargetOutputModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')">Edit</a>
                                                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#DeleteModal"  wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}})">Delete</a>
                                                                                </div>
                                                                            @endif
                                                                            {{ $target->pivot->target_output }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $target->pivot->alloted_budget }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $target->pivot->responsible }}
                                                                        </td>
                                                                    @else
                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')) && ((!$approval || (isset($approval->approve_status) && $approval->approve_status != 1))))
                                                                            <td colspan="3" class="text-center ">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    <button type="button" class="btn icon btn-primary" data-bs-toggle="modal"
                                                                                        data-bs-target="#AddTargetOutputModal" wire:click="selectOpcr('target_output',{{$output->id}}, {{$target->id}}, 'edit')" title="Add Target Output">
                                                                                        <i class="bi bi-plus"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                        @endif
                                                                    @endif
                        
                                                                    @forelse ($target->ratings as $rating)
                                                                        @if ($rating->user_id == auth()->user()->id) 
                                                                            <td>{{ $rating->accomplishment }}
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->quality)
                                                                                {{ $rating->quality }}
                                                                                @else
                                                                                NR
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->efficiency)
                                                                                    {{ $rating->efficiency }}
                                                                                @else
                                                                                    NR
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if ($rating->timeliness)
                                                                                    {{ $rating->timeliness }}
                                                                                @else
                                                                                    NR
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-nowrap">{{ $rating->average }}
                                                                            </td>
                                                                            <td>{{ $rating->remarks }}
                                                                            </td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-success"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#EditRatingModal"
                                                                                            wire:click="editRating({{ $rating->id }})"
                                                                                            title="Edit Rating">
                                                                                            <i class="bi bi-pencil-square"></i>
                                                                                        </button>
                                                                                        <button type="button"
                                                                                            class="btn icon btn-danger"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#DeleteModal"
                                                                                            title="Delete Rating"
                                                                                            wire:click="rating({{ 0 }}, {{ $rating->id }})">
                                                                                            <i class="bi bi-trash"></i>
                                                                                        </button>
                                                                                        <button type="button"
                                                                                            class="btn icon btn-secondary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddFilesModal"
                                                                                            title="Add Documents"
                                                                                            wire:click="file({{ $rating->id }})">
                                                                                            <i class="bi bi-paperclip"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                    <div class="dropup-center dropup">
                                                                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                            <i class="bi bi-file-earmark-check"></i>
                                                                                        </button>
                                                                                        <ul class="dropdown-menu p-2 border-1" style="width: 600px">
                                                                                            <li>
                                                                                                <div class="row border-bottom">
                                                                                                    <div class="col-3 border-end">
                                                                                                        <div class="row">
                                                                                                            <div class="col-12 text-center text-nowrap">
                                                                                                                <small><strong>Old Score</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>Q</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>E</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>T</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>A</strong></small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-3 border-end">
                                                                                                        <div class="row">
                                                                                                            <div class="col-12 text-center text-nowrap">
                                                                                                                <small><strong>New Score</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>Q</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>E</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>T</strong></small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small><strong>A</strong></small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-3 text-center text-nowrap border-end">
                                                                                                        <small><strong>Updated By</strong></small>
                                                                                                    </div>
                                                                                                    <div class="col-3 text-center text-nowrap">
                                                                                                        <small><strong>Updated At</strong></small>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </li>
                                                                                                @foreach ($rating->score_logs()->where('user_id', auth()->user()->id)->get() as $score_log)
                                                                                                    <li>
                                                                                                        <div class="row">
                                                                                                            <div class="col-3 border-end">
                                                                                                                <div class="row">
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_qua ? $score_log->old_qua : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_eff ? $score_log->old_eff : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_time ? $score_log->old_time : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-danger' }}">
                                                                                                                        <small>{{ $score_log->old_ave }}</small>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="col-3 border-end">
                                                                                                                <div class="row">
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_qua == $score_log->new_qua) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_qua ? $score_log->new_qua : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_eff == $score_log->new_eff) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_eff ? $score_log->new_eff : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_time == $score_log->new_time) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_time ? $score_log->new_time : 'NR' }}</small>
                                                                                                                    </div>
                                                                                                                    <div class="col-3 text-center text-nowrap {{ ($score_log->old_ave == $score_log->new_ave) ? '' : 'text-success' }}">
                                                                                                                        <small>{{ $score_log->new_ave }}</small>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap border-end">
                                                                                                                <small>{{ $score_log->updatedBy->name }}</small>
                                                                                                            </div>
                                                                                                            <div class="col-3 text-center text-nowrap">
                                                                                                                <small>{{ $score_log->updated_at->diffForHumans() }}</small>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            @break
                                                                        @elseif ($loop->last)
                                                                            <td colspan="6"></td>
                                                                            <td class="">
                                                                                <div class="hstack align-items-center justify-content-center gap-2">
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddRatingModal"
                                                                                            wire:click="rating({{ $target->id }})"
                                                                                            title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                            <i class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        @endif
                                                                    @empty
                                                                        <td colspan="6"></td>
                                                                        <td class="">
                                                                            <div class="hstack align-items-center justify-content-center gap-2">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')) && (($approval && (isset($approval->approve_status) && $approval->approve_status == 1))) && ((!$assess || (isset($assess->approve_status) && $assess->approve_status != 1))))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#AddRatingModal"
                                                                                        wire:click="rating({{ $target->id }})"
                                                                                        title="Add Rating" {{ isset($approvalStandard) ? "" : "disabled" }}>
                                                                                        <i class="bi bi-plus"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    @endforelse
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            @endforeach
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </section>

    {{ $functs->links('components.pagination') }}
    @php
        $subFuncts = auth()->user()->sub_functs()->where('type', 'opcr')->where('user_type', 'office')->get();
        $currentPage = $functs->currentPage();
        $type = 'office';
        $targetID = $target_id;
    @endphp
    <x-modals :targetName="$targetName" :print="$print" :filter="$filter" :selected="$selected" :currentPage="$currentPage" :duration="$duration" :subFuncts="$subFuncts"  :targetAllocated="$targetAllocated" :targetID="$targetID" :selectedOutput="$selectedOutput" :selectedTarget="$selectedTarget" :type="$type" :targetOutput="$targetOutput" />
</div>