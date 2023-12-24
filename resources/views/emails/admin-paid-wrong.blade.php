@extends('emails.template')

@section('title')
    Platba přijata {{$paymentRecord['title']}}
@endsection

@section('content')
    <div>
        Zdravím, <br>
        <b>{{ $payment['name'] }}</b> ti poslal špatnou částku k platbě <b>{{ $payment['title'] }}</b>.<br>
        Měl ti poslat <b>{{ $payment['amount'] }} CZK</b>, ale poslal ti <b>{{ $payment['realamount'] }} CZK</b>.<br>
        Zkus ho kontaktovat a domluvit se na řešení problému. <br>
    </div>
@endsection
