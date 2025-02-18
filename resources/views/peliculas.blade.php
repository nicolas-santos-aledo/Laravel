<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas y Géneros</title>
    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-color: #a8d0e6; /* Azul hielo */
    color: #333;
    text-align: center;
    padding: 20px;
}

h1, h2 {
    color: #374785; /* Azul oscuro */
}

.item {
    background-color: #374785; /* Azul oscuro */
    color: #ffffff; /* Texto blanco */
    margin: 10px auto;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
    max-width: 400px;
}

.item h3 {
    margin: 0;
    font-size: 1.2em;
}

button {
    background-color: #f76c6c; /* Rojo coral */
    color: white;
    border: none;
    padding: 8px 12px;
    margin: 5px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s ease;
}

button:hover {
    background-color: #ff4f4f; /* Rojo más intenso */
    transform: scale(1.05);
}

form {
    background-color: #ffffff;
    padding: 15px;
    margin: 10px auto;
    border-radius: 8px;
    box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
    max-width: 400px;
}

input, select {
    width: 90%;
    padding: 8px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    display: block;
    text-align: center;
}

button[type="submit"] {
    background-color: #009688; /* Verde azulado */
}

button[type="submit"]:hover {
    background-color: #00796b; /* Verde más oscuro */
}
    </style>
