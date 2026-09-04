<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\ViewModels\Admin\ProjectBoardViewModel;
use Illuminate\Contracts\View\View;

final class ProjectController extends Controller
{
    public function __invoke(ProjectBoardViewModel $viewModel): View
    {
        return view('pages.admin.projects.index', $viewModel->data());
    }
}
