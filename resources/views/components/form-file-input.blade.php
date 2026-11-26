@props(['label', 'name'])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <input type="file" id="{{ $name }}" name="{{ $name }}" {{ $attributes->class(['mt-1 w-full']) }}>
    @error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
