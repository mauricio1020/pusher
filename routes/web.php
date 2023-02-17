<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->prefix('pusher')->group(function () {
  dd('sdsd');
    Route::post('posts/{id}', function($id, \Illuminate\Http\Request $request){
        $comment = new \App\Models\Comment([
            'comment'  => $request->input('comment'),
            'user_id'  => auth()->user()->id,
            'post_id'  => $id
        ]);
        $comment->save();
        broadcast(new \App\Events\FireComment($comment))->toOthers()->name('comments.create');
    });

    Route::get('posts/{id}', function ($id){
        $post = \App\Models\Post::findOrFail($id);
        return view('chat', compact('post'));
    });

    Route::get('comments/{id}', function ($id){
        $comments = \App\Models\Comment::where('post_id', $id)->with('user')->get();
        return response()->json($comments)->name('commments.list');
    });

});
require __DIR__.'/auth.php';
