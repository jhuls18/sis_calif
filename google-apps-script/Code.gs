/**
 * Vuela Internet — puente Google Sheets
 * 1. Cree un Google Sheet con pestañas: Calificaciones, Usuarios, Clientes, Logins, Config
 * 2. Extensiones > Apps Script > pegue este archivo
 * 3. Implementar > Aplicación web
 *    - Ejecutar como: Yo
 *    - Quién tiene acceso: Cualquiera
 * 4. Copie la URL /exec en Configuración del sistema
 */

function doGet(e) {
  return json_({ ok: true, app: 'vuela-sis-calif' });
}

function doPost(e) {
  try {
    var body = {};
    if (e.postData && e.postData.contents) {
      body = JSON.parse(e.postData.contents);
    }
    var action = body.action || 'saveRating';
    var ss = SpreadsheetApp.getActiveSpreadsheet();

    if (action === 'saveRating') {
      append_(ss, 'Calificaciones', body.row || {});
      return json_({ ok: true });
    }
    if (action === 'saveUser') {
      append_(ss, 'Usuarios', body.row || {});
      return json_({ ok: true });
    }
    if (action === 'deleteUser') {
      return json_({ ok: true, note: 'Marque inactivo en la hoja Usuarios si lo desea.' });
    }
    if (action === 'loginLog') {
      append_(ss, 'Logins', {
        fecha: body.fecha, usuario: body.usuario, nombre: body.nombre,
        rol: body.rol, ip: body.ip
      });
      return json_({ ok: true });
    }
    if (action === 'saveConfig') {
      var sh = sheet_(ss, 'Config');
      sh.clear();
      sh.appendRow(['clave', 'valor']);
      var cfg = body.config || {};
      Object.keys(cfg).forEach(function (k) {
        sh.appendRow([k, typeof cfg[k] === 'object' ? JSON.stringify(cfg[k]) : cfg[k]]);
      });
      return json_({ ok: true });
    }
    if (action === 'getRatings') {
      return json_({ ok: true, ratings: readObjects_(ss, 'Calificaciones') });
    }
    return json_({ ok: false, error: 'Acción desconocida' });
  } catch (err) {
    return json_({ ok: false, error: String(err) });
  }
}

function sheet_(ss, name) {
  var sh = ss.getSheetByName(name);
  if (!sh) sh = ss.insertSheet(name);
  return sh;
}

function append_(ss, name, row) {
  var sh = sheet_(ss, name);
  var keys = Object.keys(row);
  if (sh.getLastRow() === 0) {
    sh.appendRow(keys);
  } else {
    var header = sh.getRange(1, 1, 1, sh.getLastColumn()).getValues()[0];
    keys.forEach(function (k) {
      if (header.indexOf(k) === -1) {
        sh.getRange(1, header.length + 1).setValue(k);
        header.push(k);
      }
    });
  }
  var header2 = sh.getRange(1, 1, 1, sh.getLastColumn()).getValues()[0];
  var line = header2.map(function (k) { return row[k] != null ? row[k] : ''; });
  sh.appendRow(line);
}

function readObjects_(ss, name) {
  var sh = sheet_(ss, name);
  var values = sh.getDataRange().getValues();
  if (values.length < 2) return [];
  var header = values[0];
  var out = [];
  for (var i = 1; i < values.length; i++) {
    var o = {};
    for (var c = 0; c < header.length; c++) o[header[c]] = values[i][c];
    out.push(o);
  }
  return out;
}

function json_(obj) {
  return ContentService.createTextOutput(JSON.stringify(obj))
    .setMimeType(ContentService.MimeType.JSON);
}
