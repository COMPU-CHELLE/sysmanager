<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $document->document_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 720px; margin: 40px auto; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        td { border: 1px solid #cbd5e1; padding: 8px 12px; }
        td:first-child { background: #f1f5f9; width: 35%; font-weight: bold; }
        .firmas { display: flex; justify-content: space-between; margin-top: 90px; }
        .firmas div { border-top: 1px solid #0f172a; width: 40%; text-align: center; padding-top: 6px; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <button onclick="window.print()">Imprimir</button>
    <h1>Acta de entrega de activo</h1>
    <p>No. {{ $document->document_number }} &middot; {{ $document->generated_at->format('Y-m-d H:i') }}</p>
    <table>
        <tr><td>Empresa</td><td>{{ $assignment->asset->company->name }}</td></tr>
        <tr><td>Activo</td><td>{{ $assignment->asset->name }} ({{ $assignment->asset->type }})</td></tr>
        <tr><td>Marca / modelo</td><td>{{ $assignment->asset->brand }} {{ $assignment->asset->model }}</td></tr>
        <tr><td>Serial</td><td>{{ $assignment->asset->serial }}</td></tr>
        <tr><td>Empleado</td><td>{{ $assignment->employee->name }}</td></tr>
        <tr><td>Fecha de asignacion</td><td>{{ $assignment->assigned_at?->format('Y-m-d H:i') }}</td></tr>
        <tr><td>Fecha de devolucion</td><td>{{ $assignment->returned_at?->format('Y-m-d H:i') ?? 'Pendiente' }}</td></tr>
    </table>
    <div class="firmas"><div>Entrega</div><div>Recibe</div></div>
</body>
</html>