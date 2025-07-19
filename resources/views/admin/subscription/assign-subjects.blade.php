<x-app-layout>
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h1 class="mb-6 text-3xl font-extrabold tracking-tight text-gray-800">
                    Assign Subjects to Subscription Plan: {{ $plan->name }}
                </h1>

                <form method="POST" action="{{ route('admin.subscriptions.assignSubjects.store', $plan->id) }}">
                    @csrf

                    @foreach ($levels as $level)
                        <div class="p-4 mb-6 border border-gray-200 rounded-md">
                            <h2 class="mb-3 text-xl font-bold text-indigo-700">Level: {{ $level->name }}</h2>

                            @php
                                $levelSubjects = $subjects->where('level_id', $level->id);
                            @endphp

                            @if ($levelSubjects->isEmpty())
                                <p class="text-sm text-gray-500">No subjects found for this level.</p>
                            @else
                                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                                    @foreach ($levelSubjects as $subject)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                                @checked(collect(old('subjects', $plan->subjects->pluck('id')->toArray()))->contains($subject->id))
                                                class="text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <span class="text-gray-800">{{ $subject->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @error('subjects')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-between mt-6">
                        <a href="{{ route('admin.subscriptions.index') }}"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancel</a>
                        <button type="submit"
                            class="px-4 py-2 font-semibold text-white bg-[#3e80f9] rounded bg-[#f7941e]">
                            Assign Subjects
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
