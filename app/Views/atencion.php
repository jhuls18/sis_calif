<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
  <div>
    <h2 class="fw-bold mb-0" style="color:var(--vuela)"><i class="fa-solid fa-headset me-2"></i>Herramientas de Atención Vuela</h2>
    <p class="text-muted mb-0">Calculadora de permanencia (18M y 12M Excepcional), protocolo de soluciones y retención.</p>
  </div>
  <a class="btn btn-vuela" href="<?= e(url('/calificar')) ?>">
    <i class="fa-solid fa-face-smile me-2"></i>Ir a Calificar
  </a>
</div>

<!-- Selector de Tipo de Contrato: 18 Meses vs 12 Meses Excepcional -->
<div class="card card-vuela p-3 mb-4 bg-white border shadow-sm">
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-file-contract fs-4 text-vuela"></i>
      <div>
        <h5 class="fw-bold text-dark mb-0">Modalidad de Contrato a Evaluar</h5>
        <small class="text-muted">Seleccione la permanencia estipulada en el contrato suscrito por el cliente.</small>
      </div>
    </div>
    <div class="btn-group rounded-pill p-1 bg-light border" role="group" id="contractTypeGroup">
      <input type="radio" class="btn-check" name="contract_type" id="contract18" value="18" checked autocomplete="off">
      <label class="btn btn-sm btn-vuela rounded-pill px-3 py-2 fw-bold" for="contract18">
        <i class="fa-solid fa-shield-check me-1"></i> Contrato Estándar (18 Meses)
      </label>

      <input type="radio" class="btn-check" name="contract_type" id="contract12" value="12" autocomplete="off">
      <label class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-bold" for="contract12">
        <i class="fa-solid fa-star me-1"></i> Contrato Excepcional (12 Meses)
      </label>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Calculadora de Penalidad de Permanencia (18M o 12M) -->
  <div class="col-lg-6">
    <div class="card card-vuela p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="fa-solid fa-calculator fs-3 text-warning"></i>
          <h4 class="fw-bold mb-0">Calculadora de Penalidad</h4>
        </div>
        <span class="badge text-bg-warning fs-7 px-3 py-2 fw-bold">Bs. 159.00 / mes</span>
      </div>
      <p class="text-muted small mb-3">Seleccione las fechas de instalación, solicitud de baja y el periodo de bloqueo temporal para calcular automáticamente la multa de rescisión.</p>

      <!-- Fechas de Contrato -->
      <div class="p-3 bg-light border rounded-4 mb-3">
        <h6 class="fw-bold text-dark small mb-2"><i class="fa-solid fa-calendar-days text-primary me-1"></i> 1. Fechas del Contrato</h6>
        <div class="row g-2">
          <div class="col-6">
            <label class="form-label fw-bold fs-8 text-dark mb-1">Mes de Instalación</label>
            <input type="month" class="form-control form-control-sm fw-bold" id="calc-fecha-inicio" value="<?= date('Y-m', strtotime('-6 months')) ?>">
          </div>
          <div class="col-6">
            <label class="form-label fw-bold fs-8 text-dark mb-1">Mes de Baja Solicitada</label>
            <input type="month" class="form-control form-control-sm fw-bold" id="calc-fecha-fin" value="<?= date('Y-m') ?>">
          </div>
        </div>
      </div>

      <!-- Fechas de Bloqueo Temporal Solicitado -->
      <div class="p-3 bg-light border rounded-4 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="fw-bold text-dark small mb-0"><i class="fa-solid fa-snowflake text-info me-1"></i> 2. Periodo de Bloqueo Temporal (Si solicitó)</h6>
          <span class="badge text-bg-info text-white fs-8" id="lbl-bloqueo-meses-calc">0 meses de bloqueo</span>
        </div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <label class="form-label fw-bold fs-8 text-dark mb-1">Inicio de Bloqueo</label>
            <input type="month" class="form-control form-control-sm fw-bold" id="calc-bloqueo-inicio" value="<?= date('Y-m', strtotime('-6 months')) ?>">
          </div>
          <div class="col-6">
            <label class="form-label fw-bold fs-8 text-dark mb-1">Fin de Bloqueo</label>
            <input type="month" class="form-control form-control-sm fw-bold" id="calc-bloqueo-fin" value="<?= date('Y-m', strtotime('-6 months')) ?>">
          </div>
        </div>
        <small class="text-muted fs-8 d-block">* Los meses en bloqueo congelan el servicio y no cuentan como permanencia pagada.</small>
      </div>

      <!-- Ajuste Manual / Slider de Meses Activos -->
      <div class="mb-3">
        <label class="form-label fw-bold d-flex justify-content-between small text-dark mb-1">
          <span>Meses Activos Transcurridos:</span>
          <span class="badge text-bg-primary fs-7" id="calc-meses-val">6 meses</span>
        </label>
        <input type="range" class="form-range" id="calc-meses" min="0" max="18" value="6" step="1">
      </div>

      <!-- Botón para Ejecutar / Recalcular Penalidad -->
      <button type="button" class="btn btn-vuela btn-sm w-100 fw-bold py-2 shadow-sm mb-3" id="btn-calcular-multa">
        <i class="fa-solid fa-calculator me-1"></i> Calcular Penalidad y Meses Restantes
      </button>

      <!-- Caja de Resultados de Penalidad -->
      <div class="stat text-center bg-light border p-3 rounded-4" id="box-resultado-calc">
        <div class="row g-2 mb-2 text-muted small">
          <div class="col-6 border-end">Transcurridos: <strong id="lbl-transcurridos-total" class="text-dark">6 meses</strong></div>
          <div class="col-6">Bloqueo Deducido: <strong id="lbl-bloqueo-total" class="text-info">0 meses</strong></div>
        </div>
        <hr class="my-2">
        <div class="text-muted small fw-semibold">Meses Restantes para Cumplir Permanencia (<span id="lbl-contract-max-months">18M</span>)</div>
        <b id="calc-restantes" class="text-dark display-6 d-block my-1">12 meses</b>
        <div class="text-muted small fw-semibold">Penalidad Estimada por Rescisión Anticipada</div>
        <b id="calc-resultado" style="color:var(--vuela)" class="display-5 d-block">Bs. 1,908.00</b>
      </div>
    </div>
  </div>

  <!-- Checklist de Requisitos y Condiciones Principales del Contrato -->
  <div class="col-lg-6">
    <div class="card card-vuela p-4 h-100">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
          <i class="fa-solid fa-file-contract fs-3 text-success"></i>
          <h4 class="fw-bold mb-0">Condiciones Principales del Contrato</h4>
        </div>
        <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-3 py-1" id="btn-marcar-todos">
          <i class="fa-solid fa-check-double me-1"></i> Marcar Todas las Opciones
        </button>
      </div>
      <p class="text-muted small mb-3">Requisitos y condiciones contractuales de Vuela Internet a verificar antes de procesar cualquier baja:</p>

      <div class="list-group list-group-flush mb-4">
        <label class="list-group-item d-flex gap-3 align-items-center bg-transparent py-2 cursor-pointer">
          <input class="form-check-input flex-shrink-0 chk-condicion" type="checkbox" id="chk1" checked>
          <span>
            <strong class="text-dark small">1. Permanencia Obligatoria (<span id="chk-permanencia-lbl">18 Meses</span>):</strong>
            <small class="d-block text-muted fs-8">Todo contrato está sujeto a un periodo de fidelidad activo.</small>
          </span>
        </label>
        <label class="list-group-item d-flex gap-3 align-items-center bg-transparent py-2 cursor-pointer">
          <input class="form-check-input flex-shrink-0 chk-condicion" type="checkbox" id="chk2" checked>
          <span>
            <strong class="text-dark small">2. Penalidad por Rescisión Anticipada (Bs. 159/mes):</strong>
            <small class="d-block text-muted fs-8">Si el cliente solicita la baja antes del plazo, cancela la multa por mes faltante.</small>
          </span>
        </label>
        <label class="list-group-item d-flex gap-3 align-items-center bg-transparent py-2 cursor-pointer">
          <input class="form-check-input flex-shrink-0 chk-condicion" type="checkbox" id="chk3">
          <span>
            <strong class="text-dark small">3. Suspensión / Bloqueo Temporal (Máx. 2 Meses):</strong>
            <small class="d-block text-muted fs-8">Pausar el servicio hasta 2 meses por año sin multa (congelado no suma como permanencia).</small>
          </span>
        </label>
        <label class="list-group-item d-flex gap-3 align-items-center bg-transparent py-2 cursor-pointer">
          <input class="form-check-input flex-shrink-0 chk-condicion" type="checkbox" id="chk4">
          <span>
            <strong class="text-dark small">4. Devolución de Equipos en Comodato (ONU / Módem):</strong>
            <small class="d-block text-muted fs-8">Entregar equipo router/ONU y transformador en perfecto estado en oficina.</small>
          </span>
        </label>
        <label class="list-group-item d-flex gap-3 align-items-center bg-transparent py-2 cursor-pointer">
          <input class="form-check-input flex-shrink-0 chk-condicion" type="checkbox" id="chk5">
          <span>
            <strong class="text-dark small">5. Estado de Cuenta al Día y Titularidad (C.I.):</strong>
            <small class="d-block text-muted fs-8">Cédula del titular y saldo al día (0 Bs. pendientes).</small>
          </span>
        </label>
      </div>

      <div class="p-3 bg-light border rounded-4" id="evaluacion-baja-box">
        <h6 class="fw-bold text-dark small mb-1" id="evaluacion-baja-titulo"><i class="fa-solid fa-circle-info text-warning me-1"></i> Evaluador Operativo de Bajas</h6>
        <p class="small text-muted mb-0" id="evaluacion-baja-desc">Si el cliente cumple los meses de permanencia, la rescisión es gratuita previa devolución de equipos y C.I. del titular.</p>
      </div>
    </div>
  </div>
