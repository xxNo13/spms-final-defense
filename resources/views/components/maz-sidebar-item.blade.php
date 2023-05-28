@props(['icon', 'link', 'name', 'alias'])

@php
$routeName = Request::route()->getName();
$classes = str_contains($routeName, strtolower($alias))
    ? 'sidebar-item active'
    : 'sidebar-item';
@endphp

<li class="{{ $classes }} {{$slot->isEmpty() ? '' : 'has-sub'}}">
    <a href="{{ $slot->isEmpty() ? $link : '#' }}" class='sidebar-link'>
        <i class="{{ $icon }}"></i>
        <span>{{ $name }}</span>
    </a>
    @if(!$slot->isEmpty())
    <ul class="submenu">
        {{$slot}}
    </ul>
    @endif
</li>