<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SPMS') }}</title>

        <!-- Styles -->
        @include('layouts.partials.styles')
    </head>
    <body>
        <div id="app">
            @include('layouts.partials.sidebar')
            
            <div id="main" class='layout-navbar'>
                @include('layouts.partials.header')
                <div id="main-content">

                    <div class="page-heading mt-5 pt-5">
                        {{ $slot }}
                    </div>

                    <button onclick="scrollToTop()" class="mb-3 mx-3 p-2 py-0 btn btn-secondary rounded-circle position-fixed fs-2 bottom-0 end-0">
                        <i class="bi bi-arrow-up-short"></i>
                    </button>
                    @include('layouts.partials.footer')
                </div>
            </div>
        </div>

        {{-- Update Password Modal --}}
        <div wire:ignore.self data-bs-backdrop="static"  class="modal fade text-left" id="PasswordModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel33">Update Password</h4>
                    </div>
                    @livewire('profile.update-password-form')
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @include('layouts.partials.scripts')

    </body>
</html>
