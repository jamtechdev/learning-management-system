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
            $leftImageUri = '';
            $rightImageUri = '';

            // Upload left image
            if (($option['match_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.label_image")
            ) {
                $leftImage = $request->file("question_data.options.$index.label_image");
                $leftImageUri = $leftImage->store('uploads/linking', 'public');
            }

            // Upload right image
            if (($option['value_type'] ?? '') === 'image' &&
                $request->hasFile("question_data.options.$index.value_image")
            ) {
                $rightImage = $request->file("question_data.options.$index.value_image");
                $rightImageUri = $rightImage->store('uploads/linking', 'public');
            }

            $answer[] = [
                'left' => [
                    'word' => $option['label_text'] ?? '',
                    'image_uri' => asset('storage/' . $leftImageUri),
                    'match_type' => $option['match_type'] ?? 'text',
                ],
                'right' => [
                    'word' => $option['value_text'] ?? '',
                    'image_uri' => asset('storage/' . $rightImageUri),
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
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Linking type question saved successfully!');
    }









    // Save Spelling Correction Question
    private function saveSpellingCorrection($data)
    {
        $question = new Question();
        $question->type = 'spelling';
        $question->content = $data['content'];
        $question->metadata = json_encode($data['metadata']);
        $question->save();

        foreach ($data['metadata']['correction_targets'] as $incorrectWord) {
            $correction = new saveSpellingCorrection();
            $correction->question_id = $question->id;
            $correction->incorrect_word = $incorrectWord;
            // Assuming you have a logic to determine the correct word, e.g., a predefined list.
            $correction->correct_word = 'correct_word_here';  // Example
            $correction->save();
        }

        return response()->json(['success' => 'Spelling correction saved successfully!']);
    }

    // Save Rearrange Question
    private function saveRearrangeQuestion($data)
    {
        $question = new Question();
        $question->type = 'rearrange';
        $question->content = $data['content'];
        $question->answer = json_encode($data['answer']);
        $question->save();

        foreach ($data['options'] as $option) {
            $optionModel = new QuestionOption();
            $optionModel->question_id = $question->id;
            $optionModel->value = $option['value'];
            $optionModel->is_correct = $option['is_correct'];
            $optionModel->save();
        }

        return response()->json(['success' => 'Rearrange saved successfully!']);
    }


    // Save Image MCQ Question
    private function saveImageMcqQuestion($data)
    {
        $question = new Question();
        $question->type = 'image_mcq';
        $question->content = $data['content'];
        $question->answer = json_encode($data['answer']);
        $question->save();

        foreach ($data['options'] as $option) {
            $optionModel = new QuestionOption();
            $optionModel->question_id = $question->id;
            $optionModel->value = $option['value'];
            $optionModel->is_correct = $option['is_correct'];
            $optionModel->save();
        }

        return response()->json(['success' => 'Image MCQ saved successfully!']);
    }

    // Save Math Question
    private function saveMathQuestion($data)
    {
        $question = new Question();
        $question->type = 'math';
        $question->content = $data['content'];
        $question->answer = json_encode($data['answer']);
        $question->save();

        return response()->json(['success' => 'Math question saved successfully!']);
    }

    // Save Grouped Question
    private function saveGroupedQuestion($data)
    {
        $group = new QuestionGroup();
        $group->title = $data['title'];
        $group->passage = $data['passage'] ?? null;
        $group->save();

        foreach ($data['sub_questions'] as $subQuestion) {
            $question = new Question();
            $question->type = $subQuestion['type'];
            $question->content = $subQuestion['content'];
            $question->answer = json_encode($subQuestion['answer']);
            $question->group_id = $group->id;
            $question->save();
        }

        return response()->json(['success' => 'Grouped question saved successfully!']);
    }

    // Save Comprehension Question
    private function saveComprehensionQuestion($data)
    {
        $group = new QuestionGroup();
        $group->title = $data['title'];
        $group->passage = $data['passage'];
        $group->save();

        foreach ($data['sub_questions'] as $subQuestion) {
            $question = new Question();
            $question->type = $subQuestion['type'];
            $question->content = $subQuestion['content'];
            $question->answer = json_encode($subQuestion['answer']);
            $question->group_id = $group->id;
            $question->save();
        }

        return response()->json(['success' => 'Comprehension question saved successfully!']);
    }
}
