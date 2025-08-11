// Función para mostrar notificaciones
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.id = 'notification';
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    notification.style.display = 'block';
    
    // Ocultar después de 5 segundos
    setTimeout(() => {
        notification.style.display = 'none';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 5000);
}

// Función para formatear fechas
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Función para confirmar acciones
function confirmAction(title, message, confirmCallback) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.id = 'confirm-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">${title}</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <p>${message}</p>
            </div>
            <div class="modal-footer">
                <button id="modal-cancel" class="btn btn-danger">Cancelar</button>
                <button id="modal-confirm" class="btn btn-success">Confirmar</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'block';
    
    document.getElementById('modal-confirm').onclick = function() {
        confirmCallback();
        modal.style.display = 'none';
        setTimeout(() => {
            modal.remove();
        }, 500);
    };
    
    document.getElementById('modal-cancel').onclick = function() {
        modal.style.display = 'none';
        setTimeout(() => {
            modal.remove();
        }, 500);
    };
    
    document.querySelector('.close-modal').onclick = function() {
        modal.style.display = 'none';
        setTimeout(() => {
            modal.remove();
        }, 500);
    };
    
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            setTimeout(() => {
                modal.remove();
            }, 500);
        }
    };
}

// Función para exportar a Excel (simulada)
function exportToExcel(fileName = 'reporte') {
    showNotification(`Archivo ${fileName}.xlsx generado correctamente`, 'success');
    // En una implementación real, aquí se usaría una biblioteca como SheetJS
}

// Función para inicializar el sidebar
function initSidebar() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.html')) {
            item.classList.add('active');
        }
    });
}

// Función para inicializar la barra de navegación superior
function initTopNavbar() {
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function() {
            confirmAction(
                'Cerrar Sesión', 
                '¿Estás seguro de que deseas cerrar sesión?', 
                function() {
                    window.location.href = 'login.html';
                }
            );
        });
    }
    
    // Actualizar título de la página actual
    const pageTitles = {
        'index.html': 'Dashboard',
        'inventory.html': 'Inventario',
        'add-product.html': 'Agregar Producto',
        'movements.html': 'Movimientos',
        'suppliers.html': 'Proveedores',
        'barcode.html': 'Escanear',
        'reports.html': 'Reportes',
        'report-stock.html': 'Reporte de Stock',
        'report-movements.html': 'Reporte de Movimientos',
        'report-low-stock.html': 'Productos con Stock Bajo',
        'report-suppliers.html': 'Reporte de Proveedores'
    };
    
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const titleElement = document.getElementById('current-section-title');
    
    if (titleElement && pageTitles[currentPage]) {
        titleElement.textContent = pageTitles[currentPage];
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initTopNavbar();
    
    // Cargar datos iniciales si no existen
    if (!localStorage.getItem('products')) {
        const initialProducts = [
            {
                id: 1,
                nombre: "Laptop HP EliteBook",
                codigo: "123456789012",
                descripcion: "Laptop empresarial con procesador i7 y 16GB RAM",
                categoria: "Computadoras",
                proveedor_id: 1,
                stock_actual: 15,
                stock_minimo: 5,
                precio: 1299.99,
                created_at: "2023-03-10"
            },
            {
                id: 2,
                nombre: "Teclado inalámbrico Logitech",
                codigo: "987654321098",
                categoria: "Periféricos",
                proveedor_id: 1,
                stock_actual: 32,
                stock_minimo: 10,
                precio: 59.99,
                created_at: "2023-03-12"
            }
        ];
        localStorage.setItem('products', JSON.stringify(initialProducts));
    }
    
    if (!localStorage.getItem('suppliers')) {
        const initialSuppliers = [
            { 
                id: 1, 
                nombre: "TecnoSuministros", 
                contacto: "Juan Pérez", 
                telefono: "555-1234",
                email: "ventas@tecnosuministros.com",
                direccion: "Av. Tecnológica 123, Ciudad",
                created_at: "2023-01-15"
            }
        ];
        localStorage.setItem('suppliers', JSON.stringify(initialSuppliers));
    }
    
    if (!localStorage.getItem('movements')) {
        const initialMovements = [
            {
                id: 1,
                producto_id: 1,
                tipo: "entry",
                producto: "Laptop HP EliteBook",
                cantidad: 20,
                motivo: "Compra inicial",
                usuario: "admin",
                fecha: "2023-03-10"
            }
        ];
        localStorage.setItem('movements', JSON.stringify(initialMovements));
    }
});

// Función para generar un ID único
function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

// Función para obtener el nombre del proveedor
function getSupplierName(id) {
    if (!id) return 'N/A';
    const suppliers = JSON.parse(localStorage.getItem('suppliers')) || [];
    const supplier = suppliers.find(s => s.id === id);
    return supplier ? supplier.nombre : 'Desconocido';
}