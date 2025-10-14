/**
 * Script para verificar y actualizar el estado de la sesión del usuario
 * Previene el acceso no autorizado cuando la sesión expira
 */

(function() {
    'use strict';
    
    // Configuración
    const CONFIG = {
        INTERVALO_VERIFICACION: 30000, // 30 segundos
        URL_VERIFICAR_SESION: window.location.origin + '/fudo/login/verificar_sesion',
        URL_LOGIN: window.location.origin + '/fudo/login',
        TIMEOUT_REQUEST: 10000 // 10 segundos timeout
    };
    
    let intervalId = null;
    let verificandoSesion = false;
    
    /**
     * Verifica si el usuario está logueado según el atributo data-logueado
     */
    function usuarioEstaLogueado() {
        const body = document.body;
        return body && body.getAttribute('data-logueado') === 'true';
    }
    
    /**
     * Realiza una petición AJAX para verificar el estado de la sesión
     */
    function verificarSesion() {
        if (verificandoSesion) return;
        
        verificandoSesion = true;
        
        const xhr = new XMLHttpRequest();
        xhr.timeout = CONFIG.TIMEOUT_REQUEST;
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                verificandoSesion = false;
                
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (!response.sesion_activa) {
                            manejarSesionExpirada();
                        }
                    } catch (e) {
                        console.warn('Error al parsear respuesta de verificación de sesión:', e);
                    }
                } else if (xhr.status === 401 || xhr.status === 403) {
                    manejarSesionExpirada();
                }
                // Para otros errores (500, timeout, etc.) no hacemos nada
                // para evitar redirecciones innecesarias por problemas de red
            }
        };
        
        xhr.ontimeout = function() {
            verificandoSesion = false;
            console.warn('Timeout en verificación de sesión');
        };
        
        xhr.onerror = function() {
            verificandoSesion = false;
            console.warn('Error en verificación de sesión');
        };
        
        xhr.open('GET', CONFIG.URL_VERIFICAR_SESION, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
    }
    
    /**
     * Maneja cuando la sesión ha expirado
     */
    function manejarSesionExpirada() {
        // Detener verificaciones futuras
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
        
        // Mostrar mensaje al usuario si es posible
        mostrarMensajeSesionExpirada();
        
        // Redirigir al login después de un breve delay
        setTimeout(function() {
            window.location.href = CONFIG.URL_LOGIN;
        }, 2000);
    }
    
    /**
     * Muestra un mensaje cuando la sesión expira
     */
    function mostrarMensajeSesionExpirada() {
        // Intentar usar el sistema de toast si existe
        if (typeof mostrarToast === 'function') {
            mostrarToast('Sesión Expirada', 'Tu sesión ha expirado. Serás redirigido al login.', 'warning');
            return;
        }
        
        // Intentar usar Bootstrap toast si existe
        const toastElement = document.getElementById('toastNotificacion');
        if (toastElement) {
            const toastTitulo = document.getElementById('toastTitulo');
            const toastMensaje = document.getElementById('toastMensaje');
            
            if (toastTitulo && toastMensaje) {
                toastTitulo.textContent = '⚠️ Sesión Expirada';
                toastMensaje.textContent = 'Tu sesión ha expirado. Serás redirigido al login en unos segundos.';
                
                // Usar Bootstrap Toast si está disponible
                if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();
                } else {
                    // Fallback: mostrar el toast manualmente
                    toastElement.classList.add('show');
                }
                return;
            }
        }
        
        // Fallback: usar alert nativo
        alert('Tu sesión ha expirado. Serás redirigido al login.');
    }
    
    /**
     * Inicializa el sistema de verificación de sesión
     */
    function inicializar() {
        // Solo inicializar si el usuario está logueado
        if (!usuarioEstaLogueado()) {
            return;
        }
        
        console.log('Iniciando verificación automática de sesión');
        
        // Verificar inmediatamente
        verificarSesion();
        
        // Configurar verificación periódica
        intervalId = setInterval(verificarSesion, CONFIG.INTERVALO_VERIFICACION);
        
        // Verificar también cuando la ventana recupera el foco
        window.addEventListener('focus', function() {
            if (usuarioEstaLogueado()) {
                verificarSesion();
            }
        });
        
        // Verificar cuando se detecta actividad del usuario
        let timeoutActividad = null;
        const eventos = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        eventos.forEach(function(evento) {
            document.addEventListener(evento, function() {
                if (timeoutActividad) {
                    clearTimeout(timeoutActividad);
                }
                
                timeoutActividad = setTimeout(function() {
                    if (usuarioEstaLogueado()) {
                        verificarSesion();
                    }
                }, 5000); // Verificar 5 segundos después de la última actividad
            }, true);
        });
    }
    
    /**
     * Limpia los recursos cuando se cierra la página
     */
    function limpiar() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
    
    // Limpiar al cerrar la página
    window.addEventListener('beforeunload', limpiar);
    
    // Exponer funciones globales si es necesario
    window.VerificadorSesion = {
        verificar: verificarSesion,
        detener: limpiar,
        reiniciar: function() {
            limpiar();
            inicializar();
        }
    };
    
})();
