# Sistema de Compras y Suministros 🏛️
## Municipalidad de Danlí, El Paraíso

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap)

### 📋 Descripción del Proyecto
Este sistema ha sido desarrollado para optimizar y modernizar la gestión de adquisiciones y suministros de la **Municipalidad de Danlí**. Permite el control eficiente de órdenes de compra, gestión de proveedores, y la generación de reportes detallados para la toma de decisiones y auditoría.

El objetivo principal es agilizar los procesos administrativos, garantizando transparencia y orden en cada transacción realizada por la institución.

---

### ✨ Características Principales

#### 📦 Gestión de Órdenes
- **Creación de Órdenes**: Interfaz intuitiva para ingresar nuevas compras con autocompletado de proveedores y solicitantes.
- **Búsqueda Rápida**: Funcionalidad para localizar órdenes históricas por su número de identificación.
- **Vista preliminar e Impresión**: Generación de formatos de impresión estandarizados (Recibos y Órdenes) listos para firma.

#### 📊 Reportes Avanzados
El sistema cuenta con un módulo de reportes robusto, filtrable por rangos de fecha personalizados:
1.  **Informe Detallado**: Desglose minucioso de cada producto adquirido (descripción, cantidad, precio unitario).
2.  **Compras por Proveedor**: Agrupación de facturas por proveedor para facilitar pagos y revisiones de cuenta.
3.  **Resumen Ejecutivo**: Vista consolidada de totales comprados por proveedor para análisis financiero rápido.
4.  **Listado General**: Histórico secuencial de todas las transacciones del período.

#### 🛠️ Herramientas Administrativas
- **Gestión de Proveedores**: Base de datos unificada de proveedores con historial de transacciones.
- **Autocompletado Inteligente**: Agiliza la entrada de datos sugiriendo proveedores y solicitantes existentes.

---

### 🚀 Requisitos del Sistema
- **PHP**: >= 8.1
- **Composer**
- **Servidor Web**: Apache/Nginx (Recomendado Laragon/XAMPP en local)
- **Base de Datos**: MySQL

---

### 🔧 Instalación y Configuración

1. **Clonar el Repositorio**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd Compras_y_Suministros
   ```

2. **Instalar Dependencias**
   ```bash
   composer install
   npm install
   ```

3. **Configurar Entorno**
   - Renombrar `.env.example` a `.env`
   - Configurar credenciales de base de datos en `.env`.

4. **Generar Clave de Aplicación**
   ```bash
   php artisan key:generate
   ```

5. **Migrar Base de Datos**
   ```bash
   php artisan migrate
   ```

6. **Iniciar Servidor Local**
   ```bash
   php artisan serve
   ```

---

### 📄 Licencia
Este software es propiedad de la **Municipalidad de Danlí** y su uso está restringido a fines institucionales autorizados.

---
**Desarrollado para la Municipalidad de Danlí - 2026**
