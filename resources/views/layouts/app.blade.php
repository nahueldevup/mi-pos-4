<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS')</title>

    @livewireStyles
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f1419;
            color: #e1e8ed;
        }

        /* Sidebar */
        #sidebar {
            width: 240px;
            background: #1a1f29;
            color: #e1e8ed;
            position: fixed;
            left: -240px;
            top: 0;
            bottom: 0;
            transition: left 0.3s ease;
            padding-top: 1em;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        #sidebar.visible {
            left: 0;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 2em;
        }

        #sidebar li {
            margin-bottom: 0.5em;
        }

        #sidebar a {
            color: #8899a6;
            text-decoration: none;
            display: block;
            padding: 0.8em 1.5em;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        #sidebar a:hover {
            background: #252d3a;
            color: #1da1f2;
            border-left-color: #1da1f2;
        }

        /* Botón de menú */
        #menu-btn {
            margin: 1em;
            cursor: pointer;
            background: #1a1f29;
            color: #e1e8ed;
            border: 1px solid #2d3748;
            padding: 0.6em 1.2em;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        #menu-btn:hover {
            background: #252d3a;
            border-color: #1da1f2;
            color: #1da1f2;
        }

        /* Main content */
        main {
            margin-left: 0;
            padding: 2em;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        main.shifted {
            margin-left: 240px;
        }

        /* Modal - Livewire controla el display */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.75);
            align-items: center;
            justify-content: center;
            z-index: 2000;
            backdrop-filter: blur(4px);
            padding: 1em;
            overflow-y: auto;
        }

        /* Solo agregamos display: flex cuando Livewire muestre el modal */
        .modal[style*="display"] {
            display: flex !important;
        }

        .modal-content {
            background: #1a1f29;
            padding: 2em;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid #2d3748;
            margin: auto;
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .modal-content {
                padding: 1.5em;
                max-width: 95%;
            }

            #sidebar {
                width: 100%;
                left: -100%;
            }

            #sidebar.visible {
                left: 0;
            }

            main.shifted {
                margin-left: 0;
            }

            table {
                font-size: 0.9em;
            }

            th, td {
                padding: 0.7em 0.5em;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                padding: 1em;
            }

            .btn {
                padding: 0.5em 1em;
                font-size: 0.9em;
            }
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 1.5em;
            color: #8899a6;
            transition: color 0.0s ease;
        }

        .close:hover {
            color: #e74c3c;
        }

        /* Botones */
        .btn {
            padding: 0.6em 1.4em;
            margin: 0.25em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.0s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #1da1f2;
            color: white;
        }

        .btn-primary:hover {
            background: #1a8cd8;
            box-shadow: 0 4px 8px rgba(29, 161, 242, 0.3);
        }

        .btn-success {
            background: #17bf63;
            color: white;
        }

        .btn-success:hover {
            background: #14a855;
            box-shadow: 0 4px 8px rgba(23, 191, 99, 0.3);
        }

        .btn-danger {
            background: #e0245e;
            color: white;
        }

        .btn-danger:hover {
            background: #c41e4f;
            box-shadow: 0 4px 8px rgba(224, 36, 94, 0.3);
        }

        .btn-warning {
            background: #ffad1f;
            color: #0f1419;
        }

        .btn-warning:hover {
            background: #ff9800;
            box-shadow: 0 4px 8px rgba(255, 173, 31, 0.3);
        }
        .add-btn {
            position: fixed; /* Para posicionarlo respecto a la ventana */
            bottom: 20px; /* Ajusta la distancia desde la parte inferior */
            right: 20px; /* Ajusta la distancia desde la parte derecha */
            width: 60px; /* Ancho del botón */
            height: 60px; /* Altura del botón */
            background: #3498db; /* Color de fondo */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            border-radius: 50%; /* Hace el botón circular */
            font-size: 30px; /* Tamaño del símbolo */
            display: flex; /* Para centrar el contenido */
            align-items: center; /* Centrado vertical */
            justify-content: center; /* Centrado horizontal */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Sombra para efecto de profundidad */
        }

        add-btn:hover {
            background: #2980b9; /* Color más oscuro al pasar el mouse */
        }
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
            background: #1a1f29;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 1em;
            text-align: left;
            border-bottom: 1px solid #2d3748;
        }

        th {
            background: #252d3a;
            color: #1da1f2;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85em;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: #252d3a;
        }

        /* Formularios */
        label {
            display: block;
            margin: 0.8em 0 0.4em;
            color: #8899a6;
            font-weight: 500;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.7em;
            margin-bottom: 1em;
            border: 1px solid #2d3748;
            border-radius: 6px;
            background: #0f1419;
            color: #e1e8ed;
            transition: all 0.2s ease;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #1da1f2;
            box-shadow: 0 0 0 3px rgba(29, 161, 242, 0.1);
        }

        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #0f1419;
        }

        ::-webkit-scrollbar-thumb {
            background: #2d3748;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1da1f2;
        }
    </style>
</head>
<body>

<button id="menu-btn">☰ Menú</button>

<nav id="sidebar">
    <ul>
        <li><a href="{{ route('products.index') }}">📦 Productos</a></li>
        <li><a href="{{ route('categories.index') }}">🏷️ Categorías</a></li>
        <li><a href="#">🛒 Ventas</a></li>
        <li><a href="#">⚙️ Config</a></li>
    </ul>
</nav>

<main id="main">
    @yield('content')
</main>

<script>
    const menuBtn = document.getElementById('menu-btn');
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('visible');
        main.classList.toggle('shifted');
    });
</script>

@livewireScripts
</body>
</html>
