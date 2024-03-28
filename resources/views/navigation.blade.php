@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/scrapers.css">
    <!-- Other head elements like title, scripts, etc. -->
</head>

<div class="container navigation-container">
    <h1 class="navigation-title">Navigácia</h1>
    <div class="navigation-buttons">
        <a href="/home" class="btn navigation-btn">Získať názory na témy</a>
        <a href="/rawopinions" class="btn navigation-btn">Spravovať názory</a>
        <a href="/analyze" class="btn navigation-btn">Analyzovať názory</a>
        <a href="/vizualizacia" class="btn navigation-btn">Vizualizácia názorov</a>
    </div>
</div>

@endsection