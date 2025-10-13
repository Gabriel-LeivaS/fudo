# ✅ CHECKLIST DE PRUEBA - Editar Permisos desde Panel Admin

## 📊 Estado Actual Confirmado

```json
{"pedidos":true,"mesas":true,"cocina":false,"mi_carta":true,"categorias":false,"productos":false}
```

✅ **Cocina = FALSE** (correcto en BD)

---

## 🎯 Objetivo de la Prueba

**Cambiar** `cocina:false` → `cocina:true` **desde el panel admin**

Y verificar si se guarda correctamente en BD

---

## 📋 CHECKLIST PASO A PASO

### ☐ Paso 1: Preparar Monitor de Logs

**Terminal PowerShell:**
```powershell
cd C:\xampp\htdocs\fudo
.\ver_logs_permisos.bat
```

**Estado:** ☐ Terminal abierta y monitoreando

---

### ☐ Paso 2: Preparar DevTools del Navegador

1. Abrir Chrome/Edge
2. Presionar **F12**
3. Ir a pestaña **Console**
4. ✅ Activar checkbox **"Preserve log"** (arriba a la derecha)
5. Limpiar consola (icono 🚫)

**Estado:** ☐ Console abierta y configurada

---

### ☐ Paso 3: Login en el Sistema

```
URL: http://localhost/fudo
Usuario: admin_centro
Contraseña: (tu contraseña)
```

**Estado:** ☐ Logueado correctamente

---

### ☐ Paso 4: Editar Usuario

1. Clic en **"👥 Usuarios"** (navbar)
2. Buscar fila de **usuario_centro**
3. Clic en botón **"✏️ Editar"**
4. Modal se abre

**Estado:** ☐ Modal de edición abierto

---

### ☐ Paso 5: Cambiar Permisos

En el modal de edición:

1. Scrollear hasta sección **"🔐 Permisos de Acceso"**
2. Buscar checkbox **"👨‍🍳 Cocina"**
3. ✅ **MARCAR** el checkbox (activarlo)
4. Verificar visualmente que quede marcado ✅

**Estado:** ☐ Checkbox "Cocina" marcado

---

### ☐ Paso 6: Guardar Cambios

1. Clic en botón **"💾 Guardar Cambios"** (abajo del modal)
2. Esperar...

**Observar:**

#### En Console (F12):
```javascript
=== DEBUG EDITAR USUARIO ===
ID Usuario: ?
Rol: ?
Permisos enviados: {
  pedidos: "?",
  cocina: "?"  ← ¿Está presente?
}
============================
```

**Anotar:** ☐ ¿Aparece `cocina` en el objeto?

#### En Terminal PowerShell:
```
=== EDITAR USUARIO DEBUG ===
Usuario editado: usuario_centro
Permisos POST: Array(...)
JSON generado: {...}
===========================
```

**Anotar:** ☐ ¿Aparece el log?

#### Toast (notificación):
```
✅ Usuario actualizado exitosamente. Los cambios de permisos...
```

**Anotar:** ☐ ¿Apareció el toast de éxito?

---

### ☐ Paso 7: Verificar en Base de Datos

**Ejecutar en PostgreSQL:**
```sql
SELECT usuario, permisos 
FROM usuarios_admin 
WHERE usuario = 'usuario_centro';
```

**Resultado esperado:**
```json
{"pedidos":true,"mesas":true,"cocina":true,...}
                              ^^^^
```

**Anotar:** ☐ ¿`cocina:true` o sigue en `cocina:false`?

---

## 📤 RESULTADOS A ENVIAR

### 1️⃣ Console del Navegador (F12)

**Copiar este bloque:**
```
=== DEBUG EDITAR USUARIO ===
[PEGA AQUÍ EL CONTENIDO]
```

### 2️⃣ Logs de PowerShell

**Copiar este bloque:**
```
=== EDITAR USUARIO DEBUG ===
[PEGA AQUÍ EL CONTENIDO]
```

### 3️⃣ Resultado de PostgreSQL

**Copiar:**
```json
[PEGA AQUÍ EL JSON COMPLETO]
```

### 4️⃣ Observaciones Adicionales

- ¿Apareció el toast de éxito? ☐ Sí / ☐ No
- ¿El modal se cerró automáticamente? ☐ Sí / ☐ No
- ¿Hubo algún error visible? ☐ Sí / ☐ No

---

## 🔍 POSIBLES ESCENARIOS

### ✅ ESCENARIO A: Todo funciona
- Console muestra `cocina: "1"`
- PowerShell muestra JSON con `"cocina":true`
- Toast de éxito aparece
- BD tiene `"cocina":true`

**Conclusión:** El problema ya está resuelto! 🎉

---

### ⚠️ ESCENARIO B: Logs OK pero BD no cambia
- Console muestra `cocina: "1"`
- PowerShell muestra JSON correcto
- Toast aparece
- **PERO** BD sigue con `"cocina":false`

**Conclusión:** Problema en `Usuario_model->actualizar()`

---

### ❌ ESCENARIO C: Permisos NO se envían
- Console muestra `Permisos enviados: {}`
- PowerShell muestra "SIN PERMISOS"
- BD no cambia

**Conclusión:** FormData no captura checkboxes

---

### 🚫 ESCENARIO D: No aparecen logs
- Console vacía (sin logs)
- PowerShell sin logs
- BD no cambia

**Conclusión:** JavaScript no se ejecuta o método PHP no se llama

---

## 🎯 Checklist Final

Antes de enviar resultados, verificar:

- [ ] ✅ Monitor de logs estaba corriendo
- [ ] ✅ Console (F12) estaba abierta
- [ ] ✅ "Preserve log" activado
- [ ] ✅ Checkbox "Cocina" marcado antes de guardar
- [ ] ✅ Toast de éxito apareció
- [ ] ✅ SELECT ejecutado después de guardar

---

**¡Ejecuta la prueba y envíame los 3 resultados!** 🚀

Con esa información sabré exactamente dónde está el problema.