</div>

<!-- CUADRO COMPLETO DE SOLUCIONES, ASISTENCIAS, COMPROMISOS Y PROMOCIONES DE RETENCIÓN -->
<div class="card card-vuela p-4 mb-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-hand-holding-hand fs-3 text-vuela"></i>
      <h4 class="fw-bold mb-0">Cuadro General de Soluciones, Asistencias y Promociones</h4>
    </div>
    <span class="badge text-bg-success fs-7 px-3 py-2 fw-bold"><i class="fa-solid fa-shield-heart me-1"></i>Matriz Integrada de Retención</span>
  </div>
  <p class="text-muted small mb-3">Opciones de soporte técnico, planes, descuentos y compromisos para ofrecer al cliente antes de registrar la baja:</p>

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="border rounded-4 p-3 bg-light h-100">
        <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-wrench text-danger me-1"></i> 1. Soporte / Visita Técnica</div>
        <p class="fs-8 text-muted mb-2">Visita técnica gratuita, cambio de clave, reubicación de módem o revisión de fibra óptica.</p>
        <div class="form-check form-check-inline fs-8">
          <input class="form-check-input" type="checkbox" id="sol_soporte">
          <label class="form-check-label text-dark fw-bold" for="sol_soporte">Asistencia NOC Aplicada</label>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border rounded-4 p-3 bg-light h-100">
        <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-handshake text-warning me-1"></i> 2. Compromiso de Pago</div>
        <p class="fs-8 text-muted mb-2">Prórroga de fecha de pago sin corte para clientes con dificultad momentánea de pago.</p>
        <div class="form-check form-check-inline fs-8">
          <input class="form-check-input" type="checkbox" id="sol_compromiso">
          <label class="form-check-label text-dark fw-bold" for="sol_compromiso">Prórroga Registrada</label>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border rounded-4 p-3 bg-light h-100">
        <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-tags text-success me-1"></i> 3. Descuento / Plan Promocional</div>
        <p class="fs-8 text-muted mb-2">Descuento temporal (20%-50%), cambio a plan de menor tarifa o bonificación de velocidad.</p>
        <div class="form-check form-check-inline fs-8">
          <input class="form-check-input" type="checkbox" id="sol_descuento">
          <label class="form-check-label text-dark fw-bold" for="sol_descuento">Promoción / Descuento</label>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="border rounded-4 p-3 bg-light h-100">
        <div class="fw-bold text-dark mb-2"><i class="fa-solid fa-truck-ramp-box text-primary me-1"></i> 4. Traslado o Congelamiento</div>
        <p class="fs-8 text-muted mb-2">Mudar la instalación conservando la antigüedad o activar Bloqueo Temporal sin multa.</p>
        <div class="form-check form-check-inline fs-8">
          <input class="form-check-input" type="checkbox" id="sol_traslado">
          <label class="form-check-label text-dark fw-bold" for="sol_traslado">Traslado / Pausa 2M</label>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Plantillas de Respuesta Rápida (Protocolo CALMA) -->
