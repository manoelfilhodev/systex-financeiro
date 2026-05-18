@props(['user'])

@php
    $name = trim((string) $user->name);
    $nameParts = collect(preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY));
    $emailName = str($user->email)->before('@')->toString();
    $initials = $nameParts->count() >= 2
        ? $nameParts->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->join('')
        : mb_substr(($name ?: $emailName).$emailName, 0, 2);
    $initials = mb_strtoupper($initials ?: 'SX');
@endphp

@if (filled($user->avatar))
    <img
        src="{{ $user->avatar }}"
        alt="{{ $user->name }}"
        referrerpolicy="no-referrer"
        {{ $attributes->merge(['class' => 'sx-avatar object-cover']) }}
    >
@else
    <span {{ $attributes->merge(['class' => 'sx-avatar']) }}>
        {{ $initials }}
    </span>
@endif
