<?php

namespace Src\BlendedConcept\User\Presentation\HTTP;

use Illuminate\Http\Request;
use Src\Common\Infrastructure\Laravel\Controller;


use Inertia\Inertia;
use Src\BlendedConcept\User\Domain\Model\Student;

class StudentController extends Controller
{
  public function create()
  {
    return Inertia::render('BlendedConcept/User/Presentation/Resources/Students/Create');
  }

  public function edit(Student $student)
  {
    return Inertia::render('BlendedConcept/User/Presentation/Resources/Students/Edit', [
      'student' => $student
    ]);
  }

  public function store(Request $request)
  {
    return $request->all();
  }
  public function dashboard()
  {
    return Inertia::render('BlendedConcept/User/Presentation/Resources/Students/Dashboard');
  }
}
