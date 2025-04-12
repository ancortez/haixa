// Obtener el ID del cliente de la URL
const urlParams = new URLSearchParams(window.location.search);
const clienteId = urlParams.get('id');

// Función para asignar eventos a los botones de eliminar
function asignarEventosEliminar() {
    document.querySelectorAll('.btn-eliminar-contacto').forEach(btn => {
        btn.addEventListener('click', function() {
            const contactoId = this.getAttribute('data-id');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarContacto(contactoId);
                }
            });
        });
    });
}

// Función para eliminar un contacto via AJAX
function eliminarContacto(contactoId) {
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Eliminando contacto',
        html: 'Por favor espera...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`includes/eliminar_contacto.php?id=${contactoId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.error || 'Error en la respuesta del servidor'); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire(
                '¡Eliminado!',
                'El contacto ha sido eliminado correctamente.',
                'success'
            ).then(() => {
                // Recargar la tabla de contactos
                cargarContactos();
            });
        } else {
            throw new Error(data.error || 'Error al eliminar el contacto');
        }
    })
    .catch(error => {
        Swal.fire(
            'Error',
            error.message || 'Ocurrió un error al eliminar el contacto',
            'error'
        );
        console.error('Error al eliminar contacto:', error);
    });
}

// Función para recargar contactos sin refrescar toda la página
function cargarContactos() {
    fetch(`includes/cargar_contactos.php?id_cliente=${clienteId}`)
        .then(response => response.text())
        .then(html => {
            document.querySelector('#contacto .table-responsive').innerHTML = html;

            // Obtener el contacto principal y actualizar el panel
            const contactoPrincipal = document.querySelector('tr td span.badge.bg-success')?.closest('tr');
            if (contactoPrincipal) {
                const valorContacto = contactoPrincipal.querySelector('td:nth-child(2)').textContent;
                document.getElementById('contacto-principal').textContent = `Contacto principal: ${valorContacto}`;
            } else {
                document.getElementById('contacto-principal').textContent = 'Sin contacto principal';
            }
            
            // Reasignar eventos a los nuevos botones
            asignarEventosContactos();
            asignarEventosEliminar();
        })
        .catch(error => {
            console.error('Error al cargar contactos:', error);
            // Si falla, recargar toda la página
            window.location.reload();
        });
}

// Función mejorada para cerrar modales y limpiar completamente
function cerrarModalCompletamente(modalId) {
    const modalElement = document.getElementById(modalId);
    if (!modalElement) return;

    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }

    // Eliminar el backdrop después de la animación (300ms es el tiempo por defecto de Bootstrap)
    setTimeout(() => {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Restaurar el estado del body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '0';
    }, 300);
}