<div class="card card-vuela p-4 mb-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <i class="fa-solid fa-comments fs-3" style="color:var(--vuela)"></i>
      <h4 class="fw-bold mb-0">Plantillas de Respuesta Rápida (Protocolo CALMA)</h4>
    </div>
    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small">Modelos de atención rápida</span>
  </div>
  <p class="text-muted small mb-3">Respuestas preconcebidas optimizadas para copiar y pegar directamente al chat con el usuario:</p>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-house-laptop me-1 text-primary"></i> 1. Mudanza / Viaje</h6>
            <span class="badge text-bg-primary fs-8">Baja</span>
          </div>
          <textarea id="tpl1" class="form-control form-control-sm text-muted bg-white mb-3" rows="4" readonly style="resize:none; font-size:0.82rem;">Estimado cliente, para procesar la baja por mudanza o viaje, se requiere apersonarse a nuestras oficinas con C.I. del titular y los equipos en comodato (módem y fuente). ¡Quedamos a su servicio!</textarea>
        </div>
        <button class="btn btn-vuela btn-sm w-100 fw-bold py-2" onclick="copyTpl('tpl1', this)">
          <i class="fa-solid fa-copy me-1"></i>Copiar Mensaje
        </button>
      </div>
    </div>

    <div class="col-md-4">
      <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-wrench me-1 text-danger"></i> 2. Derivación NOC</h6>
            <span class="badge text-bg-danger fs-8">Soporte</span>
          </div>
          <textarea id="tpl2" class="form-control form-control-sm text-muted bg-white mb-3" rows="4" readonly style="resize:none; font-size:0.82rem;">Estimado usuario, derivamos su caso al área técnica (NOC) para revisión prioritaria. Por favor mantenga el módem encendido durante la verificación remota. En breve le informaremos.</textarea>
        </div>
        <button class="btn btn-vuela btn-sm w-100 fw-bold py-2" onclick="copyTpl('tpl2', this)">
          <i class="fa-solid fa-copy me-1"></i>Copiar Mensaje
        </button>
      </div>
    </div>

    <div class="col-md-4">
      <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between">
        <div>
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-snowflake me-1 text-info"></i> 3. Bloqueo Temporal</h6>
            <span class="badge text-bg-info text-white fs-8">Alternativa</span>
          </div>
          <textarea id="tpl3" class="form-control form-control-sm text-muted bg-white mb-3" rows="4" readonly style="resize:none; font-size:0.82rem;">Estimado cliente, antes de cancelar su servicio y aplicar la multa por rescisión anticipada, le ofrecemos activar el Bloqueo Temporal sin costo de hasta 2 meses para conservar su contrato. ¿Desea aplicar este beneficio?</textarea>
        </div>
        <button class="btn btn-vuela btn-sm w-100 fw-bold py-2" onclick="copyTpl('tpl3', this)">
          <i class="fa-solid fa-copy me-1"></i>Copiar Mensaje
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  var targetContractMonths = 18;

  function calculateMonthDiff(d1, d2) {
    if (!d1 || !d2) return 0;
    var parts1 = d1.split('-');
    var parts2 = d2.split('-');
    var y1 = parseInt(parts1[0], 10);
    var m1 = parseInt(parts1[1], 10);
    var y2 = parseInt(parts2[0], 10);
    var m2 = parseInt(parts2[1], 10);
    var diff = (y2 - y1) * 12 + (m2 - m1);
    return Math.max(0, diff);
  }

  function areAllConditionsChecked() {
    var c1 = document.getElementById('chk1') ? document.getElementById('chk1').checked : false;
    var c2 = document.getElementById('chk2') ? document.getElementById('chk2').checked : false;
    var c3 = document.getElementById('chk3') ? document.getElementById('chk3').checked : false;
    var c4 = document.getElementById('chk4') ? document.getElementById('chk4').checked : false;
    var c5 = document.getElementById('chk5') ? document.getElementById('chk5').checked : false;
    return c1 && c2 && c4 && c5; // c3 is optional temporal block
  }

  function updateCalc(fromDates) {
    var c18 = document.getElementById('contract18');
    targetContractMonths = (c18 && c18.checked) ? 18 : 12;

    var lblMaxM = document.getElementById('lbl-contract-max-months');
    if (lblMaxM) lblMaxM.textContent = targetContractMonths + 'M';

    var chkPermLbl = document.getElementById('chk-permanencia-lbl');
    if (chkPermLbl) chkPermLbl.textContent = targetContractMonths + ' Meses';

    var slider = document.getElementById('calc-meses');
    if (slider) slider.max = targetContractMonths;

    var fInicio = document.getElementById('calc-fecha-inicio').value;
    var fFin = document.getElementById('calc-fecha-fin').value;
    
    var bInicio = document.getElementById('calc-bloqueo-inicio').value;
    var bFin = document.getElementById('calc-bloqueo-fin').value;

    var totalTranscurridos = calculateMonthDiff(fInicio, fFin);
    var bloqueoMeses = 0;
    
    if (bInicio && bFin) {
      bloqueoMeses = calculateMonthDiff(bInicio, bFin);
    }

    if (fromDates && slider) {
      var activosCalculados = Math.max(0, totalTranscurridos - bloqueoMeses);
      slider.value = Math.min(targetContractMonths, activosCalculados);
    }

    var mesesActivos = slider ? (parseInt(slider.value, 10) || 0) : 0;
    var restantes = Math.max(0, targetContractMonths - mesesActivos);
    var penalidad = restantes * 159;

    document.getElementById('lbl-bloqueo-meses-calc').textContent = bloqueoMeses + ' mes(es) de bloqueo';
    document.getElementById('calc-meses-val').textContent = mesesActivos + ' meses';
    document.getElementById('lbl-transcurridos-total').textContent = totalTranscurridos + ' meses';
    document.getElementById('lbl-bloqueo-total').textContent = bloqueoMeses + ' meses';
    document.getElementById('calc-restantes').textContent = restantes + ' meses';
    document.getElementById('calc-resultado').textContent = 'Bs. ' + penalidad.toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    var evalBox = document.getElementById('evaluacion-baja-box');
    var evalTitulo = document.getElementById('evaluacion-baja-titulo');
    var evalDesc = document.getElementById('evaluacion-baja-desc');
    var allChecked = areAllConditionsChecked();

    if (evalBox && evalTitulo && evalDesc) {
      if (restantes <= 0) {
        if (allChecked) {
          evalBox.className = 'p-3 bg-success-subtle border border-success rounded-4 bounce-in';
          evalTitulo.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i> Rescisión Gratuita Procedente (0 Bs.)';
          evalDesc.innerHTML = 'El cliente cumplió los <b>' + targetContractMonths + ' meses</b> de permanencia obligatoria y se verificaron todos los requisitos del contrato (Equipos + C.I. + Saldo al Día).';
        } else {
          evalBox.className = 'p-3 bg-info-subtle border border-info rounded-4 bounce-in';
          evalTitulo.innerHTML = '<i class="fa-solid fa-circle-info text-info me-1"></i> Permanencia Cumplida (Faltan Requisitos)';
          evalDesc.innerHTML = 'Fidelidad de <b>' + targetContractMonths + 'M</b> cumplida. Falta marcar devolución de equipos o C.I. del titular para autorizar la baja.';
        }
      } else {
        evalBox.className = 'p-3 bg-warning-subtle border border-warning rounded-4 bounce-in';
        evalTitulo.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Rescisión Anticipada Sujeta a Penalidad';
        evalDesc.innerHTML = 'Faltan <b>' + restantes + ' meses</b> para cumplir el contrato de ' + targetContractMonths + 'M. Multa a cobrar por rescisión anticipada: <strong class="text-danger">Bs. ' + penalidad.toFixed(2) + '</strong>.';
      }
    }
  }

  // Botón Marcar Todas las Opciones
  var btnMarcarTodos = document.getElementById('btn-marcar-todos');
  if (btnMarcarTodos) {
    btnMarcarTodos.addEventListener('click', function() {
      document.querySelectorAll('.chk-condicion').forEach(function(chk) {
        chk.checked = true;
      });
      updateCalc(false);
    });
  }

  document.querySelectorAll('.chk-condicion').forEach(function(chk) {
    chk.addEventListener('change', function() { updateCalc(false); });
  });

  document.querySelectorAll('input[name="contract_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      var c18Label = document.querySelector('label[for="contract18"]');
      var c12Label = document.querySelector('label[for="contract12"]');
      if (document.getElementById('contract18').checked) {
        c18Label.className = 'btn btn-sm btn-vuela rounded-pill px-3 py-2 fw-bold';
        c12Label.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-bold';
      } else {
        c12Label.className = 'btn btn-sm btn-vuela rounded-pill px-3 py-2 fw-bold';
        c18Label.className = 'btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-bold';
      }
      updateCalc(true);
    });
  });

  document.getElementById('calc-fecha-inicio').addEventListener('change', function() { updateCalc(true); });
  document.getElementById('calc-fecha-fin').addEventListener('change', function() { updateCalc(true); });
  document.getElementById('calc-bloqueo-inicio').addEventListener('change', function() { updateCalc(true); });
  document.getElementById('calc-bloqueo-fin').addEventListener('change', function() { updateCalc(true); });
  if (document.getElementById('calc-meses')) {
    document.getElementById('calc-meses').addEventListener('input', function() { updateCalc(false); });
  }
  
  var btnCalc = document.getElementById('btn-calcular-multa');
  if (btnCalc) {
    btnCalc.addEventListener('click', function() { updateCalc(true); });
  }

  updateCalc(true);

  function copyTpl(id, btn) {
    var txt = document.getElementById(id);
    txt.select();
    navigator.clipboard.writeText(txt.value).then(function() {
      var orig = btn.innerHTML;
      btn.innerHTML = '<i class="fa-solid fa-check me-1"></i>¡Copiado!';
      btn.classList.remove('btn-vuela');
      btn.classList.add('btn-success');
      setTimeout(function() {
        btn.innerHTML = orig;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-vuela');
      }, 1800);
    });
  }
</script>
