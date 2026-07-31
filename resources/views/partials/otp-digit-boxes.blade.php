@php $refPrefix = $refPrefix ?? 'd'; @endphp
<div class="d-flex justify-content-center gap-2 otp-box mb-3">
    @for ($i = 0; $i < 6; $i++)
        <input type="text"
            inputmode="numeric"
            maxlength="1"
            class="form-control text-center fs-3 fw-bold"
            x-ref="{{ $refPrefix }}{{ $i }}"
            @input="onInput({{ $i }}, $event, '{{ $refPrefix }}')"
            @keydown="onKeydown({{ $i }}, $event, '{{ $refPrefix }}')"
            @paste="onPaste($event, '{{ $refPrefix }}')"
            autocomplete="one-time-code">
    @endfor
</div>
