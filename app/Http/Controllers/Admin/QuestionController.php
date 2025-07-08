<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\QuestionDataTable;
use App\Enum\QuestionTypes;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(QuestionDataTable $dataTable)
    {
        $questionTypes = \App\Enum\QuestionTypes::TYPES;
        $levels = \App\Models\QuestionLevel::with('subjects', 'subjects.topics')->get();
        $subjects = \App\Models\QuestionSubject::select('name')
            ->groupBy('name')
            ->get();
        $topics = \App\Models\QuestionTopic::select('name')
            ->groupBy('name')
            ->get();

        return $dataTable->render('admin.question.index', [
            'questionTypes' => $questionTypes,
            'levels' => $levels,
            'subjects' => $subjects,
            'topics' => $topics,
        ]);
    }

    public function create()
    {
        // Load levels with subjects eager loaded
        $levels = \App\Models\QuestionLevel::with('subjects', 'subjects.topics')->get()->groupBy('education_type');
        if ($levels->isEmpty()) {
            Session::flash('error', 'No levels found. Please create a level first.');
            return redirect()->route('admin.levels.create');
        }

        // dd($levels);
        return view('admin.question.create', compact('levels'));
    }


    public function edit($id)
    {
        $question = Question::with(['options', 'level', 'subject', 'topic'])->findOrFail($id);
        $levels = \App\Models\QuestionLevel::with('subjects')->get()->groupBy('education_type');

        return view('admin.question.edit', compact('question', 'levels'));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->input('question_data');

        switch ($data['type']) {
            case QuestionTypes::MCQ:
                $this->saveMcqQuestion($data);
                break;

            case QuestionTypes::FILL_IN_THE_BLANK:
                $this->saveFillBlankQuestion($data);
                break;

            case QuestionTypes::TRUE_FALSE:
                $this->saveTrueFalseQuestion($data);
                break;

            case QuestionTypes::LINKING:
                $this->saveLinkingQuestion($data, $request);
                break;

            case QuestionTypes::REARRANGING:
                $this->saveRearrangingQuestion($data);
                break;

            case QuestionTypes::COMPREHENSION:
                $this->saveComprehensionQuestion($data);
                break;

            case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                $this->saveGrammarClozeWithOptions($data);
                break;
            case QuestionTypes::EDITING:
                $this->saveEditingQuestion($data);
                break;
            case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                $this->saveClozeWithDropdownOptions($data);
                break;

            default:
                return response()->json(['error' => 'Invalid question type'], 400);
        }

        return redirect()->route('admin.questions.index');
    }

    // 1. MCQ
    public function saveMcqQuestion(array $data)
    {
        $correctIndex = (int) $data['correct_option'];
        $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
            return [
                'value' => $option['value'],
                'explanation' => $option['explanation'] ?? null,
                'is_correct' => ($index === $correctIndex),
            ];
        }, $data['options'], array_keys($data['options']));


        $answer = [
            'answer' => $structuredOptions[$correctIndex]['value'] ?? null,
            'format' => 'text',
        ];

        $payload = $data;
        $payload['options'] = $structuredOptions;
        $payload['answer'] = $answer;
        $payload['instruction'] = $data['instruction'] ?? '';
        unset($payload['correct_option']);


        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->education_type = $data['education_type'];
        $question->subject_id = $data['subject_id'];
        $question->level_id = $data['level_id'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $payload;
        $question->save();

        foreach ($structuredOptions as $index => $option) {
            \App\Models\QuestionOption::create([
                'question_id' => $question->id,
                'label' => chr(65 + $index),
                'value' => $option['value'],
                'is_correct' => $option['is_correct'],
            ]);
        }
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'MCQ question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    // 2. Fill in the Blank
    private function saveFillBlankQuestion($data)
    {

        $data['instruction'] = $data['instruction'] ?? '';

        // Decode the JSON fill_in_the_blank_metadata to array
        $blanks = json_decode($data['fill_in_the_blank_metadata'], true);

        // Build the metadata array
        $metadata = [
            'instruction'   => $data['instruction'],
            'type'  => $data['type'],
            'question_text' => $data['content'],
            'blanks'        => $blanks ?? [],
        ];

        // Save the Question
        $question = new Question();
        $question->topic_id       = $data['topic_id'];
        $question->type           = $data['type'];
        $question->content        = $data['content'];
        $question->explanation    = $data['explanation'] ?? null;
        $question->level_id       = $data['level_id'] ?? null;
        $question->subject_id     = $data['subject_id'] ?? null;
        $question->education_type = $data['education_type'] ?? null;
        $question->metadata       = $metadata; // auto-cast to JSON via $casts
        $question->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fill in the blank type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }


    // 3. True/False
    private function saveTrueFalseQuestion($data)
    {
        $transformed = [
            'type' => 'true_false',
            'content' => $data['content'],
            'instruction' => $data['instruction'] ?? '',
            'options' => [
                ['value' => 'True'],
                ['value' => 'False'],
            ],
            'answer' => [
                'choice' => $data['true_false_answer'],
                'explanation' => $data['explanation'] ?? null,
                'format' => $data['format'] ?? 'text',
            ],
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'True and False type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    // 4. Linking
    private function saveLinkingQuestion($data, $request)
    {
        $answer = [];

        foreach ($data['options'] as $index => $option) {
            $leftImageUri = null;
            $rightImageUri = null;

            if (($option['label_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.label_image")) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $storedPath = $leftImage->store('uploads/linking', 'public');
                $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            }

            if (($option['value_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.value_image")) {
                $rightImage = $request->file("question_data.options.$index.value_image");
                $storedPath = $rightImage->store('uploads/linking', 'public');
                $rightImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            }

            $answer[] = [
                'left' => [
                    'word' => $option['label_text'] ?? '',
                    'image_uri' => $leftImageUri,
                    'match_type' => $option['label_type'] ?? 'text',
                ],
                'right' => [
                    'word' => $option['value_text'] ?? '',
                    'image_uri' => $rightImageUri,
                    'match_type' => $option['value_type'] ?? 'text',
                ],
            ];
        }

        $transformed = [
            'type' => 'linking',
            'content' => $data['content'] ?? '',
            'explanation' => $data['explanation'] ?? '',
            'instruction' => $data['instruction'] ?? '',
            'format' => 'mapping',
            'answer' => $answer,
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Linking type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    // 5. Rearranging
    private function saveRearrangingQuestion($data)
    {
        $questionText = $data['question_text'];
        $orderedAnswer = preg_split('/\s+/', trim($questionText));
        $shuffled = $orderedAnswer;
        shuffle($shuffled);

        $options = collect($shuffled)->map(fn($word) => [
            'value' => $word,
            'is_correct' => false,
        ])->values()->toArray();

        $transformed = [
            'type' => 'rearranging',
            'content' => $data['content'] ?? '',
            'instruction' => $data['instruction'] ?? '',
            'options' => $options,
            'answer' => [
                'answer' => $orderedAnswer,
                'format' => 'ordered'
            ],
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Rearrange type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    // 6. Grammar Cloze With Options
    private function saveGrammarClozeWithOptions(array $data)
    {
        $metadata = json_decode($data['metadata'], true);
        // dd($metadata);
        $metadata['instruction'] = $data['instruction'] ?? '';
        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $metadata['paragraph'] ?? '';
        $question->education_type = $data['education_type'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->explanation = $metadata['explanation'] ?? null;
        $question->metadata = $metadata;
        $question->save();
        // Success message with Toastr
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Grammar Cloze With Options type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    // 7. Comprehension
    private function saveComprehensionQuestion(array $data)
    {
        $decodedMetadata = is_string($data['comprehension_metadata'])
            ? json_decode($data['comprehension_metadata'], true)
            : $data['comprehension_metadata'];

        $fullMetadata = [
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'instruction' => $data['instruction'] ?? '',
            'passage' => $decodedMetadata['passage'] ?? null,
            'subquestions' => $decodedMetadata['subquestions'] ?? [],
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $fullMetadata['passage'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Comprehension  type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    protected function saveEditingQuestion($data)
    {
        // Parse the editing_metadata JSON
        $decodedMetadata = json_decode($data['editing_metadata'], true);
        $fullMetadata = [
            'instruction' => $data['instruction'] ?? '',
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'paragraph' => $decodedMetadata['paragraph'] ?? null,
            'questions' => $decodedMetadata['questions'] ?? [],
        ];

        // dd($fullMetadata);
        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Editing type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }

    protected function saveClozeWithDropdownOptions($data)
    {
        // Parse the editing_metadata JSON
        $decodedMetadata = json_decode($data['underline_metadata'], true);
        // dd($decodedMetadata);
        $fullMetadata = [
            'instruction' => $data['instruction'] ?? '',
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'paragraph' => $data['content'] ?? null,
            'questions' => $decodedMetadata['questions'] ?? [],
        ];

        $question = new Question();
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();
        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Cloze With Dropdown Options type question created successfully.!'
        ]);
        return redirect()->route('admin.questions.index');
    }


    // // Update existing question
    // public function update(Request $request, $question)
    // {
    //     // dd($request->all());
    //     $question = Question::findOrFail($question);

    //     $data = $request->input('question_data');

    //     switch ($data['type']) {
    //         case QuestionTypes::MCQ:
    //             $this->updateMcqQuestion($question, $data);
    //             break;

    //         case QuestionTypes::FILL_IN_THE_BLANK:
    //             $this->updateFillBlankQuestion($question, $data);
    //             break;

    //         case QuestionTypes::TRUE_FALSE:
    //             $this->updateTrueFalseQuestion($question, $data);
    //             break;

    //         case QuestionTypes::LINKING:
    //             $this->updateLinkingQuestion($question, $data, $request);
    //             break;

    //         case QuestionTypes::REARRANGING:
    //             $this->updateRearrangingQuestion($question, $data);
    //             break;
    //         case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
    //             $this->updateGrammarClozeWithOptions($question, $data);
    //             break;

    //         case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
    //             $this->updateClozeWithDropdownOptions($question, $data);
    //             break;

    //         case QuestionTypes::COMPREHENSION:
    //             $this->updateComprehensionQuestion($question, $data);
    //             break;

    //         case QuestionTypes::EDITING:
    //             $this->updateEditingQuestion($question, $data);
    //             break;
    //         default:
    //             return redirect()->route('admin.questions.index');
    //     }

    //     return redirect()->route('admin.questions.index');
    // }



    // public function updateEditingQuestion($question, array $data)
    // {

    //     $decodedMetadata = json_decode($data['metadata'], true);
    //     $fullMetadata = [
    //         'instruction' => $data['instruction'] ?? '',
    //         'education_type' => $data['education_type'],
    //         'level_id' => $data['level_id'],
    //         'subject_id' => $data['subject_id'],
    //         'type' => $data['type'],
    //         'paragraph' => $decodedMetadata['paragraph'] ?? null,
    //         'questions' => $decodedMetadata['questions'] ?? [],
    //     ];

    //     // dd($fullMetadata);
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->content = $data['content'] ?? '';
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $fullMetadata;
    //     $question->save();
    //     return redirect()->route('admin.questions.index');
    // }


    // public function updateComprehensionQuestion($question, array $data)
    // {
    //     $decodedMetadata = is_string($data['comprehension_metadata'])
    //         ? json_decode($data['comprehension_metadata'], true)
    //         : $data['comprehension_metadata'];

    //     $fullMetadata = [
    //         'education_type' => $data['education_type'],
    //         'level_id' => $data['level_id'],
    //         'subject_id' => $data['subject_id'],
    //         'type' => $data['type'],
    //         'instruction' => $data['instruction'] ?? '',
    //         'passage' => $decodedMetadata['passage'] ?? null,
    //         'subquestions' => $decodedMetadata['subquestions'] ?? [],
    //     ];

    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->content = $fullMetadata['passage'] ?? '';
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $fullMetadata;
    //     $question->save();

    //     return redirect()->route('admin.questions.index');
    // }

    // protected function updateClozeWithDropdownOptions($question, array $data)
    // {
    //     // Parse the editing_metadata JSON
    //     $decodedMetadata = json_decode($data['underline_metadata'], true);
    //     $fullMetadata = [
    //         'instruction' => $data['instruction'] ?? '',
    //         'education_type' => $data['education_type'],
    //         'level_id' => $data['level_id'],
    //         'subject_id' => $data['subject_id'],
    //         'type' => $data['type'],
    //         'paragraph' => $data['content'] ?? null,
    //         'questions' => $decodedMetadata['questions'] ?? [],
    //     ];

    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->content = $data['content'] ?? '';
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $fullMetadata;
    //     $question->save();
    //     return redirect()->route('admin.questions.index');
    // }


    // public function updateGrammarClozeWithOptions($question, array $data)
    // {
    //     $metadata = json_decode($data['metadata'], true);
    //     // dd($metadata);
    //     $metadata['instruction'] = $data['instruction'] ?? '';
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->content = $metadata['paragraph'] ?? '';
    //     $question->education_type = $data['education_type'] ?? null;
    //     $question->level_id = $data['level_id'] ?? null;
    //     $question->subject_id = $data['subject_id'] ?? null;
    //     $question->explanation = $metadata['explanation'] ?? null;
    //     $question->metadata = $metadata;
    //     $question->save();

    //     return redirect()->route('admin.questions.index');
    // }

    // public function updateRearrangingQuestion($question, array $data)
    // {
    //     $questionText = $data['question_text'];
    //     $orderedAnswer = preg_split('/\s+/', trim($questionText));
    //     $shuffled = $orderedAnswer;
    //     shuffle($shuffled);

    //     $options = collect($shuffled)->map(fn($word) => [
    //         'value' => $word,
    //         'is_correct' => false,
    //     ])->values()->toArray();

    //     $transformed = [
    //         'type' => 'rearranging',
    //         'content' => $data['content'] ?? '',
    //         'instruction' => $data['instruction'] ?? '',
    //         'options' => $options,
    //         'answer' => [
    //             'answer' => $orderedAnswer,
    //             'format' => 'ordered'
    //         ],
    //     ];
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->content = $data['content'];
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $transformed;
    //     $question->save();
    //     return redirect()->route('admin.questions.index');
    // }

    // public function updateMcqQuestion($question, array $data)
    // {
    //     $correctIndex = (int) $data['correct_option'];

    //     // Updated: handle options with explanation
    //     $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
    //         return [
    //             'value' => $option['value'],
    //             'explanation' => $option['explanation'] ?? null,
    //             'is_correct' => ($index === $correctIndex),
    //         ];
    //     }, $data['options'], array_keys($data['options']));

    //     $answer = [
    //         'answer' => $structuredOptions[$correctIndex]['value'] ?? null,
    //         'format' => 'text',
    //     ];

    //     $payload = $data;
    //     $payload['options'] = $structuredOptions;
    //     $payload['answer'] = $answer;
    //     $payload['instruction'] = $data['instruction'] ?? '';
    //     unset($payload['correct_option']);

    //     // Update question
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->content = $data['content'];
    //     $question->education_type = $data['education_type'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->level_id = $data['level_id'];
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $payload;
    //     $question->save();

    //     // Sync Question Options
    //     QuestionOption::where('question_id', $question->id)->delete();

    //     foreach ($structuredOptions as $index => $option) {
    //         QuestionOption::create([
    //             'question_id' => $question->id,
    //             'label' => chr(65 + $index), // A, B, C, D...
    //             'value' => $option['value'],
    //             'is_correct' => $option['is_correct'],
    //         ]);
    //     }

    //     return redirect()->route('admin.questions.index');
    // }


    // public function updateFillBlankQuestion($question, array $data)
    // {
    //     $data['instruction'] = $data['instruction'] ?? '';

    //     // Decode the JSON fill_in_the_blank_metadata to array
    //     $blanks = json_decode($data['fill_in_the_blank_metadata'], true);

    //     // Build the metadata array
    //     $metadata = [
    //         'instruction'   => $data['instruction'],
    //         'type'  => $data['type'],
    //         'question_text' => $data['content'],
    //         'blanks'        => $blanks ?? [],
    //     ];

    //     $question->topic_id       = $data['topic_id'];
    //     $question->type           = $data['type'];
    //     $question->content        = $data['content'];
    //     $question->explanation    = $data['explanation'] ?? null;
    //     $question->level_id       = $data['level_id'] ?? null;
    //     $question->subject_id     = $data['subject_id'] ?? null;
    //     $question->education_type = $data['education_type'] ?? null;
    //     $question->metadata       = $metadata; // auto-cast to JSON via $casts
    //     $question->save();
    // }

    // public function updateTrueFalseQuestion(Question $question, array $data)
    // {
    //     $transformed = [
    //         'type' => 'true_false',
    //         'content' => $data['content'],
    //         'options' => [
    //             ['value' => 'True'],
    //             ['value' => 'False'],
    //         ],
    //         'answer' => [
    //             'choice' => $data['true_false_answer'],
    //             'explanation' => $data['explanation'] ?? null,
    //             'format' => $data['format'] ?? 'text',
    //         ],
    //     ];

    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->content = $data['content'];
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $transformed;
    //     $question->save();
    // }

    // public function updateLinkingQuestion($question, array $data, Request $request)
    // {
    //     $answer = [];

    //     foreach ($data['options'] as $index => $option) {
    //         $leftImageUri = null;
    //         $rightImageUri = null;

    //         if (($option['label_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.label_image")) {
    //             $leftImage = $request->file("question_data.options.$index.label_image");
    //             $storedPath = $leftImage->store('uploads/linking', 'public');
    //             $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
    //         }

    //         if (($option['value_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.value_image")) {
    //             $rightImage = $request->file("question_data.options.$index.value_image");
    //             $storedPath = $rightImage->store('uploads/linking', 'public');
    //             $rightImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
    //         }

    //         $answer[] = [
    //             'left' => [
    //                 'word' => $option['label_text'] ?? '',
    //                 'image_uri' => $leftImageUri,
    //                 'match_type' => $option['label_type'] ?? 'text',
    //             ],
    //             'right' => [
    //                 'word' => $option['value_text'] ?? '',
    //                 'image_uri' => $rightImageUri,
    //                 'match_type' => $option['value_type'] ?? 'text',
    //             ],
    //         ];
    //     }

    //     $transformed = [
    //         'type' => 'linking',
    //         'content' => $data['content'] ?? '',
    //         'explanation' => $data['explanation'] ?? '',
    //         'instruction' => $data['instruction'] ?? '',
    //         'format' => 'mapping',
    //         'answer' => $answer,
    //     ];
    //     $question->topic_id = $data['topic_id'];
    //     $question->type = $data['type'];
    //     $question->education_type = $data['education_type'];
    //     $question->level_id = $data['level_id'];
    //     $question->subject_id = $data['subject_id'];
    //     $question->content = $data['content'];
    //     $question->explanation = $data['explanation'] ?? null;
    //     $question->metadata = $transformed;
    //     $question->save();

    //     return redirect()->route('admin.questions.index');
    // }
    public function update(Request $request, $question)
    {
        $question = Question::findOrFail($question);

        $data = $request->input('question_data');

        switch ($data['type']) {
            case QuestionTypes::MCQ:
                $this->updateMcqQuestion($question, $data);
                break;

            case QuestionTypes::FILL_IN_THE_BLANK:
                $this->updateFillBlankQuestion($question, $data);
                break;

            case QuestionTypes::TRUE_FALSE:
                $this->updateTrueFalseQuestion($question, $data);
                break;

            case QuestionTypes::LINKING:
                $this->updateLinkingQuestion($question, $data, $request);
                break;

            case QuestionTypes::REARRANGING:
                $this->updateRearrangingQuestion($question, $data);
                break;
            case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                $this->updateGrammarClozeWithOptions($question, $data);
                break;

            case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                $this->updateClozeWithDropdownOptions($question, $data);
                break;

            case QuestionTypes::COMPREHENSION:
                $this->updateComprehensionQuestion($question, $data);
                break;

            case QuestionTypes::EDITING:
                $this->updateEditingQuestion($question, $data);
                break;
            default:
                session()->flash('toastr', [
                    'type' => 'success',
                    'message' => 'Question updated successfully!',
                ]);
                return redirect()->route('admin.questions.index');
        }
        return redirect()->route('admin.questions.index');
    }

    public function updateEditingQuestion($question, array $data)
    {
        $decodedMetadata = json_decode($data['metadata'], true);
        $fullMetadata = [
            'instruction' => $data['instruction'] ?? '',
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'paragraph' => $decodedMetadata['paragraph'] ?? null,
            'questions' => $decodedMetadata['questions'] ?? [],
        ];

        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Editing question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateComprehensionQuestion($question, array $data)
    {
        $decodedMetadata = is_string($data['comprehension_metadata'])
            ? json_decode($data['comprehension_metadata'], true)
            : $data['comprehension_metadata'];

        $fullMetadata = [
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'instruction' => $data['instruction'] ?? '',
            'passage' => $decodedMetadata['passage'] ?? null,
            'subquestions' => $decodedMetadata['subquestions'] ?? [],
        ];

        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $fullMetadata['passage'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Comprehension type question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    protected function updateClozeWithDropdownOptions($question, array $data)
    {
        $decodedMetadata = json_decode($data['underline_metadata'], true);
        $fullMetadata = [
            'instruction' => $data['instruction'] ?? '',
            'education_type' => $data['education_type'],
            'level_id' => $data['level_id'],
            'subject_id' => $data['subject_id'],
            'type' => $data['type'],
            'paragraph' => $data['content'] ?? null,
            'questions' => $decodedMetadata['questions'] ?? [],
        ];

        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Cloze with dropdown options question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateGrammarClozeWithOptions($question, array $data)
    {
        $metadata = json_decode($data['metadata'], true);
        $metadata['instruction'] = $data['instruction'] ?? '';
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $metadata['paragraph'] ?? '';
        $question->education_type = $data['education_type'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->explanation = $metadata['explanation'] ?? null;
        $question->metadata = $metadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Grammar cloze with options question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateRearrangingQuestion($question, array $data)
    {
        $questionText = $data['question_text'];
        $orderedAnswer = preg_split('/\s+/', trim($questionText));
        $shuffled = $orderedAnswer;
        shuffle($shuffled);

        $options = collect($shuffled)->map(fn($word) => [
            'value' => $word,
            'is_correct' => false,
        ])->values()->toArray();

        $transformed = [
            'type' => 'rearranging',
            'content' => $data['content'] ?? '',
            'instruction' => $data['instruction'] ?? '',
            'options' => $options,
            'answer' => [
                'answer' => $orderedAnswer,
                'format' => 'ordered'
            ],
        ];
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Rearranging question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateMcqQuestion($question, array $data)
    {
        $correctIndex = (int) $data['correct_option'];

        $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
            return [
                'value' => $option['value'],
                'explanation' => $option['explanation'] ?? null,
                'is_correct' => ($index === $correctIndex),
            ];
        }, $data['options'], array_keys($data['options']));

        $answer = [
            'answer' => $structuredOptions[$correctIndex]['value'] ?? null,
            'format' => 'text',
        ];

        $payload = $data;
        $payload['options'] = $structuredOptions;
        $payload['answer'] = $answer;
        $payload['instruction'] = $data['instruction'] ?? '';
        unset($payload['correct_option']);

        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->education_type = $data['education_type'];
        $question->subject_id = $data['subject_id'];
        $question->level_id = $data['level_id'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $payload;
        $question->save();

        QuestionOption::where('question_id', $question->id)->delete();

        foreach ($structuredOptions as $index => $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'label' => chr(65 + $index), // A, B, C, D...
                'value' => $option['value'],
                'is_correct' => $option['is_correct'],
            ]);
        }

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'MCQ question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateFillBlankQuestion($question, array $data)
    {
        $data['instruction'] = $data['instruction'] ?? '';

        $blanks = json_decode($data['fill_in_the_blank_metadata'], true);

        $metadata = [
            'instruction' => $data['instruction'],
            'type' => $data['type'],
            'question_text' => $data['content'],
            'blanks' => $blanks ?? [],
        ];

        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->education_type = $data['education_type'] ?? null;
        $question->metadata = $metadata;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Fill in the blank question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateTrueFalseQuestion(Question $question, array $data)
    {
        $transformed = [
            'type' => 'true_false',
            'content' => $data['content'],
            'options' => [
                ['value' => 'True'],
                ['value' => 'False'],
            ],
            'answer' => [
                'choice' => $data['true_false_answer'],
                'explanation' => $data['explanation'] ?? null,
                'format' => $data['format'] ?? 'text',
            ],
        ];

        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'True/False question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }

    public function updateLinkingQuestion($question, array $data, Request $request)
    {
        $answer = [];

        foreach ($data['options'] as $index => $option) {
            $leftImageUri = null;
            $rightImageUri = null;

            if (($option['label_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.label_image")) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $storedPath = $leftImage->store('uploads/linking', 'public');
                $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            }

            if (($option['value_type'] ?? '') === 'image' && $request->hasFile("question_data.options.$index.value_image")) {
                $rightImage = $request->file("question_data.options.$index.value_image");
                $storedPath = $rightImage->store('uploads/linking', 'public');
                $rightImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            }

            $answer[] = [
                'left' => [
                    'word' => $option['label_text'] ?? '',
                    'image_uri' => $leftImageUri,
                    'match_type' => $option['label_type'] ?? 'text',
                ],
                'right' => [
                    'word' => $option['value_text'] ?? '',
                    'image_uri' => $rightImageUri,
                    'match_type' => $option['value_type'] ?? 'text',
                ],
            ];
        }

        $transformed = [
            'type' => 'linking',
            'content' => $data['content'] ?? '',
            'explanation' => $data['explanation'] ?? '',
            'instruction' => $data['instruction'] ?? '',
            'format' => 'mapping',
            'answer' => $answer,
        ];
        $question->topic_id = $data['topic_id'];
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        session()->flash('toastr', [
            'type' => 'success',
            'message' => 'Linking question updated successfully!',
        ]);

        return redirect()->route('admin.questions.index');
    }


    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $type = $question->type;

        // Handle MCQ and TRUE_FALSE: delete related options
        if (in_array($type, ['mcq', 'true_false'])) {
            QuestionOption::where('question_id', $question->id)->delete();
        }

        // Handle LINKING: delete associated images
        if ($type === 'linking') {
            $data = json_decode($question->data, true);
            if (isset($data['answer']) && is_array($data['answer'])) {
                foreach ($data['answer'] as $item) {
                    foreach (['left', 'right'] as $side) {
                        if (
                            isset($item[$side]['match_type'], $item[$side]['image_uri']) &&
                            $item[$side]['match_type'] === 'image'
                        ) {
                            $imageUrl = $item[$side]['image_uri'];
                            $path = str_replace(url('/storage'), 'public', $imageUrl);

                            if (Storage::exists($path)) {
                                Storage::delete($path);
                            }
                        }
                    }
                }
            }
        }
        $question->delete();
        return redirect()->route('admin.questions.index');
    }
}
