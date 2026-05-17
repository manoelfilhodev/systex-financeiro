<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sx-button']) }}>
    {{ $slot }}
</button>
