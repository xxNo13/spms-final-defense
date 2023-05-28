<div>
    <div class="form-group" wire:ignore>
        <label for="account_type">Account Types</label>
        <select name="account_type[]" id="account_type" class="form-select" multiple="multiple">
            <option></option>
            @foreach ($account_types as $account_type)
                <option value="{{$account_type->id}}" {{ (collect(old('account_type'))->contains($account_type->id)) ? 'selected':'' }}>{{$account_type->account_type}}</option>
            @endforeach
        </select>
    </div>

    <!-- Faculty Position -->
    <div class="form-group">
        <label for="faculty_position">Faculty Rank</label>
        <select name="faculty_position_id" wire:model="faculty_position_id" id="faculty_position_id" class="form-select">
            <option></option>
            @foreach ($faculty_positions as $faculty_position)
                <option value="{{$faculty_position->id}}" {{ (collect(old('faculty_position_id'))->contains($faculty_position->id)) ? 'selected':'' }}>{{$faculty_position->position_name}}</option>
            @endforeach
        </select>
    </div>
    
    @push('script')
        <script>
            $('#account_type').select2({
                placeholder: "Select an Account Type",
                multiple: true,
            });

            $('#account_type').on('change', function () {
                var data = $('#account_type').select2("val");
                @this.set('account_type_selected', data);
            });

            document.addEventListener("livewire:load", function (event) {
                Livewire.hook('message.processed', function (message, component){
                    $('#account_type').select2({
                        placeholder: "Select an Account Type",
                        multiple: true,
                    });
                    
                    $('#faculty_position_id').select2({
                        placeholder: "Select a Faculty Rank",
                    });
                });
            });
            
            $('select[name="account_type[]"]').on('change',function(){
                var $this = $(this); 
                $('select[name="account_type[]"]').find('option[value="1"]').prop('disabled', ($this.val().indexOf("2") > -1) ); // disabled or enabled 
                $('select[name="account_type[]"]').find('option[value="2"]').prop('disabled', ($this.val().indexOf("1") > -1) ); // disabled or enabled 
            });

            $('select[name="account_type[]"]').trigger('change');
        </script>
    @endpush
</div>