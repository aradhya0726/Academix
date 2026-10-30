<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\FacultyController;
use Illuminate\Support\Facades\Route;

// Faculty
Route::get('/faculty/list', [FacultyController::class, 'list'])->name('faculty.list');
Route::get('/faculty/add', [FacultyController::class, 'add'])->name('faculty.add');
Route::post('/faculty/store', [FacultyController::class, 'store'])->name('faculty.store');
Route::get('/faculty/edit/{id}', [FacultyController::class, 'edit'])->name('faculty.edit');
Route::put('/faculty/update/{id}', [FacultyController::class, 'update'])->name('faculty.update');
Route::delete('/faculty/delete/{id}', [FacultyController::class, 'delete'])->name('faculty.delete');

// Class
Route::get('/class/list', [ClassController::class, 'list'])->name('class.list');
Route::get('/class/add', [ClassController::class, 'add'])->name('class.add');
Route::post('/class/store', [ClassController::class, 'store'])->name('class.store');
Route::get('/class/edit/{id}', [ClassController::class, 'edit'])->name('class.edit');
Route::put('/class/update/{id}', [ClassController::class, 'update'])->name('class.update');
Route::delete('/class/delete/{id}', [ClassController::class, 'delete'])->name('class.delete');
