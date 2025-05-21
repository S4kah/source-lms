<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateBuilder;
use App\Models\CertificateBuilderItem;
use App\Models\Course;
use App\Models\Exercise;
use App\Models\ExerciseResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $userId = user()->id;

        $correctCount = 0;

        $watchedLessonCount = \App\Models\WatchHistory::where([
            'user_id' => $userId,
            'course_id' => $course->id,
            'is_completed' => 1
        ])->count();

        $lessonCount = $course->lessons()->count();

        if ($watchedLessonCount != $lessonCount) {
            return abort(404);
        }

        $exercises = Exercise::where('course_id', $course->id)->get();

        foreach ($exercises as $exercise) {
            $exerciseResult = ExerciseResult::where([
                'user_id' => $userId,
                'exercise_id' => $exercise->id
            ])->first();

            if ($exerciseResult && $exerciseResult->is_correct) {
                $correctCount++;
            }
        }

        $grade = ($correctCount / $exercises->count()) * 100;

        $grade = number_format($grade, 2);

        $certificate = CertificateBuilder::first();
        $certificateItems = CertificateBuilderItem::all();

        $html = view('frontend.student-dashboard.enrolled-course.certificate', compact('certificate', 'certificateItems', 'grade'))->render();

        $html = str_replace("[student_name]", user()->name, $html);
        $html = str_replace("[course]", $course->title, $html);
        $html = str_replace("[date]", date('d-m-Y'), $html);
        $html = str_replace("[platform_name]", 'Edu Core', $html);
        $html = str_replace("[instructor_name]", $course->instructor->name, $html);
        $html = str_replace("[grade]", $grade, $html);

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('certificate.pdf');
    }
}
