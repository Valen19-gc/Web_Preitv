<?php
// informe_itv_submission.php

// 1. Conexión a la base de datos
$servername = "192.168.33.11";
$username   = "root";
$password   = "agl123";
$dbname     = "preitv";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    // --- SECTION A: Identificación ---
    $secA = [
        'codigo_estacion','clasificacion_vehiculo','categoria','lineas','lectura_cuentakm',
        'marca_modelo','numero_bastidor','matricula_actual','tipo_vehiculo',
        'contrasena_homologacion','informe_itv','fecha_primera_matriculacion',
        'fecha_inspeccion','fecha_proxima_inspeccion','numero_factura','tarifa',
        'tipo_inspeccion'
    ];
    $valsA = [];
    foreach ($secA as $field) {
        $valsA[$field] = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : null;
    }
    $phA = implode(',', array_fill(0, count($secA), '?'));
    $stmtA = $conn->prepare("INSERT INTO informe_a (" . implode(',', $secA) . ") VALUES ($phA)");
    // bind params
    $typesA = str_repeat('s', count($secA));
    $bindA = [$typesA];
    foreach ($valsA as &$v) {
        $bindA[] = &$v;
    }
    call_user_func_array([$stmtA, 'bind_param'], $bindA);
    $stmtA->execute();
    $informe_id = $stmtA->insert_id;
    $stmtA->close();

    // --- SECTION B: Alcance y trazabilidad ---
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'item_') === 0) {
            $code = htmlspecialchars($k);
            $val  = htmlspecialchars(trim($v));
            $stmtB = $conn->prepare(
                "INSERT INTO informe_b (informe_id, item_codigo, item_valor) VALUES (?, ?, ?)"
            );
            $stmtB->bind_param('iss', $informe_id, $code, $val);
            $stmtB->execute();
            $stmtB->close();
        }
    }

    // --- SECTION C: Pruebas generales ---
    $secC = ['emisiones','frenado','alineacion','velocidad','ruidos','dinamometro','bascula','otros'];
    $valsC = [];
    foreach ($secC as $field) {
        $valsC[$field] = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : null;
    }
    $phC = implode(',', array_fill(0, count($secC), '?'));
    $stmtC = $conn->prepare(
        "INSERT INTO informe_c (informe_id, " . implode(',', $secC) . ") VALUES (?,$phC)"
    );
    $typesC = 'i' . str_repeat('s', count($secC));
    $bindC = [$typesC, &$informe_id];
    foreach ($valsC as &$v) {
        $bindC[] = &$v;
    }
    call_user_func_array([$stmtC, 'bind_param'], $bindC);
    $stmtC->execute();
    $stmtC->close();

    // --- SECTION D: Pruebas específicas ---
    $secD = [
        'opacidad','co_ralenti','co_ralenti_acelerado','lambda',
        'alineacion1','alineacion2','alineacion3','alineacion4','limite_velocidad'
    ];
    $valsD = [];
    foreach ($secD as $field) {
        $valsD[$field] = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : null;
    }
    $phD = implode(',', array_fill(0, count($secD), '?'));
    $stmtD = $conn->prepare(
        "INSERT INTO informe_d (informe_id, " . implode(',', $secD) . ") VALUES (?,$phD)"
    );
    $typesD = 'i' . str_repeat('s', count($secD));
    $bindD = [$typesD, &$informe_id];
    foreach ($valsD as &$v) {
        $bindD[] = &$v;
    }
    call_user_func_array([$stmtD, 'bind_param'], $bindD);
    $stmtD->execute();
    $stmtD->close();

    // --- SECTION E: Firma y datos finales ---
    $signature = isset($_POST['signature']) ? $_POST['signature'] : null;
    if ($signature && strpos($signature, 'data:image') === 0) {
        $parts = explode(',', $signature);
        $signature = base64_decode(end($parts));
    }
    $secE = ['NITV','fecha','hora','fecha_hora_completa'];
    $valsE = [];
    foreach ($secE as $field) {
        $valsE[$field] = isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field])) : null;
    }
    $self_repair = isset($_POST['self-repair']) ? 1 : 0;

    $phE = implode(',', array_fill(0, count($secE), '?'));
    $stmtE = $conn->prepare(
        "INSERT INTO informe_e (informe_id, signature, " . implode(',', $secE) . ", self_repair) VALUES (?, ?, $phE, ?)"
    );
    $typesE = 'ib' . str_repeat('s', count($secE)) . 'i';
    $bindE = [$typesE, &$informe_id, &$signature];
    foreach ($valsE as &$v) {
        $bindE[] = &$v;
    }
    $bindE[] = &$self_repair;
    call_user_func_array([$stmtE, 'bind_param'], $bindE);
    $stmtE->send_long_data(1, $signature);
    $stmtE->execute();
    $stmtE->close();

    // Cerramos y redirigimos
    $conn->close();
    header('Location: success.php?id=' . $informe_id);
    exit;
}

http_response_code(405);
echo 'Method Not Allowed';
