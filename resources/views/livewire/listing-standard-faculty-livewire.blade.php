<div>
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Standard</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Standard - Faculty</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-3">
        @foreach ($functs as $funct)
            <div class="hstack mb-3">
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
            </div>
            @if ($duration)
                @foreach ($funct->sub_functs()->where('type', 'ipcr')->where('user_type', 'faculty')->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $sub_funct)
                    <div>
                        <h5>
                            {{ $sub_funct->sub_funct }}
                        </h5>
                        @foreach ($sub_funct->outputs()->where('type', 'ipcr')->where('user_type', 'faculty')->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $output)
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ $output->code }} {{ $output->output }}</h4>
                                        <p class="text-subtitle text-muted"></p>
                                    </div>
                                    @forelse ($output->suboutputs()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $suboutput)
                                        <div class="card-body">
                                            <h6>{{ $suboutput->suboutput }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="accordion accordion-flush"
                                                id="{{ 'suboutput' }}{{ $suboutput->id }}">
                                                <div class="row">
                                                    @foreach ($suboutput->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                        <div class="col-12 col-sm-4">
                                                            <div wire:ignore.self
                                                                class="accordion-button collapsed gap-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                                aria-expanded="true"
                                                                aria-controls="{{ 'target' }}{{ $target->id }}"
                                                                role="button">
                                                                @foreach ($target->standards as $standard)
                                                                    @if ($standard->user_id == null)
                                                                        <span class="my-auto">
                                                                            <i class="bi bi-check2"></i>
                                                                        </span>
                                                                        @break
                                                                    @endif
                                                                @endforeach
                                                                {{ $target->target }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @foreach ($suboutput->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                    <div wire:ignore.self
                                                        id="{{ 'target' }}{{ $target->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="flush-headingOne"
                                                        data-bs-parent="#{{ 'suboutput' }}{{ $suboutput->id }}">
                                                        <div class="accordion-body table-responsive">
                                                            <table class="table table-lg text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <td colspan="6">Rating</td>
                                                                        <td rowspan="2">Action</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">Q</td>
                                                                        <td colspan="2">E</td>
                                                                        <td colspan="2">T</td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($target->standards as $standard)
                                                                        @if ($standard->user_id == null) 
                                                                            <tr>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_5 ? $standard->qua_5 : 'NR' }}
                                                                                </td>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_5 ? $standard->eff_5 : 'NR' }}
                                                                                </td>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_5 ? $standard->time_5 : 'NR' }}
                                                                                </td>
                                                                                <td rowspan="5">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-success"
                                                                                                wire:click="clicked('{{ 'edit' }}', {{ $standard->id }})"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#EditStandardModal"
                                                                                                title="Edit Standard">
                                                                                                <i
                                                                                                    class="bi bi-pencil-square"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-danger"
                                                                                                wire:click="clicked('{{ 'delete' }}', {{ $standard->id }})"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#DeleteModal"
                                                                                                title="Delete Standard">
                                                                                                <i
                                                                                                    class="bi bi-trash"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_4 ? $standard->qua_4 : 'NR' }}
                                                                                </td>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_4 ? $standard->eff_4 : 'NR' }}
                                                                                </td>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_4 ? $standard->time_4 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_3 ? $standard->qua_3 : 'NR' }}
                                                                                </td>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_3 ? $standard->eff_3 : 'NR' }}
                                                                                </td>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_3 ? $standard->time_3 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_2 ? $standard->qua_2 : 'NR' }}
                                                                                </td>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_2 ? $standard->eff_2 : 'NR' }}
                                                                                </td>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_2 ? $standard->time_2 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_1 ? $standard->qua_1 : 'NR' }}
                                                                                </td>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_1 ? $standard->eff_1 : 'NR' }}
                                                                                </td>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_1 ? $standard->time_1 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            @break
                                                                        @elseif ($loop->last)
                                                                            <tr>
                                                                                <td colspan="6"></td>
                                                                                <td>
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddStandardModal"
                                                                                            title="Add Standard">
                                                                                            <i
                                                                                                class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6"></td>
                                                                            <td>
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-primary"
                                                                                        wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#AddStandardModal"
                                                                                        title="Add Standard">
                                                                                        <i
                                                                                            class="bi bi-plus"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
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
                                                    @foreach ($output->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                        <div class="col-12 col-sm-4">
                                                            <div wire:ignore.self
                                                                class="accordion-button collapsed gap-2"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                                aria-expanded="true"
                                                                aria-controls="{{ 'target' }}{{ $target->id }}"
                                                                role="button">
                                                                @foreach ($target->standards as $standard)
                                                                    @if ($standard->user_id == null)
                                                                        <span class="my-auto">
                                                                            <i class="bi bi-check2"></i>
                                                                        </span>
                                                                        @break
                                                                    @endif
                                                                @endforeach
                                                                {{ $target->target }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @foreach ($output->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                    <div wire:ignore.self
                                                        id="{{ 'target' }}{{ $target->id }}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="flush-headingOne"
                                                        data-bs-parent="#{{ 'output' }}{{ $output->id }}">
                                                        <div class="accordion-body table-responsive">
                                                            <table class="table table-lg text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <td colspan="6">Rating</td>
                                                                        <td rowspan="2">Action</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="2">Q</td>
                                                                        <td colspan="2">E</td>
                                                                        <td colspan="2">T</td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse ($target->standards as $standard)
                                                                        @if ($standard->user_id == null) 
                                                                            <tr>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_5 ? $standard->qua_5 : 'NR' }}
                                                                                </td>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_5 ? $standard->eff_5 : 'NR' }}
                                                                                </td>
                                                                                <td>5</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_5 ? $standard->time_5 : 'NR' }}
                                                                                </td>
                                                                                <td rowspan="5">
                                                                                    <div class="hstack align-items-center justify-content-center gap-2">
                                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                            <button type="button"
                                                                                                class="btn icon btn-success"
                                                                                                wire:click="clicked('{{ 'edit' }}', {{ $standard->id }})"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#EditStandardModal"
                                                                                                title="Edit Standard">
                                                                                                <i
                                                                                                    class="bi bi-pencil-square"></i>
                                                                                            </button>
                                                                                            <button type="button"
                                                                                                class="btn icon btn-danger"
                                                                                                wire:click="clicked('{{ 'delete' }}', {{ $standard->id }})"
                                                                                                data-bs-toggle="modal"
                                                                                                data-bs-target="#DeleteModal"
                                                                                                title="Delete Standard">
                                                                                                <i
                                                                                                    class="bi bi-trash"></i>
                                                                                            </button>
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_4 ? $standard->qua_4 : 'NR' }}
                                                                                </td>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_4 ? $standard->eff_4 : 'NR' }}
                                                                                </td>
                                                                                <td>4</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_4 ? $standard->time_4 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_3 ? $standard->qua_3 : 'NR' }}
                                                                                </td>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_3 ? $standard->eff_3 : 'NR' }}
                                                                                </td>
                                                                                <td>3</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_3 ? $standard->time_3 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_2 ? $standard->qua_2 : 'NR' }}
                                                                                </td>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_2 ? $standard->eff_2 : 'NR' }}
                                                                                </td>
                                                                                <td>2</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_2 ? $standard->time_2 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->qua_1 ? $standard->qua_1 : 'NR' }}
                                                                                </td>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->eff_1 ? $standard->eff_1 : 'NR' }}
                                                                                </td>
                                                                                <td>1</td>
                                                                                <td style="max-width: 150px;">{{ $standard->time_1 ? $standard->time_1 : 'NR' }}
                                                                                </td>
                                                                            </tr>
                                                                            @break
                                                                        @elseif ($loop->last)
                                                                            <tr>
                                                                                <td colspan="6"></td>
                                                                                <td>
                                                                                    @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                        <button type="button"
                                                                                            class="btn icon btn-primary"
                                                                                            wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#AddStandardModal"
                                                                                            title="Add Standard">
                                                                                            <i
                                                                                                class="bi bi-plus"></i>
                                                                                        </button>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6"></td>
                                                                            <td>
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-primary"
                                                                                        wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#AddStandardModal"
                                                                                        title="Add Standard">
                                                                                        <i
                                                                                            class="bi bi-plus"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
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
                @foreach ($funct->outputs()->where('type', 'ipcr')->where('user_type', 'faculty')->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $output)
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ $output->code }} {{ $output->output }}</h4>
                                <p class="text-subtitle text-muted"></p>
                            </div>
                            @forelse ($output->suboutputs()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $suboutput)
                                <div class="card-body">
                                    <h6>{{ $suboutput->suboutput }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="accordion accordion-flush"
                                        id="{{ 'output' }}{{ $output->id }}">
                                        <div class="row">
                                            @foreach ($suboutput->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                <div class="col-12 col-sm-4">
                                                    <div wire:ignore.self
                                                        class="accordion-button collapsed gap-2"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                        aria-expanded="true"
                                                        aria-controls="{{ 'target' }}{{ $target->id }}"
                                                        role="button">
                                                        @foreach ($target->standards as $standard)
                                                            @if ($standard->user_id == null)
                                                                <span class="my-auto">
                                                                    <i class="bi bi-check2"></i>
                                                                </span>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        {{ $target->target }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @foreach ($suboutput->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                            <div wire:ignore.self
                                                id="{{ 'target' }}{{ $target->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="flush-headingOne"
                                                data-bs-parent="#{{ 'output' }}{{ $output->id }}">
                                                <div class="accordion-body table-responsive">
                                                    <table class="table table-lg text-center">
                                                        <thead>
                                                            <tr>
                                                                <td colspan="6">Rating</td>
                                                                <td rowspan="2">Action</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Q</td>
                                                                <td colspan="2">E</td>
                                                                <td colspan="2">T</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($target->standards as $standard)
                                                                @if ($standard->user_id == null) 
                                                                    <tr>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_5 ? $standard->qua_5 : 'NR' }}
                                                                        </td>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_5 ? $standard->eff_5 : 'NR' }}
                                                                        </td>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_5 ? $standard->time_5 : 'NR' }}
                                                                        </td>
                                                                        <td rowspan="5">
                                                                            <div class="hstack align-items-center justify-content-center gap-2">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-success"
                                                                                        wire:click="clicked('{{ 'edit' }}', {{ $standard->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#EditStandardModal"
                                                                                        title="Edit Standard">
                                                                                        <i
                                                                                            class="bi bi-pencil-square"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn icon btn-danger"
                                                                                        wire:click="clicked('{{ 'delete' }}', {{ $standard->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#DeleteModal"
                                                                                        title="Delete Standard">
                                                                                        <i
                                                                                            class="bi bi-trash"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_4 ? $standard->qua_4 : 'NR' }}
                                                                        </td>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_4 ? $standard->eff_4 : 'NR' }}
                                                                        </td>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_4 ? $standard->time_4 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_3 ? $standard->qua_3 : 'NR' }}
                                                                        </td>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_3 ? $standard->eff_3 : 'NR' }}
                                                                        </td>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_3 ? $standard->time_3 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_2 ? $standard->qua_2 : 'NR' }}
                                                                        </td>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_2 ? $standard->eff_2 : 'NR' }}
                                                                        </td>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_2 ? $standard->time_2 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_1 ? $standard->qua_1 : 'NR' }}
                                                                        </td>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_1 ? $standard->eff_1 : 'NR' }}
                                                                        </td>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_1 ? $standard->time_1 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    @break
                                                                @elseif ($loop->last)
                                                                    <tr>
                                                                        <td colspan="6"></td>
                                                                        <td>
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                <button type="button"
                                                                                    class="btn icon btn-primary"
                                                                                    wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#AddStandardModal"
                                                                                    title="Add Standard">
                                                                                    <i
                                                                                        class="bi bi-plus"></i>
                                                                                </button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6"></td>
                                                                    <td>
                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                            <button type="button"
                                                                                class="btn icon btn-primary"
                                                                                wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#AddStandardModal"
                                                                                title="Add Standard">
                                                                                <i
                                                                                    class="bi bi-plus"></i>
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforelse
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
                                            @foreach ($output->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                                <div class="col-12 col-sm-4">
                                                    <div wire:ignore.self
                                                        class="accordion-button collapsed gap-2"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#{{ 'target' }}{{ $target->id }}"
                                                        aria-expanded="true"
                                                        aria-controls="{{ 'target' }}{{ $target->id }}"
                                                        role="button">
                                                        @foreach ($target->standards as $standard)
                                                            @if ($standard->user_id == null)
                                                                <span class="my-auto">
                                                                    <i class="bi bi-check2"></i>
                                                                </span>
                                                                @break
                                                            @endif
                                                        @endforeach
                                                        {{ $target->target }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @foreach ($output->targets()->where('duration_id', $duration->id)->where('added_by', '!=', null)->get() as $target)
                                            <div wire:ignore.self
                                                id="{{ 'target' }}{{ $target->id }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="flush-headingOne"
                                                data-bs-parent="#{{ 'output' }}{{ $output->id }}">
                                                <div class="accordion-body table-responsive">
                                                    <table class="table table-lg text-center">
                                                        <thead>
                                                            <tr>
                                                                <td colspan="6">Rating</td>
                                                                <td rowspan="2">Action</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Q</td>
                                                                <td colspan="2">E</td>
                                                                <td colspan="2">T</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($target->standards as $standard)
                                                                @if ($standard->user_id == null) 
                                                                    <tr>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_5 ? $standard->qua_5 : 'NR' }}
                                                                        </td>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_5 ? $standard->eff_5 : 'NR' }}
                                                                        </td>
                                                                        <td>5</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_5 ? $standard->time_5 : 'NR' }}
                                                                        </td>
                                                                        <td rowspan="5">
                                                                            <div class="hstack align-items-center justify-content-center gap-2">
                                                                                @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                    <button type="button"
                                                                                        class="btn icon btn-success"
                                                                                        wire:click="clicked('{{ 'edit' }}', {{ $standard->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#EditStandardModal"
                                                                                        title="Edit Standard">
                                                                                        <i
                                                                                            class="bi bi-pencil-square"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn icon btn-danger"
                                                                                        wire:click="clicked('{{ 'delete' }}', {{ $standard->id }})"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#DeleteModal"
                                                                                        title="Delete Standard">
                                                                                        <i
                                                                                            class="bi bi-trash"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_4 ? $standard->qua_4 : 'NR' }}
                                                                        </td>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_4 ? $standard->eff_4 : 'NR' }}
                                                                        </td>
                                                                        <td>4</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_4 ? $standard->time_4 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_3 ? $standard->qua_3 : 'NR' }}
                                                                        </td>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_3 ? $standard->eff_3 : 'NR' }}
                                                                        </td>
                                                                        <td>3</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_3 ? $standard->time_3 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_2 ? $standard->qua_2 : 'NR' }}
                                                                        </td>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_2 ? $standard->eff_2 : 'NR' }}
                                                                        </td>
                                                                        <td>2</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_2 ? $standard->time_2 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->qua_1 ? $standard->qua_1 : 'NR' }}
                                                                        </td>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->eff_1 ? $standard->eff_1 : 'NR' }}
                                                                        </td>
                                                                        <td>1</td>
                                                                        <td style="max-width: 150px;">{{ $standard->time_1 ? $standard->time_1 : 'NR' }}
                                                                        </td>
                                                                    </tr>
                                                                    @break
                                                                @elseif ($loop->last)
                                                                    <tr>
                                                                        <td colspan="6"></td>
                                                                        <td>
                                                                            @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                                <button type="button"
                                                                                    class="btn icon btn-primary"
                                                                                    wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#AddStandardModal"
                                                                                    title="Add Standard">
                                                                                    <i
                                                                                        class="bi bi-plus"></i>
                                                                                </button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6"></td>
                                                                    <td>
                                                                        @if (($duration && $duration->end_date >= date('Y-m-d')))
                                                                            <button type="button"
                                                                                class="btn icon btn-primary"
                                                                                wire:click="clicked('{{ 'add' }}', {{ $target->id }})"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#AddStandardModal"
                                                                                title="Add Standard">
                                                                                <i
                                                                                    class="bi bi-plus"></i>
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforelse
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
            @endif
        @endforeach
    </section>

    {{ $functs->links('components.pagination') }}
    <x-modals :standardValue="$standardValue"  />
</div>
