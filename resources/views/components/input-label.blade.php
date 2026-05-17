@props(['value'])

<label {{ $attributes->merge(['class' => 'sx-label']) }}>
    {{ $value ?? $slot }}
</label>
