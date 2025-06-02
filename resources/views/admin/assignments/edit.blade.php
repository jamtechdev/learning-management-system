<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Edit Assessment
                </h2>
                <form action="{{route('admin.assignments.update', $assessment->id)}}" method="POST">
                    @csrf

                    @if (isset($user))
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                     @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Title -->
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" value="{{old('title', $assessment->title)}}" required
                                class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                        </div>
                         <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Students</label>
                            <select name="student_id"  id="" required class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                                   @foreach ($students as $student)
                                             <option value="{{$student->id}}" {{ $student->id == $assessment->user_id ? 'selected' : '' }}>{{$student->first_name}}</option>
                                   @endforeach
                            </select>

                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Update Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
