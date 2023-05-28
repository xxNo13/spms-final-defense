<section class="section">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ __('Profile Information') }}</h4>
            <p class="card-description">{{ __('Update your account\'s profile information and email address.') }}</p>
        </div>
        <div class="card-body">
            
            <x-maz-alert class="mr-3" on="saved" color='success'>
                Saved
            </x-maz-alert>
            <form wire:submit.prevent="updateProfileInformation">
                
                <div class="row">
                    <div class="col-md-6">
                        @foreach (auth()->user()->offices as $off)
                            @foreach($off->users as $user)
                                @if ($user->pivot->isHead == 1 && $user->pivot->user_id != auth()->user()->id)
                                    @break
                                @elseif ($loop->last)
                                    <!-- is Head -->
                                    <div class="form-group">
                                        <input id="isHead.{{$off->id}}" type="checkbox" class="form-check-input" wire:model.defer="isHead.{{$off->id}}" autocomplete="isHead.{{$off->id}}" >
                                        <label for="isHead.{{$off->id}}">Head of the {{ $off->office_name }}</label>
                                        <x-maz-input-error for="isHead.{{$off->id}}" />
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        @foreach (auth()->user()->institutes as $inst)
                            @foreach($inst->users as $user)
                                @if ($user->pivot->isProgramChair == 1 && $user->pivot->user_id != auth()->user()->id)
                                    @break
                                @elseif ($loop->last)
                                    <!-- is Head -->
                                    <div class="form-group">
                                        <input id="isProgramChair.{{ $inst->id }}" type="checkbox" class="form-check-input" wire:model.defer="isProgramChair.{{ $inst->id }}" autocomplete="isProgramChair.{{ $inst->id }}" >
                                        <label for="isProgramChair.{{ $inst->id }}">Program Chair of the {{ $inst->institute_name }}</label>
                                        <x-maz-input-error for="isProgramChair.{{ $inst->id }}" />
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <hr>

                <!-- Name -->
                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" wire:model.defer="state.name" autocomplete="name" >
                    <x-maz-input-error for="name" />
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4">
                    <label for="email">Email</label>
                    <input type="email" name="email " id="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" wire:model.defer="state.email" >
                    <x-maz-input-error for="email" />
                </div>

                <hr>

                <!-- Account Types -->
                <div class="form-group" wire:ignore>
                    <label for="account_type">Account Types</label>
                    <select name="account_type" id="account_type" class="form-select" wire:model="account_type" multiple="multiple">
                        <option></option>
                        @foreach ($account_types as $choices)
                            <option value="{{$choices->id}}" 
                                {{ in_array($choices->id, $account_type) ? 'selected' : '' }}
                                >{{$choices->account_type}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Faculty Position -->
                <div class="form-group">
                    <label for="faculty_position">Faculty Rank</label>
                    <select name="faculty_position_id" id="faculty_position" class="form-select" wire:model="faculty_position_id">
                        <option></option>
                        @foreach ($faculty_positions as $choices)
                            <option value="{{$choices->id}}" 
                                {{ ($choices->id == $faculty_position_id) ? 'selected' : '' }}
                                >{{$choices->position_name}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Offices -->
                <div class="form-group" wire:ignore>
                    <label for="office">Offices</label>
                    <select name="office" id="office" class="form-select" wire:model="office" multiple="multiple">
                        <option></option>
                        @foreach ($offices as $choices)
                            <option value="{{$choices->id}}" {{ in_array($choices->id, $office) ? 'selected' : '' }}>{{$choices->office_name}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Institute -->
                <div class="form-group">
                    <label for="institute">Course</label>
                    <select name="institute" id="institute" class="form-select" wire:model="institute">
                        <option></option>
                        @foreach ($institutes as $choices)
                            <option value="{{$choices->id}}" {{ ($choices->id == $institute) ? 'selected' : '' }}>{{$choices->institute_name}}</option>
                        @endforeach
                    </select>
                </div>

                @push('script')
                    <script>
                        $('#office').select2({
                            placeholder: "Select an Office",
                            multiple: true,
                        });

                        $('#office').on('change', function () {
                            @this.office = $(this).val();
                        });

                        $('#institute').select2({
                            placeholder: "Select a Course",
                        });

                        $('#institute').on('change', function () {
                            @this.institute = $(this).val();
                        });
                        
                        $('#account_type').select2({
                            placeholder: "Select an Account Type",
                            multiple: true,
                        });

                        $('#account_type').on('change', function () {
                            @this.account_type = $(this).val();
                        });

                        $('#faculty_position').select2({
                            placeholder: "Select a Faculty Rank",
                        });

                        $('#faculty_position').on('change', function () {
                            @this.faculty_position_id = $(this).val();
                        });

                        document.addEventListener("livewire:load", function (event) {
                            Livewire.hook('message.processed', function (message, component){
                                $('#account_type').select2({
                                    placeholder: "Select an Account Type",
                                    multiple: true,
                                });

                                $('#institute').select2({
                                    placeholder: "Select a Course",
                                });

                                $('#office').select2({
                                    placeholder: "Select an Office",
                                    multiple: true,
                                });

                                $('#faculty_position').select2({
                                    placeholder: "Select a Faculty Rank",
                                });
                            });
                        });

                        $(document).ready(function(){

                            $('select[name="account_type"]').on('change',function(){
                                var $this = $(this); 
                                $('select[name="account_type"]').find('option[value="1"]').prop('disabled', ($this.val().indexOf("2") > -1) ); // disabled or enabled 
                                $('select[name="account_type"]').find('option[value="2"]').prop('disabled', ($this.val().indexOf("1") > -1) ); // disabled or enabled 
                            });

                            $('select[name="account_type"]').trigger('change');

                        });
                    </script>
                @endpush

                <button class="btn btn-primary float-end mt-2"  wire:loading.attr="disabled" wire:target="photo">Save</button>
            </form>
        </div>
    </div>
</section>