// Función para limpiar formularios de modales
function limpiarFormularioModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Función para asignar eventos a los botones de contacto
function asignarEventosContactos() {
    // Asignar eventos a los botones de editar
    document.querySelectorAll('.btn-editar-contacto').forEach(btn => {
        btn.addEventListener('click', function() {
            const contactoId = this.getAttribute('data-id');
            const tipoContacto = this.getAttribute('data-tipo');
            const valorContacto = this.getAttribute('data-valor');
            const principal = this.getAttribute('data-principal') === '1';
            
            // Llenar el formulario de edición
            document.getElementById('contacto_id').value = contactoId;
            document.getElementById('edit_tipo_contacto').value = tipoContacto;
            document.getElementById('edit_valor_contacto').value = valorContacto;
            document.getElementById('edit_principal').checked = principal;
        });
    });
    
    // Asignar eventos a los botones de eliminar
    document.querySelectorAll('.btn-eliminar-contacto').forEach(btn => {
        btn.addEventListener('click', function() {
            const contactoId = this.getAttribute('data-id');
            
            Swal.fire({
                title: '¿Está seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`includes/eliminar_contacto.php?id=${contactoId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    '¡Eliminado!',
                                    'El contacto ha sido eliminado.',
                                    'success'
                                ).then(() => {
                                    cargarContactos();
                                });
                            } else {
                                throw new Error(data.error || 'Error al eliminar');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message, 'error');
                            console.error('Error:', error);
                        });
                }
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Verificar que SweetAlert esté cargado
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado');
        // Opcional: cargar SweetAlert2 dinámicamente
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);
    }

    // Formulario de identificación
    const formIdentificacion = document.getElementById('formIdentificacion');
    if (formIdentificacion) {
        formIdentificacion.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: '¿Confirmar cambios?',
                text: "Los datos de identificación son muy importantes. ¿Está seguro de modificarlos?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(formIdentificacion);
                    
                    fetch('includes/actualizar_cliente.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la respuesta');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            actualizarPanelIzquierdo(data.cliente);
                            Swal.fire('¡Guardado!', 'Datos actualizados correctamente', 'success');
                        } else {
                            throw new Error(data.error || 'Error desconocido');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', error.message, 'error');
                        console.error('Error:', error);
                    });
                }
            });
        });
    }

    // Función para actualizar el panel izquierdo
    function actualizarPanelIzquierdo(datosCliente) {
        // Actualizar nombre
        const nombreCompleto = `${datosCliente.nombres} ${datosCliente.apellido_paterno} ${datosCliente.apellido_materno || ''}`.trim();
        document.querySelector('.card-body.text-center h3').textContent = nombreCompleto;
        
        // Actualizar RFC
        if (datosCliente.rfc) {
            document.querySelector('.card-body.text-center .text-muted').textContent = datosCliente.rfc;
        }
    }

    // Manejo de domicilios
    const btnNuevoDomicilio = document.getElementById('btnNuevoDomicilio');
    if (btnNuevoDomicilio) {
        btnNuevoDomicilio.addEventListener('click', manejarNuevoDomicilio);
    }

    function manejarNuevoDomicilio() {
        const form = document.getElementById('formDomicilio');
        const titulo = document.getElementById('domicilioTitulo');
        const estado = document.getElementById('estado');
        const linkModificar = document.getElementById('linkModificarDomicilio');
        const btnGuardar = document.getElementById('btnGuardarDomicilio');
        const btnCancelar = document.getElementById('btnCancelarDomicilio');
        
        if (!form || !titulo || !estado || !linkModificar || !btnGuardar || !btnCancelar) {
            console.error('Elementos del formulario de domicilio no encontrados');
            return;
        }
        
        form.reset();
        form.querySelector('input[name="id"]').value = '';
        titulo.textContent = '*REGISTRO DE NUEVO DOMICILIO';
        titulo.className = 'mb-0 d-inline-block text-warning';
        
        disableForm(form);
        estado.disabled = false;
        
        linkModificar.classList.add('d-none');
        btnGuardar.classList.remove('d-none');
        btnCancelar.classList.remove('d-none');
    }

    // Funciones auxiliares
    function enableForm(form) {
        if (!form) return;
        Array.from(form.elements).forEach(element => {
            element.disabled = false;
        });
    }

    function disableForm(form) {
        if (!form) return;
        Array.from(form.elements).forEach(element => {
            element.disabled = true;
        });
    }

    // Manejo de contactos
    document.querySelectorAll('.btn-editar-contacto').forEach(btn => {
        btn.addEventListener('click', function() {
            const contactoId = this.getAttribute('data-id');
            const tipoContacto = this.getAttribute('data-tipo');
            const valorContacto = this.getAttribute('data-valor');
            const principal = this.getAttribute('data-principal') === '1';
            
            // Llenar el formulario de edición
            document.getElementById('contacto_id').value = contactoId;
            document.getElementById('edit_tipo_contacto').value = tipoContacto;
            document.getElementById('edit_valor_contacto').value = valorContacto;
            document.getElementById('edit_principal').checked = principal;
        });
    });

    const formNuevoContacto = document.getElementById('formNuevoContacto');
    const formEditarContacto = document.getElementById('formEditarContacto');

    // Para nuevo contacto
    if (formNuevoContacto) {
        formNuevoContacto.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch('includes/guardar_contacto.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Contacto guardado correctamente',
                        icon: 'success'
                    }).then(() => {
                        // Cerrar y limpiar el modal de nuevo contacto
                        cerrarModalCompletamente('nuevoContactoModal');
                        this.reset();
                        // Recargar la tabla de contactos
                        cargarContactos();
                    });
                } else {
                    throw new Error(data.error || 'Error al guardar');
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message, 'error');
                console.error('Error:', error);
            });
        });
    }


    // Para editar contacto
    if (formEditarContacto) {
        formEditarContacto.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch('includes/actualizar_contacto.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Contacto actualizado correctamente',
                        icon: 'success'
                    }).then(() => {
                        // Cerrar y limpiar el modal de editar contacto
                        cerrarModalCompletamente('editarContactoModal');
                        // Recargar la tabla de contactos
                        cargarContactos();
                    });
                } else {
                    throw new Error(data.error || 'Error al actualizar');
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message, 'error');
                console.error('Error:', error);
            });
        });
    }

    // Eventos para manejar el cierre manual (botón X o Cancelar)
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                cerrarModalCompletamente(modal.id);
            }
        });
    });

    // Manejar el evento hidden.bs.modal para limpieza adicional
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            // Limpieza adicional si es necesario
            document.body.classList.remove('modal-open');
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0';
        });
    });

    function mostrarAlertaContacto(tipo, mensaje) {
        const alerta = document.createElement('div');
        alerta.className = `alert alert-${tipo} alert-dismissible fade show mb-3`;
        alerta.role = 'alert';
        alerta.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const contenedor = document.querySelector('#contacto .tab-pane');
        contenedor.prepend(alerta);
        
        setTimeout(() => {
            alerta.classList.remove('show');
            setTimeout(() => alerta.remove(), 150);
        }, 5000);
    }

    // Eliminar contacto
    document.querySelectorAll('.btn-eliminar-contacto').forEach(btn => {
        btn.addEventListener('click', function() {
            const contactoId = this.getAttribute('data-id');
            
            if (confirm('¿Está seguro de eliminar este contacto?')) {
                fetch(`/haixa/includes/eliminar_contacto.php?id=${contactoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Contacto eliminado correctamente');
                            window.location.reload(); // Recargar para ver los cambios
                        } else {
                            alert('Error al eliminar: ' + (data.error || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar el contacto');
                    });
            }
        });
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
        fetch('/haixa/includes/cargar_estados.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                estadoSelect.innerHTML = '<option value="">Seleccionar...</option>';
                data.forEach(estado => {
                    const option = document.createElement('option');
                    option.value = estado.id_estado;
                    option.textContent = estado.nombre_estado;
                    estadoSelect.appendChild(option);
                });
            });
    }
});

// Manejar eventos de cierre del modal
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function () {
        // Eliminar el backdrop si existe
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
        
        // Restaurar el scroll del body
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '0';
        document.body.classList.remove('modal-open');
    });
});