<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quickad — Migration Placeholder</title>
    <style>
        body{font-family:system-ui,sans-serif;max-width:640px;margin:4rem auto;padding:0 1rem;color:#222}
        code{background:#f4f4f4;padding:2px 6px;border-radius:4px}
        .badge{display:inline-block;padding:2px 8px;background:#eee;border-radius:4px;font-size:.75rem}
    </style>
</head>
<body>
    <span class="badge">MIGRATION PLACEHOLDER</span>
    <h1>{{ $legacy ?? 'unknown' }} → <em>{{ $action ?? 'index' }}</em></h1>
    <p>Route hit successfully. Legacy source: <code>php/{{ $legacy }}</code>.</p>
    <p>Business logic will be ported here in <strong>Phase 4</strong> of the migration.</p>
    <p>See <code>MIGRATION_PLAN.md</code> at the repo root for the roadmap.</p>
</body>
</html>
