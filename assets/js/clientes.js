document.addEventListener('DOMContentLoaded', function() {
    // Manejo de la pestaña de domicilios
    const domicilioForm = document.getElementById('formDomicilio');
    const btnModificarDomicilio = document.getElementById('btnModificarDomicilio');
    const btnGuardarDomicilio = document.getElementById('btnGuardarDomicilio');
    const btnCancelarDomicilio = document.getElementById('btnCancelarDomicilio');
    const btnNuevoDomicilio = document.getElementById('btnNuevoDomicilio');
    const domicilioTitulo = document.getElementById('domicilioTitulo');
    
    // Estados y municipios
    const estadoSelect = document.getElementById('estado');
    const municipioSelect = document.getElementById('municipio');
    const codigoPostalInput = document.getElementById('codigo_postal');
    const btnBuscarColonias = document.getElementById('btnBuscarColonias');
    const coloniaSelect = document.getElementById('colonia');
    const calleSelect = document.getElementById('calle');
    
    // Cargar estados al iniciar
    fetchEstados();
    
    // Evento para modificar domicilio
    btnModificarDomicilio.addEventListener('click', function() {
        enableForm(domicilioForm);
        btnModificarDomicilio.classList.add('d-none');
        btnGuardarDomicilio.classList.remove('d-none');
        btnCancelarDomicilio.classList.remove('d-none');
    });
    
    // Evento para cancelar edición
    btnCancelarDomicilio.addEventListener('click', function() {
        disableForm(domicilioForm);
        btnModificarDomicilio.classList.remove('d-none');
        btnGuardarDomicilio.classList.add('d-none');
        btnCancelarDomicilio.classList.add('d-none');
        // Aquí podrías resetear el formulario si es necesario
    });
    
    // Evento para nuevo domicilio
    btnNuevoDomicilio.addEventListener('click', function() {
        domicilioForm.reset();
        domicilioTitulo.textContent = 'REGISTRO DE NUEVO DOMICILIO';
        domicilioTitulo.classList.add('text-warning');
        enableForm(domicilioForm);
        btnModificarDomicilio.classList.add('d-none');
        btnGuardarDomicilio.classList.remove('d-none');
        btnCancelarDomicilio.classList.remove('d-none');
    });
    
    // Evento cambio de estado
    estadoSelect.addEventListener('change', function() {
        const estadoId = this.value;
        municipioSelect.disabled = true;
        municipioSelect.innerHTML = '<option value="">Cargando...</option>';
        
        if (estadoId) {
            fetch(`/api/municipios?estado=${estadoId}`)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccionar...</option>';
                    data.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.id;
                        option.textContent = municipio.nombre;
                        municipioSelect.appendChild(option);
                    });
                    municipioSelect.disabled = false;
                });
        } else {
            municipioSelect.innerHTML = '<option value="">Seleccionar estado primero</option>';
        }
    });
    
    // Evento para buscar colonias por CP
    btnBuscarColonias.addEventListener('click', function() {
        const cp = codigoPostalInput.value;
        const municipioId = municipioSelect.value;
        
        if (cp.length !== 5) {
            alert('El código postal debe tener 5 dígitos');
            return;
        }
        
        if (!municipioId) {
            alert('Seleccione un municipio primero');
            return;
        }
        
        coloniaSelect.disabled = true;
        coloniaSelect.innerHTML = '<option value="">Cargando...</option>';
        
        fetch(`/api/colonias?cp=${cp}&municipio=${municipioId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    coloniaSelect.innerHTML = '<option value="">No se encontraron colonias</option>';
                    alert('No se encontraron colonias para este CP en el municipio seleccionado');
                } else {
                    coloniaSelect.innerHTML = '<option value="">Seleccionar...</option>';
                    data.forEach(colonia => {
                        const option = document.createElement('option');
                        option.value = colonia.id;
                        option.textContent = colonia.nombre;
                        coloniaSelect.appendChild(option);
                    });
                    coloniaSelect.disabled = false;
                }
            });
    });
    
    // Evento cambio de colonia
    coloniaSelect.addEventListener('change', function() {
        const coloniaId = this.value;
        calleSelect.disabled = true;
        calleSelect.innerHTML = '<option value="">Cargando...</option>';
        
        if (coloniaId) {
            fetch(`/api/calles?colonia=${coloniaId}`)
                .then(response => response.json())
                .then(data => {
                    calleSelect.innerHTML = '<option value="">Seleccionar...</option>';
                    data.forEach(calle => {
                        const option = document.createElement('option');
                        option.value = calle.id;
                        option.textContent = calle.nombre;
                        calleSelect.appendChild(option);
                    });
                    calleSelect.disabled = false;
                });
        } else {
            calleSelect.innerHTML = '<option value="">Seleccione colonia primero</option>';
        }
    });
    
    // Funciones auxiliares
    function enableForm(form) {
        const elements = form.elements;
        for (let i = 0; i < elements.length; i++) {
            elements[i].disabled = false;
        }
    }
    
    function disableForm(form) {
        const elements = form.elements;
        for (let i = 0; i < elements.length; i++) {
            elements[i].disabled = true;
        }
    }
    
    function fetchEstados() {
        fetch('/api/estados')
            .then(response => response.json())
            .then(data => {
                estadoSelect.innerHTML = '<option value="">Seleccionar...</option>';
                data.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.id;
                    option.textContent = estado.nombre;
                    estadoSelect.appendChild(option);
                });
            });
    }
});