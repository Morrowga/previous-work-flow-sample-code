<?php

use Illuminate\Support\Facades\Route;
use Src\BlendedConcept\Teacher\Presentation\HTTP\ConductLessonController;
use Src\BlendedConcept\Teacher\Presentation\HTTP\LearningActivityController;
use Src\BlendedConcept\Teacher\Presentation\HTTP\TeacherController;
use Src\BlendedConcept\Teacher\Presentation\HTTP\PlayListController;
use Src\BlendedConcept\Teacher\Presentation\HTTP\ViewStudentController;

Route::group(['middleware' => ['auth']], function () {

    Route::resource('teachers', TeacherController::class);

    Route::get("viewteacher", [TeacherController::class, 'viewteacher'])->name('viewteacher');
    Route::get("editteacher", [TeacherController::class, 'editteacher'])->name('editteacher');

    Route::get('playlists',[PlayListController::class,'index']);

    Route::get('createplaylists',[PlayListController::class,'create']);
    Route::get('showplaylists',[PlayListController::class,'show']);

    Route::get('/conduct_lessons', [ConductLessonController::class, 'index'])->name('conduct_lessons.index');
    Route::get('/conduct_lessons/show', [ConductLessonController::class, 'show'])->name('conduct_lessons.show');

    Route::get('/view_students', [ViewStudentController::class, 'index'])->name('view_students.index');
    Route::get('/view_students/show', [ViewStudentController::class, 'show'])->name('view_students.show');
    Route::get('/view_students/create', [ViewStudentController::class, 'create'])->name('view_students.create');
    Route::get('/view_students/edit', [ViewStudentController::class, 'edit'])->name('view_students.edit');

    Route::get('/learning_activities', [LearningActivityController::class, 'index'])->name('learning_activities.index');
});
