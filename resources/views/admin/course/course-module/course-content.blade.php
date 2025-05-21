@extends('admin.course.course-module.course-app')

@section('tab_content')
    <div class="tab-pane fade show active" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
        <form action="" class="course-form more_info_form">
            @csrf
            <input type="hidden" name="id" value="{{ request()?->id }}">
            <input type="hidden" name="current_step" value="3">
            <input type="hidden" name="next_step" value="4">
        </form>
        <div class="add_course_content">
            <div class="flex-wrap mt-3 add_course_content_btn_area d-flex justify-content-between">
                <a class="common_btn dynamic-modal-btn btn btn-primary" href="#" data-id="{{ $courseId }}"> Add
                    New Chapter</a>
                <a class="common_btn sort_chapter_btn btn btn-primary" data-id="{{ $courseId }}"
                    href="javascript:;">Short Chapter</a>
            </div>
            <div class="accordion" id="accordionExample">
                @foreach ($chapters as $chapter)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ $chapter->id }}" aria-expanded="true"
                                aria-controls="collapse-{{ $chapter->id }}">
                                <span>{{ $chapter->title }}</span>
                            </button>
                            <div class="add_course_content_action_btn">
                                <div class="dropdown">
                                    <div class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-plus"></i>
                                    </div>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li class="add_lesson" data-chapter-id="{{ $chapter->id }}"
                                            data-course-id="{{ $chapter->course_id }}"><a class="dropdown-item"
                                                href="javascript:;">
                                                Add Lesson</a>
                                        </li>
                                    </ul>
                                </div>
                                <a class="edit edit_chapter" data-course-id="{{ $chapter->course_id }}"
                                    data-chapter-id="{{ $chapter->id }}" href="#"><i class="ti ti-edit"></i></a>
                                <a class="del delete-item"
                                    href="{{ route('admin.course-content.destory-chapter', $chapter->id) }}"><i
                                        class="ti ti-trash-x"></i></a>
                            </div>
                        </h2>
                        <div id="collapse-{{ $chapter->id }}" class="accordion-collapse collapse"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <ul class="item_list sortable_list">
                                    @foreach ($chapter->lessons ?? [] as $lesson)
                                        <li class="" data-lesson-id="{{ $lesson->id }}"
                                            data-chapter-id="{{ $chapter->id }}">
                                            <span>{{ $lesson->title }}</span>
                                            <div class="add_course_content_action_btn">
                                                <a class="edit_lesson" data-lesson-id="{{ $lesson->id }}"
                                                    data-chapter-id="{{ $chapter->id }}"
                                                    data-course-id="{{ $chapter->course_id }}" class="edit"
                                                    href="javascript:;"><i class="ti ti-edit"></i></a>
                                                <a class="del delete-item"
                                                    href="{{ route('admin.course-content.destroy-lesson', $lesson->id) }}"><i
                                                        class="ti ti-trash-x"></i></a>
                                                <a class="arrow dragger" href="javascript:;"><i
                                                        class="ti ti-arrows-maximize"></i></a>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>

                            </div>
                        </div>
                    </div>
                @endforeach

                <h1 class="pt-3">Exercise</h1>
                @if ($exercises->count())
                    @foreach ($exercises as $exercise)
                        <form method="POST" action="{{ route('admin.course-content.update-exercise', $exercise->id) }}"
                            class="p-3 mb-3 border rounded bg-light">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="course_id" value="{{ $courseId }}">

                            <div class="mb-2">
                                <label><strong>Question</strong></label>
                                <input type="text" name="question" class="form-control"
                                    value="{{ $exercise->question }}" required>
                            </div>

                            <div class="row">
                                @foreach (['a', 'b', 'c', 'd'] as $letter)
                                    <div class="mb-2 col-md-6">
                                        <label>Answer {{ strtoupper($letter) }}</label>
                                        <input type="text" name="answer_{{ $letter }}" class="form-control"
                                            value="{{ $exercise->{'answer_' . $letter} }}" required>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-2">
                                <label><strong>Correct Answer</strong></label>
                                <select name="correct_answer" class="form-select" required>
                                    <option value="">-- Select Correct Answer --</option>
                                    @foreach (['a', 'b', 'c', 'd'] as $option)
                                        <option value="{{ $option }}"
                                            {{ $exercise->correct_answer === $option ? 'selected' : '' }}>
                                            {{ strtoupper($option) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="gap-2 pt-2 d-flex">
                                <button type="submit" class="btn btn-primary btn-md">Save</button>
                                <a href="{{ route('admin.course-content.destroy-exercise', $exercise->id) }}"
                                    class="btn btn-danger btn-md delete-item">Delete</a>
                            </div>
                        </form>
                    @endforeach
                @endif

                <form action="{{ route('admin.course-content.store-exercise') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $courseId }}">

                    <div id="exercise_container">
                        <!-- Questions will be appended here -->
                    </div>


                    <div class="mt-4 justify-content-between d-flex">
                        <button type="button" class="mt-3 btn btn-primary" id="add_question_btn">+ Add
                            Question</button>
                        <button type="submit" class="gap-2 btn btn-primary d-flex">Save Exercises <i class="ti ti-circle-check"></i>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let questionIndex = 0;

        document.getElementById('add_question_btn').addEventListener('click', function() {
            const container = document.getElementById('exercise_container');

            const html = `
        <div class="p-3 mb-3 border rounded question-block" data-index="${questionIndex}">
            <div class="mb-2">
                <label><strong>Question</strong></label>
                <input type="text" name="questions[${questionIndex}][question]" class="form-control" required>
                <x-input-error :messages="$errors->get('questions.${questionIndex}.question')" class="mt-2" />
            </div>
            <div class="row">
                ${['a','b','c','d'].map(letter => `
                                                                <div class="mb-2 col-md-6">
                                                                    <label>Answer ${letter.toUpperCase()}</label>
                                                                    <input type="text" name="questions[${questionIndex}][answers][${letter}]" class="form-control" required>
                                                                    <x-input-error :messages="$errors->get('questions.${questionIndex}.answers.${letter}')" class="mt-2" />
                                                                </div>
                                                            `).join('')}
            </div>
            <div class="mb-2">
                <label>Correct Answer</label>
                <select name="questions[${questionIndex}][correct]" class="form-select" required>
                    <option value="">-- Select Correct Answer --</option>
                    <option value="a">A</option>
                    <option value="b">B</option>
                    <option value="c">C</option>
                    <option value="d">D</option>
                </select>
                <x-input-error :messages="$errors->get('questions.${questionIndex}.correct')" class="mt-2" />
            </div>
            <button type="button" class="gap-1 mt-3 d-flex btn btn-danger remove-question">Remove <i class="ti ti-xbox-x"></i></button>
        </div>`;

            container.insertAdjacentHTML('beforeend', html);
            questionIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-question')) {
                e.target.closest('.question-block').remove();
            }
        });
    </script>
@endpush
