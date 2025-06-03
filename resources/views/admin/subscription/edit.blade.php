<x-app-layout>
    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h1 class="mb-6 text-3xl font-extrabold tracking-tight text-gray-800">
                    Edit Subscription Plan
                </h1>
                <form method="POST" action="{{ route('admin.subscriptions.update', $plan->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="name" class="block mb-1 font-semibold text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="price" class="block mb-1 font-semibold text-gray-700">Price</label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $plan->price) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="duration_days" class="block mb-1 font-semibold text-gray-700">Duration (Days)</label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $plan->duration_days) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block mb-1 font-semibold text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $plan->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="subjects" class="block mb-1 font-semibold text-gray-700">Subjects</label>
                        <select name="subjects[]" id="subjects" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @if (collect(old('subjects', $plan->subjects->pluck('id')->toArray()))->contains($subject->id)) selected @endif>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subjects')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.subscriptions.index') }}"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancel</a>
                        <button type="submit"
                            class="px-4 py-2 font-semibold text-white bg-indigo-600 rounded hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
