<?php

Route::get('cybersource/payment/form', function (){
	return view('cybersource.secure.form');
})->name('secure.payment.form');

Route::post('cybersource/payment/confirm', function (){
	$data = request()->all();
	return view('cybersource.secure.confirm', compact('data'));
})->name('secure.payment.confirm');

Route::post('cybersource/payment/response', function (){
	return request()->all();
})->name('cybersource.payment.response');

Route::post('cybersource/payment/cancel', function (){
	return request()->all();
})->name('cybersource.payment.cancel');