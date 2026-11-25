@props(['label', 'name', 'value' => null])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="5"
              {{ $attributes->class(['mt-1 w-full rounded border-gray-300', 'border-red-600' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
