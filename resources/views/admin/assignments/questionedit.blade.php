<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-8xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow-xl sm:rounded-lg">
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Edit Assessment Question
                </h2>
                <form action="{{route('admin.assignments.questionupdate', $questionAssignment->id)}}" method="POST">
                    @csrf
                      @method('PUT')
                    @if (isset($user))
                         <input type="hidden" name="user_id" value="{{ $user->id }}">
                     @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Question</label>
                            <select name="question_id" id="" required class="w-full px-4 py-2 border rounded-md shadow-sm focus:ring focus:ring-indigo-200">

                                   @foreach ($questions as $question)
                                             <option value="{{old('content', $question->id)}}">{{$question->content}}</option>
                                   @endforeach
                            </select>
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                            class="px-6 py-2 text-white bg-green-500  focus:outline-none">
                            Update Assessment Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>


