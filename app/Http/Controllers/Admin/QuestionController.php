<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\QuestionOption;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with(['options', 'level', 'subject'])->get();

        return view('admin.question.index', compact('questions'));
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
            default:
                return response()->json(['error' => 'Invalid question type'], 400);
        }
        return redirect()->route('admin.questions.index')->with('success', 'Question created successfully!');
    }

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

        // Save each option
        foreach ($structuredOptions as $index => $option) {
            \App\Models\QuestionOption::create([
                'question_id' => $question->id,
                'label' => chr(65 + $index), // A, B, C...
                'value' => $option['value'],
                'is_correct' => $option['is_correct'],
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Fill in blanks type question created successfully!');
    }

    private function saveFillBlankQuestion($data)
    {
        $question = new Question();
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->level_id = $data['level_id'] ?? null;
        $question->subject_id = $data['subject_id'] ?? null;
        $question->education_type = $data['education_type'] ?? null;
        $question->metadata = $data;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Fill in the Blank type question created successfully!');
    }

    // Save True/False Question
    private function saveTrueFalseQuestion($data)
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

        $question = new Question();
        $question->education_type = $data['education_type'];
        $question->level_id = $data['level_id'];
        $question->subject_id = $data['subject_id'];
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return redirect()->route('admin.questions.index')
            ->with('success', 'True/False type question saved successfully!');
    }

    // Save Linking Question
    private function saveLinkingQuestion($data, $request)
    {
        $answer = [];

        foreach ($data['options'] as $index => $option) {
            $leftImageUri = null;
            $rightImageUri = null;

            // Upload left image if label_type is 'image' and file is uploaded
            if (($option['label_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.label_image")
            ) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $storedPath = $leftImage->store('uploads/linking', 'public');
                $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            }

            // Upload right image if value_type is 'image' and file is uploaded
            if (($option['value_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.value_image")
            ) {
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

        return redirect()->route('admin.questions.index')->with('success', 'Linking type question saved successfully!');
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
                return response()->json(['error' => 'Invalid question type'], 400);
        }

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully!');
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

            if (($option['match_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.label_image")
            ) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $storedPath = $leftImage->store('uploads/linking', 'public');
                $leftImageUri = $storedPath ? asset('storage/' . $storedPath) : null;
            } else if (isset($option['existing_label_image_uri'])) {
                $leftImageUri = $option['existing_label_image_uri'];
            }

            if (($option['value_type'] ?? '') === 'image' &&
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
}