</head>
<body>
    <h1>Gestión de Películas y Géneros</h1>
    
    <h2>Géneros</h2>
    <div id="generos"></div>
    <h2>Películas</h2>
    <div id="peliculas"></div>
    
    <h2>Agregar Nuevo Género</h2>
    <form id="generoForm">
        <input type="text" id="descripcionGenero" placeholder="Descripción" required>
        <button type="submit">Agregar Género</button>
    </form>

    <h2>Actualizar Género</h2>
    <form id="generoUpdateForm" style="display:none;">
        <input type="hidden" id="updateGeneroId">
        <input type="text" id="updateDescripcionGenero" placeholder="Nueva Descripción" required>
        <button type="submit">Actualizar Género</button>
    </form>
    
    <h2>Agregar Nueva Película</h2>
    <form id="peliculaForm">
        <input type="text" id="nombrePelicula" placeholder="Nombre" required>
        <input type="number" id="precioPelicula" placeholder="Precio" required>
        <select id="generoPelicula" required>
            <option value="">Seleccione un Género</option>
        </select>
        <button type="submit">Agregar Película</button>
    </form>

    <h2>Actualizar Película</h2>
    <form id="peliculaUpdateForm" style="display:none;">
        <input type="hidden" id="updatePeliculaId">
        <input type="text" id="updateNombrePelicula" placeholder="Nuevo Nombre" required>
        <input type="number" id="updatePrecioPelicula" placeholder="Nuevo Precio" required>
        <select id="updateGeneroPelicula" required></select>
        <button type="submit">Actualizar Película</button>
    </form>

    <script>
        let generos = {};

        fetch('/api/generos')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('generos');
                const generoSelect = document.getElementById('generoPelicula');
                data.forEach(genero => {
                    generos[genero.id] = genero.descripcion;
                    container.innerHTML += `
                        <div class="item" data-id="${genero.id}">
                            <h3>${genero.descripcion}</h3>
                            <button onclick="editGenero(${genero.id})">Editar</button>
                            <button onclick="deleteGenero(${genero.id})">Eliminar</button>
                        </div>`;
                    generoSelect.innerHTML += `<option value="${genero.id}">${genero.descripcion}</option>`;
                });
                cargarPeliculas();
            });

        function cargarPeliculas() {
            fetch('/api/peliculas')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('peliculas');
                    container.innerHTML = '';
                    data.forEach(pelicula => {
                        const generoDescripcion = generos[pelicula.codigogenero] || 'Desconocido';
                        container.innerHTML += `
                            <div class="item" data-id="${pelicula.id}">
                                <h3>${pelicula.nombre} - $${pelicula.precio} (${generoDescripcion})</h3>
                                <button onclick="editPelicula(${pelicula.id})">Editar</button>
                                <button onclick="deletePelicula(${pelicula.id})">Eliminar</button>
                            </div>`;
                    });
                });
        }
      document.getElementById('generoForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const descripcion = document.getElementById('descripcionGenero').value;

        fetch('/api/generos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ descripcion })
        })
        .then(response => response.json())
        .then(data => {
            // Agregar el nuevo género a la lista sin recargar la página
            const container = document.getElementById('generos');
            const generoSelect = document.getElementById('generoPelicula');
            
            generos[data.id] = data.descripcion;
            
            container.innerHTML += `
                <div class="item" data-id="${data.id}">
                    <h3>${data.descripcion}</h3>
                    <button onclick="editGenero(${data.id})">Editar</button>
                    <button onclick="deleteGenero(${data.id})">Eliminar</button>
                </div>`;

            generoSelect.innerHTML += `<option value="${data.id}">${data.descripcion}</option>`;

            document.getElementById('descripcionGenero').value = ''; // Limpiar input
        })
        .catch(error => console.error('Error al agregar género:', error));
    });

        function deleteGenero(id) {
            fetch(`/api/generos/${id}`, { method: 'DELETE' })
                .then(() => {
                    document.querySelector(`.item[data-id="${id}"]`).remove();
                    delete generos[id];
                    cargarPeliculas();
                });
        }
      document.getElementById('peliculaForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const nombre = document.getElementById('nombrePelicula').value;
        const precio = document.getElementById('precioPelicula').value;
        const codigogenero = document.getElementById('generoPelicula').value;

        fetch('/api/peliculas', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, precio, codigogenero })
        })
        .then(response => response.json())
        .then(data => {
            // Agregar la nueva película a la lista sin recargar la página
            const container = document.getElementById('peliculas');
            const generoDescripcion = generos[data.codigogenero] || 'Desconocido';

            container.innerHTML += `
                <div class="item" data-id="${data.id}">
                    <h3>${data.nombre} - $${data.precio} (${generoDescripcion})</h3>
                    <button onclick="editPelicula(${data.id})">Editar</button>
                    <button onclick="deletePelicula(${data.id})">Eliminar</button>
                </div>`;

            // Limpiar el formulario
            document.getElementById('nombrePelicula').value = '';
            document.getElementById('precioPelicula').value = '';
            document.getElementById('generoPelicula').value = '';
        })
        .catch(error => console.error('Error al agregar película:', error));
    });

        function deletePelicula(id) {
            fetch(`/api/peliculas/${id}`, { method: 'DELETE' })
                .then(() => {
                    document.querySelector(`.item[data-id="${id}"]`).remove();
                });
        }

        function editGenero(id) {
            document.getElementById('updateGeneroId').value = id;
            document.getElementById('updateDescripcionGenero').value = generos[id];
            document.getElementById('generoUpdateForm').style.display = 'block';
        }

        document.getElementById('generoUpdateForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const id = document.getElementById('updateGeneroId').value;
            const descripcion = document.getElementById('updateDescripcionGenero').value;
            fetch(`/api/generos/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descripcion })
            }).then(() => location.reload());
        });

	function editPelicula(id) {
            fetch(`/api/peliculas/${id}`)
                .then(response => response.json())
                .then(pelicula => {
                    document.getElementById('updatePeliculaId').value = pelicula.id;
                    document.getElementById('updateNombrePelicula').value = pelicula.nombre;
                    document.getElementById('updatePrecioPelicula').value = pelicula.precio;
                    const generoSelect = document.getElementById('updateGeneroPelicula');
                    generoSelect.innerHTML = '';
                    for (const [id, descripcion] of Object.entries(generos)) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = descripcion;
                        if (id == pelicula.codigogenero) {
                            option.selected = true;
                        }
                        generoSelect.appendChild(option);
                    }
                    document.getElementById('peliculaUpdateForm').style.display = 'block';
                })
           .catch(error => console.error("Error al obtener la película:", error));
    }

    // Actualizar Película
    document.getElementById('peliculaUpdateForm').addEventListener('submit', function (e) {
        e.preventDefault();
        
        const id = document.getElementById('updatePeliculaId').value;
        const nombre = document.getElementById('updateNombrePelicula').value;
        const precio = document.getElementById('updatePrecioPelicula').value;
        const codigogenero = document.getElementById('updateGeneroPelicula').value;

        fetch(`/api/peliculas/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ nombre, precio, codigogenero })
        })
        .then(response => response.json())
        .then(() => {
            cargarPeliculas();
            document.getElementById('peliculaUpdateForm').style.display = 'none';
        })
        .catch(error => console.error("Error al actualizar la película:", error));
    }); 


    </script>
</body>
</html>
