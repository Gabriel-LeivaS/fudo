# 🔍 PRUEBA DE DEBUGGING - Permisos no se guardan

## 📋 Problema Actual

✅ **Confirmado:**
- Editar permisos desde SQL → Funciona
- Editar permisos desde admin_centro → NO funciona

❌ **Causa:** El formulario NO está guardando correctamente los cambios en la BD

---

## 🛠️ Debugging Implementado

He agregado **logs completos** en:

### 1. Backend (PHP)
**Archivo:** `application/controllers/Usuarios.php` (líneas 193-223)

**Logs agregados:**
- ✅ Muestra datos POST recibidos
- ✅ Muestra cómo se procesan los permisos
- ✅ Muestra JSON generado
- ✅ Detecta si `permisos_array` está vacío

### 2. Frontend (JavaScript)
**Archivo:** `application/views/admin/usuarios.php` (líneas 970-997)

**Logs agregados:**
- ✅ Muestra FormData antes de enviar
- ✅ Muestra checkboxes marcados
- ✅ Se puede ver en consola del navegador (F12)

---

## 🧪 PRUEBA PASO A PASO

### Paso 1: Abrir Monitor de Logs

```powershell
# Opción A: Usar el script creado
cd C:\xampp\htdocs\fudo
.\ver_logs_permisos.bat

# Opción B: PowerShell directo
Get-Content C:\xampp\apache\logs\error.log -Tail 0 -Wait | Select-String -Pattern "EDITAR USUARIO"
```

**Deja esta ventana abierta** para ver logs en tiempo real

---

### Paso 2: Abrir Navegador con DevTools

1. Abre Chrome/Edge
2. Presiona **F12** (abrir DevTools)
3. Ve a pestaña **Console**
4. Activa "Preserve log" (checkbox arriba)

---

### Paso 3: Realizar Prueba

1. Login como `admin_centro`
2. Ir a **Usuarios**
3. Editar `usuario_centro`
4. **Marcar** el checkbox "Cocina" (activarlo)
5. Guardar

**Observar:**
- ✅ En **Console (F12)**: Ver qué permisos se enviaron
- ✅ En **PowerShell**: Ver logs del servidor
- ✅ Toast de éxito

---

### Paso 4: Verificar en BD

```sql
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

**Resultado esperado:**
```json
{"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}
```

---

### Paso 5: Probar Desmarcar

1. Editar `usuario_centro` de nuevo
2. **Desmarcar** el checkbox "Cocina"
3. Guardar

**Observar los mismos logs**

4. Verificar en BD:
```sql
SELECT usuario, permisos FROM usuarios_admin WHERE usuario = 'usuario_centro';
```

**Resultado esperado:**
```json
{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}
```

---

## 🎯 Qué Buscar en los Logs

### ✅ Caso CORRECTO (debería verse así):

**Console del navegador:**
```javascript
=== DEBUG EDITAR USUARIO ===
ID Usuario: 5
Rol: usuario
Permisos enviados: {
  pedidos: "1",
  mesas: "1",
  cocina: "1",        // ✅ Si está marcado
  mi_carta: "1"
}
============================
```

**PowerShell (PHP logs):**
```
=== EDITAR USUARIO DEBUG ===
Usuario editado: usuario_centro
Rol: usuario
Permisos POST: Array
(
    [pedidos] => 1
    [mesas] => 1
    [cocina] => 1       // ✅ Si está marcado
    [mi_carta] => 1
)
Permisos procesados: Array
(
    [pedidos] => 1
    [mesas] => 1
    [cocina] => 1       // ✅ true
    [mi_carta] => 1
    [categorias] => 
    [productos] => 
)
JSON generado: {"pedidos":true,"mesas":true,"cocina":true,"mi_carta":true,"categorias":false,"productos":false}
===========================
```

---

### ❌ Posibles Problemas:

#### Problema 1: Permisos NO llegan al servidor

**Console:**
```javascript
Permisos enviados: {}  // ❌ Vacío!
```

**Causa:** Los checkboxes no se están enviando en FormData

**Solución:** Problema en el HTML del formulario

---

#### Problema 2: `permisos_array` es NULL

**PowerShell:**
```
=== EDITAR USUARIO - SIN PERMISOS ===
Rol: usuario pero permisos_array NO es array
permisos_array valor: NULL
```

**Causa:** El servidor no recibe `$_POST['permisos']`

**Solución:** Ver nombre de los inputs en HTML

---

#### Problema 3: JSON se genera pero no se guarda en BD

**PowerShell muestra JSON correcto pero BD no cambia**

**Causa:** Problema en `Usuario_model->actualizar()`

**Solución:** Revisar modelo

---

## 📊 Checklist de Diagnóstico

- [ ] Monitor de logs abierto
- [ ] DevTools (F12) abierto en pestaña Console
- [ ] Editar usuario y marcar "Cocina"
- [ ] Ver logs en Console del navegador
- [ ] Ver logs en PowerShell
- [ ] Verificar en BD si se guardó
- [ ] Editar usuario y desmarcar "Cocina"
- [ ] Ver logs nuevamente
- [ ] Verificar en BD si cambió a false

---

## 🚨 EJECUTAR AHORA

1. **Abrir terminal PowerShell:**
```powershell
cd C:\xampp\htdocs\fudo
.\ver_logs_permisos.bat
```

2. **Abrir navegador con F12**

3. **Realizar prueba:** Editar usuario_centro → Marcar/desmarcar Cocina → Guardar

4. **Copiar TODOS los logs** y envíarmelos:
   - Logs de Console (F12)
   - Logs de PowerShell
   - Resultado de SELECT en PostgreSQL

---

## 💡 Resultado Esperado

Después de esta prueba sabremos **EXACTAMENTE** dónde está fallando:

- ✅ **Frontend no envía:** Problema en HTML/JavaScript
- ✅ **Backend no recibe:** Problema en POST
- ✅ **Backend recibe pero no guarda:** Problema en modelo
- ✅ **Todo se ve bien en logs pero BD no cambia:** Problema en transacción SQL

---

**Fecha:** 13 de octubre de 2025  
**Estado:** Debugging implementado, esperando logs de prueba
