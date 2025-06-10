<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $questionTypes = QuestionTypes::TYPES;

        $tab = $request->query('tab', 'all');
        $search = $request->query('search');

        $questions = Question::with(['options', 'level', 'subject'])
            ->when($tab !== 'all', function ($query) use ($tab) {
                $query->where('type', $tab);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'ILIKE', "%$search%")
                        ->orWhere('type', 'ILIKE', "%$search%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString(); // Preserve tab/search in pagination links

        return view('admin.question.index', compact('questions', 'questionTypes'));
    }


    public function create()
    {
        // Load levels with subjects eager loaded
        $levels = \App\Models\QuestionLevel::with('subjects')->get()->groupBy('education_type');

        return view('admin.question.create', compact('levels'));
    }


    public function edit($id)
    {
        $question = Question::with(['options', 'level', 'subject'])->findOrFail($id);
        $levels = \App\Models\QuestionLevel::with('subjects')->get()->groupBy('education_type');

        return view('admin.question.edit', compact('question', 'levels'));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->input('question_data');

        switch ($data['type']) {
            case 'mcq':
                $this->saveMcqQuestion($data);
                break;

            case 'fill_blank':
                $this->saveFillBlankQuestion($data);
                break;

            case 'true_false':
                $this->saveTrueFalseQuestion($data);
                break;

            case 'linking':
                $this->saveLinkingQuestion($data, $request);
                break;

            case 'rearranging':
                $this->saveRearrangingQuestion($data);
                break;

            case 'comprehension':
                $this->saveComprehensionQuestion($data);
                break;

            case 'grammar_cloze_with_options':
                $this->saveGrammarClozeWithOptions($data);
                break;
            case 'editing':
                $this->saveEditingQuestion($data);
                break;
            case 'underlinecorrect':
                $this->saveUnderlineInpuQuestion($data);
                break;

            default:
                return response()->json(['error' => 'Invalid question type'], 400);
        }

        return redirect()->route('admin.questions.index')->with('message', 'Question created successfully!');
    }

    // 1. MCQ
    public function saveMcqQuestion(array $data)
    {
        $correctIndex = (int) $data['correct_option'];
        $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
            return [
                'value' => $option,
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

        $question = new \App\Models\Question();
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

        return redirect()->route('admin.questions.index')->with('success', 'MCQ question created successfully!');
    }

    // 2. Fill in the Blank
    private function saveFillBlankQuestion($data)
    {
        $data['instruction'] = $data['instruction'] ?? '';

        $question = new Question();
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->education_type = $data['education_type'] ?? null;
        $question->metadata = $data;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Fill in the Blank question saved successfully!');
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
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'True/False question saved successfully!');
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
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Linking question saved successfully!');
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
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Rearranging question saved successfully!');
    }

    // 6. Grammar Cloze With Options
    private function saveGrammarClozeWithOptions(array $data)
    {
        $metadata = json_decode($data['metadata'], true);
        $metadata['instruction'] = $data['instruction'] ?? '';
        $question = new Question();
        $question->type = $data['type'];
        $question->content = $metadata['paragraph'] ?? '';
        $question->education_type = $data['education_type'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->explanation = $metadata['explanation'] ?? null;
        $question->metadata = $metadata;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Grammar Cloze With Options question saved successfully!');
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
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $fullMetadata['passage'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Comprehension question saved successfully!');
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
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();
        return redirect()->route('admin.questions.index')->with('success', 'Editing spelling  question saved successfully!');
    }

    protected function saveUnderlineInpuQuestion($data)
    {
        // Parse the editing_metadata JSON
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

        $question = new Question();
        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'] ?? '';
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $fullMetadata;
        $question->save();
        return redirect()->route('admin.questions.index')->with('success', 'Underline correct  question saved successfully!');
    }


    // Update existing question
    public function update(Request $request, $question)
    {
        $question = Question::findOrFail($question);

        $data = $request->input('question_data');

        switch ($data['type']) {
            case 'mcq':
                $this->updateMcqQuestion($question, $data);
                break;

            case 'fill_blank':
                $this->updateFillBlankQuestion($question, $data);
                break;

            case 'true_false':
                $this->updateTrueFalseQuestion($question, $data);
                break;

            case 'linking':
                $this->updateLinkingQuestion($question, $data, $request);
                break;

            default:
                return redirect()->route('admin.questions.index')->with('message', 'Invalid question type!');
        }

        return redirect()->route('admin.questions.index')->with('message', 'Question updated successfully!');
    }
    public function updateMcqQuestion($question, array $data)
    {
        $correctIndex = (int) $data['correct_option']; // Cast to int

        $structuredOptions = array_map(function ($option, $index) use ($correctIndex) {
            return [
                'value' => $option,
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
        unset($payload['correct_option']);

        // Save to DB
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->education_type = $data['education_type'];
        $question->subject_id = $data['subject_id'];
        $question->level_id = $data['level_id'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $payload;
        $question->save();

        // Update options
        QuestionOption::where('question_id', $question->id)->delete();

        foreach ($structuredOptions as $index => $option) {
            QuestionOption::create([
                'question_id' => $question->id,
                'label' => chr(65 + $index),
                'value' => $option['value'],
                'is_correct' => $option['is_correct'],
            ]);
        }
    }

    public function updateFillBlankQuestion($question, array $data)
    {
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->education_type = $data['education_type'] ?? null;
        $question->metadata = $data;
        $question->save();
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
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
    }

    public function updateLinkingQuestion(Question $question, array $data, Request $request)
    {
        $answer = [];

        foreach ($data['options'] as $index => $option) {
            $leftImageUri = null;
            $rightImageUri = null;

            if (
                ($option['match_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.label_image")
            ) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $storedPath = $leftImage->store('uploads/linking', 'public');
                $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            } else if (isset($option['existing_label_image_uri'])) {
                $leftImageUri = $option['existing_label_image_uri'];
            }

            if (
                ($option['value_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.value_image")
            ) {
                $rightImage = $request->file("question_data.options.$index.value_image");
                $storedPath = $rightImage->store('uploads/linking', 'public');
                $rightImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            } else if (isset($option['existing_value_image_uri'])) {
                $rightImageUri = $option['existing_value_image_uri'];
            }

            $answer[] = [
                'left' => [
                    'word' => $option['label_text'] ?? '',
                    'image_uri' => ($option['match_type'] ?? 'text') === 'image' ? $leftImageUri : null,
                    'match_type' => $option['match_type'] ?? 'text',
                ],
                'right' => [
                    'word' => $option['value_text'] ?? '',
                    'image_uri' => ($option['value_type'] ?? 'text') === 'image' ? $rightImageUri : null,
                    'match_type' => $option['value_type'] ?? 'text',
                ],
            ];
        }

        $transformed = [
            'type' => 'linking',
            'content' => $data['content'] ?? '',
            'explanation' => $data['explanation'] ?? '',
            'format' => 'mapping',
            'answer' => $answer,
        ];

        $question->type = $data['type'];
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
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
        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully!');
    }
}
