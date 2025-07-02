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
                    break;

                case QuestionTypes::FILL_IN_THE_BLANK:
                    Excel::import(new FillBlankQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::TRUE_FALSE:
                    Excel::import(new TrueFalseQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::LINKING:
                    Excel::import(new LinkingQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::REARRANGING:
                    Excel::import(new RearrangingQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::COMPREHENSION:
                    Excel::import(new ComprehensionQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::OPEN_CLOZE_WITH_OPTIONS:
                    Excel::import(new GrammarClozeOptionsImport, $request->file('file'));
                    break;

                case QuestionTypes::EDITING:
                    Excel::import(new EditingQuestionImport, $request->file('file'));
                    break;

                case QuestionTypes::OPEN_CLOZE_WITH_DROPDOWN_OPTIONS:
                    Excel::import(new DropdownClozeQuestionImport, $request->file('file'));
                    break;

                default:
                    return back()->with('error', 'Invalid question type selected. Please choose a valid question type.');
            }

            return back()->with('success', 'Questions imported successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
