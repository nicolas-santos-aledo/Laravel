<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Géneros y Películas Management</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .item { margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .item h3 { margin: 0; }
        .item p { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Géneros y Películas Management</h1>
    
    <h2>Géneros</h2>
    <div id="generos"></div>
    
    <h2>Películas</h2>
    <div id="peliculas"></div>
    
    <h2>Agregar Nuevo Género</h2>
    <form id="generoForm">
        <input type="text" id="descripcionGenero" placeholder="Descripción del Género" required>
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
        <input type="text" id="nombrePelicula" placeholder="Nombre de la Película" required>
        <select id="genero" required>
            <option value="">Seleccione un Género</option>
        </select>
        <input type="number" id="precio" placeholder="Precio" required>
        <button type="submit">Agregar Película</button>
    </form>

    <h2>Actualizar Película</h2>
    <form id="peliculaUpdateForm" style="display:none;">
        <input type="hidden" id="updatePeliculaId">
        <input type="text" id="updateNombrePelicula" placeholder="Nuevo Nombre" required>
        <select id="updateGenero" required></select>
        <input type="number" id="updatePrecio" placeholder="Nuevo Precio" required>
        <button type="submit">Actualizar Película</button>
    </form>

    <script>
        let generos = {};

        // Cargar Géneros
        fetch('/api/generos')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('generos');
                const generoSelect = document.getElementById('genero');
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

        // Cargar Películas
        function cargarPeliculas() {
            fetch('/api/peliculas')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('peliculas');
                    container.innerHTML = '';
                    data.forEach(pelicula => {
                        const generoNombre = generos[pelicula.codigogenero] || 'Desconocido';
                        container.innerHTML += `
                            <div class="item" data-id="${pelicula.id}">
                                <h3>${pelicula.nombre} (${generoNombre}, $${pelicula.precio})</h3>
                                <button onclick="editPelicula(${pelicula.id})">Editar</button>
                                <button onclick="deletePelicula(${pelicula.id})">Eliminar</button>
                            </div>`;
                    });
                });
        }

        // Agregar Género
        document.getElementById('generoForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const descripcion = document.getElementById('descripcionGenero').value;
            fetch('/api/generos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descripcion })
            }).then(response => response.json())
              .then(genero => {
                  generos[genero.id] = genero.descripcion;
                  document.getElementById('generos').innerHTML += `
                      <div class="item" data-id="${genero.id}">
                          <h3>${genero.descripcion}</h3>
                          <button onclick="editGenero(${genero.id})">Editar</button>
                          <button onclick="deleteGenero(${genero.id})">Eliminar</button>
                      </div>`;
                  document.getElementById('genero').innerHTML += `<option value="${genero.id}">${genero.descripcion}</option>`;
                  document.getElementById('generoForm').reset();
              });
        });

        // Editar Género
        function editGenero(id) {
            const genero = generos[id];
            const generoData = document.querySelector(`.item[data-id="${id}"]`);
            const descripcion = generoData.querySelector('h3').textContent;

            document.getElementById('updateGeneroId').value = id;
            document.getElementById('updateDescripcionGenero').value = descripcion;
            document.getElementById('generoUpdateForm').style.display = 'block';
        }

        // Actualizar Género
        document.getElementById('generoUpdateForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('updateGeneroId').value;
            const descripcion = document.getElementById('updateDescripcionGenero').value;
            fetch(`/api/generos/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descripcion })
            }).then(response => response.json())
              .then(genero => {
                  const generoData = document.querySelector(`.item[data-id="${id}"]`);
                  generoData.querySelector('h3').textContent = genero.descripcion;
                  document.getElementById('generoUpdateForm').style.display = 'none';
              });
        });

        // Agregar Película
        document.getElementById('peliculaForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const nombre = document.getElementById('nombrePelicula').value;
            const generoId = document.getElementById('genero').value;
            const precio = document.getElementById('precio').value;
            fetch('/api/peliculas', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre, codigogenero: generoId, precio })
            }).then(response => response.json())
              .then(() => {
                  cargarPeliculas();
                  document.getElementById('peliculaForm').reset();
              });
        });

        // Editar Película
        function editPelicula(id) {
            fetch(`/api/peliculas/${id}`)
                .then(response => response.json())
                .then(pelicula => {
                    document.getElementById('updatePeliculaId').value = pelicula.id;
                    document.getElementById('updateNombrePelicula').value = pelicula.nombre;
                    document.getElementById('updatePrecio').value = pelicula.precio;

                    const generoSelect = document.getElementById('updateGenero');
                    generoSelect.innerHTML = ''; // Limpiar las opciones anteriores
                    Object.keys(generos).forEach(generoId => {
                        const option = document.createElement('option');
                        option.value = generoId;
                        option.textContent = generos[generoId];
                        if (generoId == pelicula.codigogenero) option.selected = true;
                        generoSelect.appendChild(option);
                    });

                    document.getElementById('peliculaUpdateForm').style.display = 'block';
                })
                .catch(error => console.error("Error al obtener la película:", error));
        }

        // Actualizar Película
        document.getElementById('peliculaUpdateForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('updatePeliculaId').value;
            const nombre = document.getElementById('updateNombrePelicula').value;
            const generoId = document.getElementById('updateGenero').value;
            const precio = document.getElementById('updatePrecio').value;
            fetch(`/api/peliculas/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre, codigogenero: generoId, precio })
            }).then(response => response.json())
              .then(() => {
                  cargarPeliculas();
                  document.getElementById('peliculaUpdateForm').style.display = 'none';
              });
        });

        // Eliminar Género
        function deleteGenero(id) {
            fetch(`/api/generos/${id}`, { method: 'DELETE' })
                .then(() => {
                    document.querySelector(`.item[data-id="${id}"]`).remove();
                    document.querySelector(`#genero option[value="${id}"]`).remove();
                    delete generos[id];
                    cargarPeliculas();
                });
        }

        // Eliminar Película
        function deletePelicula(id) {
            fetch(`/api/peliculas/${id}`, { method: 'DELETE' })
                .then(() => {
                    document.querySelector(`.item[data-id="${id}"]`).remove();
                });
        }
    </script>
</body>
</html>
