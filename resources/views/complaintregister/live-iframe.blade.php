<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Screen Component</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body, html { height: 100%; margin: 0; padding: 0; background-color: #f0f2f5; overflow: hidden; }
        .whatsapp-container { height: 100vh !important; margin: 0 !important; border: none !important; border-radius: 0 !important; }
    </style>
    @pwaHead
    {!! str_replace('console.error("Service workers are not supported.");', '', \Illuminate\Support\Facades\Blade::render('@laravelPwa')) !!}
</head>
<body>
    @include('complaintregister.live-partial')

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
