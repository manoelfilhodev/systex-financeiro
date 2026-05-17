<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sx-button-danger']) }}>
    {{ $slot }}
</button>
