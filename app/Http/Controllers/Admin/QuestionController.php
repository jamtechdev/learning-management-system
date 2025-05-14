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
        $questions = Question::all();
        return view('admin.question.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.question.create');
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
                $this->saveLinkingQuestion($data);
                break;

            case 'spelling':
                dd($this->saveSpellingCorrection($data));
                break;

            case 'rearrange':
                dd($this->saveRearrangeQuestion($data));
                break;
            case 'image_mcq':
                dd($this->saveImageMcqQuestion($data));
                break;

            case 'math':
                dd($this->saveMathQuestion($data));
                break;

            case 'grouped':
                dd($this->saveGroupedQuestion($data));
                break;

            case 'comprehension':
                dd($this->saveComprehensionQuestion($data));  // Only dump the data for Comprehension
                break;

            default:
                return response()->json(['error' => 'Invalid question type'], 400);
        }
        return redirect()->route('admin.questions.index')->with('success', 'Question created successfully!');
    }

    private function saveMcqQuestion(array $data)
    {
        // Prepare the question data
        $questionData = [
            'type' => $data['type'],
            'content' => $data['content'],
            'explanation' => $data['explanation'] ?? null,
        ];

        // Prepare options
        $options = array_map(function ($option) {
            if (!isset($option['is_correct'])) {
                $option['is_correct'] = false;
            } elseif ($option['is_correct'] == "1") {
                $option['is_correct'] = true;
            }
            return $option;
        }, $data['options']);

        // Set options and answer in metadata
        $questionData['options'] = $options;

        $correctAnswer = collect($options)->firstWhere('is_correct', true);
        if ($correctAnswer) {
            $questionData['answer'] = [
                'answer' => $correctAnswer['value'],
                'format' => 'text',
            ];
        }
        // Save question
        $question = new Question();
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $questionData;
        $question->save();

        // Save options in the question_options table
        foreach ($options as $index => $option) {
            \App\Models\QuestionOption::create([
                'question_id' => $question->id,
                'label' => chr(65 + $index), // A, B, C...
                'value' => $option['value'],
                'is_correct' => $option['is_correct'],
            ]);
        }

        return $questionData;
    }

    private function saveFillBlankQuestion($data)
    {

        $question = new Question();
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $data;
        $question->save();

        return redirect()->route('admin.questions.index')->with('success', 'Fill in blanks type question created successfully!');
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
                'choice' => $data['answer']['choice'] ?? null,
                'explanation' => $data['explanation'] ?? null,
                'format' => $data['format'] ?? 'text',
            ],
        ];
        $question = new Question();
        $question->type = $data['type'];
        $question->content = $data['content'];
        $question->explanation = $data['explanation'] ?? null;
        $question->metadata = $transformed;
        $question->save();
        return redirect()->route('admin.questions.index')->with('success', 'True/False type question saved successfully!');
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

    // Save Linking Question
    private function saveLinkingQuestion($data)
    {
        $question = new Question();
        $question->type = 'linking';
        $question->content = $data['content'];
        $question->answer = json_encode($data['answer']);
        $question->save();

        foreach ($data['options'] as $option) {
            $optionModel = new QuestionOption();
            $optionModel->question_id = $question->id;
            $optionModel->value = $option['value'];
            $optionModel->label = $option['label'];
            $optionModel->save();
        }

        return response()->json(['success' => 'Linking saved successfully!']);
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
