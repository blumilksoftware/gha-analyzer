<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.ts')
    @inertiaHead
</head>
<body class="h-full bg-gradient-to-r from-white to-blue-400">
    <div class="h-full container py-4 mx-auto">
        @inertia
    </div>
</body>
</html>
