<?php

namespace App\Http\Controllers\Admin;

use App\Enum\QuestionTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\McqQuestionImport;
use App\Imports\FillBlankQuestionImport;
use App\Imports\TrueFalseQuestionImport;
use App\Imports\LinkingQuestionImport;
use App\Imports\RearrangingQuestionImport;
use App\Imports\ComprehensionQuestionImport;
use App\Imports\GrammarClozeOptionsImport;
use App\Imports\EditingQuestionImport;
use App\Imports\DropdownClozeQuestionImport;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionImportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'type' => 'required|string',
        ]);

        $type = $request->input('type'); // Get the 'type' from the request

        try {
            // Check if the type is one of the valid question types
            switch ($type) {
                case QuestionTypes::MCQ:
                    Excel::import(new McqQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'MCQ type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::FILL_IN_THE_BLANK:
                    Excel::import(new FillBlankQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Fill in the blank type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::TRUE_FALSE:
                    Excel::import(new TrueFalseQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'True/False type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::LINKING:
                    Excel::import(new LinkingQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Linking type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::REARRANGING:
                    Excel::import(new RearrangingQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Rearranging type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::COMPREHENSION:
                    Excel::import(new ComprehensionQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Comprehension type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                    Excel::import(new GrammarClozeOptionsImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Grammar cloze with options type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::EDITING:
                    Excel::import(new EditingQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Editing type questions imported successfully!',
                    ]);
                    break;

                case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                    Excel::import(new DropdownClozeQuestionImport, $request->file('file'));
                    session()->flash('toastr', [
                        'type' => 'success',
                        'message' => 'Grammar cloze with dropdown options type questions imported successfully!',
                    ]);
                    break;

                default:
                    return back()->with('error', 'Invalid question type selected. Please choose a valid question type.');
            }

            return back()->with('success', 'Questions imported successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadSample(Request $request)
    {
        $request->validate([
            'type' => 'required|string'
        ]);

        $type = $request->input('type');

        // Mapping of question types to sample file names
        $map = [
            QuestionTypes::MCQ => 'sampleMCQ.xlsx',
            QuestionTypes::FILL_IN_THE_BLANK => 'sampleFillInTheBlank.xlsx',
            QuestionTypes::TRUE_FALSE => 'sampleTrueFalse.xlsx',
            QuestionTypes::LINKING => 'sampleLinking.xlsx',
            QuestionTypes::REARRANGING => 'sampleRearrange.xlsx',
            QuestionTypes::COMPREHENSION => 'sampleComprehension.xlsx',
            QuestionTypes::OPEN_CLOZE_WITH_OPTIONS => 'sampleGrammarClozeWithOptions.xlsx',
            QuestionTypes::EDITING => 'sampleEditingMultipleMistakes.xlsx',
            QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS => 'sampleGrammarClozeWithDropdown.xlsx',
        ];

        // Check if the provided type exists in the map
        if (!array_key_exists($type, $map)) {
            return back()->with('error', 'Invalid type for sample download.');
        }

        // Define the file path to the sample file
        $filePath = public_path('samples/' . $map[$type]);

        // Check if the sample file exists
        if (!file_exists($filePath)) {
            return back()->with('error', 'Sample file not found.');
        }

        // Return the sample file for download
        session('toastr', [
            'type' => 'success',
            'message' => 'Sample file downloaded successfully!',
        ]);

        return response()->download($filePath);
    }
}
