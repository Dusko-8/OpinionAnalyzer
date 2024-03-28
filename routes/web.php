<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\RawOpinionsController;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\VizualizaciaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

#Route::get('/', function () {
#    return view('welcome');
#});

Route::get('/', function () {
    // Check if user is logged in
    if (Auth::check()) {
        // If logged in, redirect to /navigation
        return redirect('/navigation');
    } else {
        // If not logged in, redirect to /login
        return redirect('/login');
    }
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/scrape-reddit', [HomeController::class, 'scrapeReddit'])->name('scrape.reddit');
Route::get('/scrape-facebook', [HomeController::class, 'scrapeFacebook'])->name('scrape.facebook');
Route::get('/check-post-existence', [HomeController::class, 'checkPostExists'])->name('check-post-existence');
Route::get('/navigation', [NavigationController::class, 'index'])->name('navigation');
Route::get('/rawopinions', [RawOpinionsController::class, 'index'])->name('rawopinions');
Route::get('/getComments', [RawOpinionsController::class, 'getComments'])->name('getComments');
Route::delete('/deleteComment', [RawOpinionsController::class, 'deleteComment']);
Route::get('/vizualizacia', [VizualizaciaController::class, 'index'])->name('vizualizacia');
Route::get('/analyze', [AnalyzeController::class, 'index'])->name('analyze');
Route::get('/show', [VizualizaciaController::class, 'show'])->name('show');
Route::post('/save-topics', [AnalyzeController::class, 'saveTopics']);
Route::post('/Sugest-Subtopics', [AnalyzeController::class, 'SugestSubtopics']);
Route::post('/getCommentsFilteredByTopics', [AnalyzeController::class, 'getCommentsFilteredByTopics']);
