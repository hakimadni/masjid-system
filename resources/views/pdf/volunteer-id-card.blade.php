<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card Relawan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .grid { width: 100%; }
        .card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            width: 46%;
            display: inline-block;
            margin: 1%;
            padding: 12px;
            vertical-align: top;
        }
        .title { font-size: 14px; font-weight: bold; margin-bottom: 8px; }
        .text { font-size: 12px; margin: 2px 0; }
    </style>
</head>
<body>
<div class="grid">
    @foreach($volunteers as $volunteer)
        <div class="card">
            <div class="title">ID Relawan Qurban</div>
            <div class="text"><strong>Nama:</strong> {{ $volunteer->name }}</div>
            <div class="text"><strong>Peran:</strong> {{ strtoupper($volunteer->role_type) }}</div>
            <div class="text"><strong>ID:</strong> {{ $volunteer->id }}</div>
            <div style="margin-top: 8px;">
                {!! QrCode::size(90)->generate($volunteer->qr_token) !!}
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
