<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $pageTitle ?? 'AnthroConnect - Onboarding' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif-heading { font-family: 'Lora', serif; }
    </style>
</head>
<body class="bg-[#f8f7f6] text-slate-900 antialiased">
    {{ $slot }}

    @livewireScripts
</body>
</html>
