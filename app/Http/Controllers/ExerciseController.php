<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseResult;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.answers' => 'required|array',
            'questions.*.answers.a' => 'required|string',
            'questions.*.answers.b' => 'required|string',
            'questions.*.answers.c' => 'required|string',
            'questions.*.answers.d' => 'required|string',
            'questions.*.correct' => 'required|string',
        ]);

        try {
            foreach ($request->questions as $questionData) {
                Exercise::create([
                    'course_id' => $request->course_id,
                    'question' => $questionData['question'],
                    'answer_a' => $questionData['answers']['a'],
                    'answer_b' => $questionData['answers']['b'],
                    'answer_c' => $questionData['answers']['c'],
                    'answer_d' => $questionData['answers']['d'],
                    'correct_answer' => $questionData['correct'],
                ]);
            }

            notyf()->success('Created Successfully!');

            return redirect()->back();
        } catch (\Throwable $th) {
            notyf()->error($th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function submitExercise(Request $request)
    {
        $answers = $request->input('answers');
        $userId = auth()->user()->id;

        $correctCount = 0;

        foreach ($answers as $exerciseId => $selectedAnswer) {
            $exercise = Exercise::find($exerciseId);
            if (!$exercise) {
                continue;
            }

            $isCorrect = $exercise->correct_answer === $selectedAnswer;
            if ($isCorrect) {
                $correctCount++;
            }

            ExerciseResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'exercise_id' => $exerciseId
                ],
                [
                    'selected_answer' => $selectedAnswer,
                    'is_correct' => $isCorrect,
                    'score' => $isCorrect ? 1 : 0
                ]
            );
        }

        $total = count($answers);
        $grade = $correctCount * 10;

        notyf()->success("Exercise submitted. Your grade is $grade");

        return redirect()->back()->with([
            'exercise_grade' => $grade,
            'correct' => $correctCount,
            'total' => $total,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'question' => 'required|string',
            'answer_a' => 'required|string',
            'answer_b' => 'required|string',
            'answer_c' => 'required|string',
            'answer_d' => 'required|string',
            'correct_answer' => 'required|string',
        ]);


        try {
            Exercise::where('id', $id)->update([
                'course_id' => $request->course_id,
                'question' => $request->question,
                'answer_a' => $request->answer_a,
                'answer_b' => $request->answer_b,
                'answer_c' => $request->answer_c,
                'answer_d' => $request->answer_d,
                'correct_answer' => $request->correct_answer,
            ]);

            notyf()->success('Updated Successfully!');

            return redirect()->back();
        } catch (\Throwable $th) {
            notyf()->error($th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Exercise::where('id', $id)->delete();

            notyf()->success('Deleted Successfully!');

            return redirect()->back();
        } catch (\Throwable $th) {
            notyf()->error($th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
