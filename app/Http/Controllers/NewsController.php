<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request, News $news): Response
    {
        $params = $request->validate([
            'search' => ['nullable', 'string'],
            'page' => ['nullable', 'numeric', 'min:1'],
        ]);

        $data = $news->handle($params['search'] ?? null, (int) ($params['page'] ?? 1));

        return Inertia::render('News', [
            'canLogin' => fn () => Route::has('login'),
            'canRegister' => fn () => Route::has('register'),
            'articles' => $data['news'],
        ]);
    }
}
