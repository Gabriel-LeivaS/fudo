# 🚨 GUÍA RÁPIDA DE DEBUGGING

## ⚡ EJECUCIÓN RÁPIDA (2 minutos)

### Terminal 1: Monitor de Logs PHP
```powershell
cd C:\xampp\htdocs\fudo
.\ver_logs_permisos.bat
```
**DEJAR ABIERTA** ⚠️

---

### Terminal 2: Ver logs PostgreSQL (opcional)
```powershell
# Si usas pgAdmin, ver query logs
# O ejecutar SELECT antes y después de editar
```

---

### Navegador: DevTools
1. **F12** → Pestaña **Console**
2. ✅ Activar **"Preserve log"** (checkbox arriba)
3. Ir a `http://localhost/fudo`
4. Login como `admin_centro`

---

## 🧪 PRUEBA

### Escenario 1: Marcar Cocina

1. **Usuarios** → Editar `usuario_centro`
2. ✅ **Marcar** checkbox "Cocina"
3. **Guardar**

**Observar:**
- Console (F12): ¿Qué dice `Permisos enviados:`?
- PowerShell: ¿Apareció el log `=== EDITAR USUARIO DEBUG ===`?

**Verificar BD:**
```sql
SELECT permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```
¿Tiene `"cocina":true`?

---

### Escenario 2: Desmarcar Cocina

1. **Usuarios** → Editar `usuario_centro`
2. ❌ **Desmarcar** checkbox "Cocina"
3. **Guardar**

**Observar logs nuevamente**

**Verificar BD:**
```sql
SELECT permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```
¿Cambió a `"cocina":false`?

---

## 📋 CHECKLIST

- [ ] `ver_logs_permisos.bat` corriendo
- [ ] F12 abierto en pestaña Console
- [ ] "Preserve log" activado
- [ ] Editar usuario → Marcar Cocina → Guardar
- [ ] Ver logs en Console
- [ ] Ver logs en PowerShell
- [ ] Verificar BD (SELECT)
- [ ] Editar usuario → Desmarcar Cocina → Guardar
- [ ] Ver logs nuevamente
- [ ] Verificar BD nuevamente

---

## 📤 QUÉ ENVIARME

### 1. Screenshot o copia de Console (F12)
```
Ejemplo:
=== DEBUG EDITAR USUARIO ===
ID Usuario: 5
Rol: usuario
Permisos enviados: {pedidos: "1", ...}
```

### 2. Copia de PowerShell logs
```
Ejemplo:
=== EDITAR USUARIO DEBUG ===
Usuario editado: usuario_centro
Permisos POST: Array(...)
```

### 3. Resultado de BD
```sql
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

---

## 🔍 POSIBLES RESULTADOS

### ✅ CASO A: Todo se ve bien en logs pero BD no cambia
**Causa:** Problema en `Usuario_model->actualizar()`

### ❌ CASO B: Permisos enviados = {} (vacío)
**Causa:** FormData no captura checkboxes

### ❌ CASO C: Logs de PHP dicen "SIN PERMISOS"
**Causa:** POST no llega al servidor

### ❌ CASO D: No aparecen logs de PHP
**Causa:** Método `editar()` no se está ejecutando

---

## 🆘 SI NO APARECEN LOGS

### Verificar que Apache está logueando:
```powershell
# Ver últimas 20 líneas del log
Get-Content C:\xampp\apache\logs\error.log -Tail 20
```

### Si está vacío, habilitar logs:
1. Abrir `C:\xampp\apache\conf\httpd.conf`
2. Buscar `LogLevel`
3. Cambiar a: `LogLevel warn`
4. Reiniciar Apache

---

## 💡 TIPS

- Si no ves logs de PHP, prueba marcar/desmarcar varias veces
- Si Console está vacía, recarga la página (F5)
- Si PowerShell no muestra nada, verifica que Apache esté corriendo
- Si BD no cambia, verifica que el usuario tenga permisos de escritura

---

**¡Con estos logs tendré suficiente info para solucionar el problema!** 🎯
