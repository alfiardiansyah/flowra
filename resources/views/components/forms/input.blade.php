@props(['label' => '', 'name' => '', 'type' => 'text', 'value' => ''])
<div class="floating">
  <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder=" " value="{{ old($name, $value) }}" {{ $attributes }} />
  <label for="{{ $name }}">{{ $label }}</label>
</div>
