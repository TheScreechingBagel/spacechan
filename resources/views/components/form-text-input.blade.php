@props(['label', 'name', 'value' => null, 'type' => 'text'])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
           value="{{ old($name, $value) }}"
        {{ $attributes->class(['mt-1 w-full rounded border-gray-300', 'border-red-600' => $errors->has($name)]) }}>
    @error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
