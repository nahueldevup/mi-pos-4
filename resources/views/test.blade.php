<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Tailwind</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-lg max-w-md">
    <h1 class="text-3xl font-bold text-blue-600 mb-4">
        ¡Tailwind Funciona! 🎉
    </h1>

    <p class="text-gray-700 mb-4">
        Si ves este texto con estilos bonitos, Tailwind está funcionando correctamente.
    </p>

    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
        Botón de prueba
    </button>
</div>

</body>
</html>
