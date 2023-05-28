<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Profile</h3>
                <p class="text-subtitle text-muted">Edit your profile.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="hstack gap-3 justify-content-center">
            @foreach (auth()->user()->account_types as $account_type)
               @if (str_contains(strtolower($account_type), 'faculty'))
                    <a href="{{ route('print.listings.faculty') }}" target="_blank" class="btn icon btn-primary" title="Print List of Faculty IPCR">
                        <i class="bi bi-printer"></i>
                        List of Faculty IPCR
                    </a>
                    <a href="{{ route('print.rankings.faculty') }}" target="_blank" class="btn icon btn-primary" title="Print Rank of Faculty IPCR">
                        <i class="bi bi-printer"></i>
                        Rank of Faculty IPCR
                    </a>
               @endif 
            @endforeach
        </div>
    </div>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                
                <div class="mt-10 sm:mt-0">
                    @livewire('profile-form')
                </div>

                <x-jet-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-jet-section-border />
            @endif

            {{-- @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-jet-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div> --}}

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-jet-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
