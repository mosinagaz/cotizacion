<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>API de Cotizaciones · UFV y Dólar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="API REST de cotizaciones diarias: UFV y dólar. Consulta por día, mes y año.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg: #0b1220;
            --bg-soft: #111827;
            --card: #ffffff;
            --muted: #64748b;
            --text: #0f172a;
            --line: #e2e8f0;
            --accent: #2563eb;
            --accent-soft: #eff6ff;
            --ok: #059669;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "Instrument Sans", system-ui, sans-serif;
            background: #f1f5f9;
            color: var(--text);
            margin: 0;
        }

        header {
            background:
                radial-gradient(circle at top right, #1d4ed8 0%, transparent 40%),
                linear-gradient(160deg, #0b1220, #111827);
            color: #fff;
            padding: 2.5rem 1.25rem 3rem;
        }

        .wrap {
            max-width: 1080px;
            margin: 0 auto;
        }

        header p.lead {
            max-width: 42rem;
            color: #cbd5e1;
            line-height: 1.6;
            margin: 0.75rem 0 0;
        }

        .pills {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        .pill {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            color: #e2e8f0;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.85rem;
        }

        main {
            max-width: 1080px;
            margin: -2rem auto 2rem;
            padding: 0 1.25rem 2rem;
        }

        .quotes {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .quote {
            background: var(--card);
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        .quote h2 {
            font-size: 0.85rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--muted);
            margin: 0 0 0.5rem;
        }

        .quote .value {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .quote .pair {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .quote .pair .label {
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .quote .meta {
            color: var(--muted);
            font-size: 0.9rem;
            margin-top: 0.35rem;
        }

        .history {
            overflow-x: auto;
        }

        .history table {
            margin-top: 0.25rem;
        }

        .num { font-variant-numeric: tabular-nums; }

        section.card {
            background: var(--card);
            border-radius: 14px;
            padding: 1.5rem;
            margin-top: 1.25rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
        }

        h3 { margin: 0 0 0.85rem; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 0.7rem 0.5rem;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        th { color: var(--muted); font-size: 0.8rem; font-weight: 600; }

        code, pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 0.85rem;
        }

        code {
            background: #f8fafc;
            border: 1px solid var(--line);
            padding: .15rem .4rem;
            border-radius: 6px;
        }

        pre {
            background: #0b1220;
            color: #e2e8f0;
            padding: 1rem;
            border-radius: 10px;
            overflow: auto;
        }

        .try {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .try input, .try select, .try button {
            font: inherit;
            padding: 0.7rem 0.85rem;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        .try input { flex: 1; min-width: 220px; }

        .try button {
            background: var(--accent);
            color: #fff;
            border: 0;
            font-weight: 600;
            cursor: pointer;
        }

        .hint { color: var(--muted); font-size: 0.9rem; }

        footer {
            text-align: center;
            color: var(--muted);
            padding: 2rem 1rem;
            font-size: .9rem;
        }

        @media (max-width: 800px) {
            .quotes { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <header>
        <div class="wrap">
            <h1>API de Cotizaciones</h1>
            <p class="lead">
                API REST para consultar indicadores económicos diarios de Bolivia:
                UFV y dólar. Lectura pública; escritura protegida con API Key.
            </p>
            <div class="pills">
                <span class="pill">Base: {{ $baseUrl }}</span>
                <span class="pill">JSON</span>
                <span class="pill">Laravel 12</span>
            </div>
        </div>
    </header>

    <main>
        <div class="quotes">
            <article class="quote">
                <h2>UFV</h2>
                @if ($ufv)
                    <div class="value">{{ number_format((float) $ufv->valor, 5) }}</div>
                    <div class="meta">{{ $ufv->fecha }}</div>
                @else
                    <div class="value">—</div>
                    <div class="meta">Sin registros</div>
                @endif
            </article>

            <article class="quote">
                <h2>Dólar</h2>
                @if ($dolar)
                    <div class="pair">
                        <div>
                            <div class="label">Compra</div>
                            <div class="value">{{ number_format((float) $dolar->precio_compra, 2) }}</div>
                        </div>
                        <div>
                            <div class="label">Venta</div>
                            <div class="value">{{ number_format((float) $dolar->precio_venta, 2) }}</div>
                        </div>
                    </div>
                    <div class="meta">{{ $dolar->fecha }}</div>
                @else
                    <div class="value">—</div>
                    <div class="meta">Sin registros</div>
                @endif
            </article>
        </div>

        <section class="card">
            <h3>Histórico · últimos 10 días</h3>
            @if ($historico->isEmpty())
                <p class="hint">Sin registros.</p>
            @else
                <div class="history">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>UFV</th>
                                <th>Dólar compra</th>
                                <th>Dólar venta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historico as $dia)
                                <tr>
                                    <td>{{ $dia['fecha'] }}</td>
                                    <td class="num">{{ $dia['ufv'] !== null ? number_format((float) $dia['ufv'], 5) : '—' }}</td>
                                    <td class="num">{{ $dia['compra'] !== null ? number_format((float) $dia['compra'], 2) : '—' }}</td>
                                    <td class="num">{{ $dia['venta'] !== null ? number_format((float) $dia['venta'], 2) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <h3>Probar un endpoint</h3>
            <p class="hint">GET público. Elige un recurso y ejecuta la consulta contra esta misma API.</p>
            <div class="try">
                <select id="preset">
                    <option value="/api/ufv?per_page=5">UFV · últimos</option>
                    <option value="/api/ufv/{{ now()->toDateString() }}">UFV · hoy</option>
                    <option value="/api/ufv/get-month/{{ now()->format('Y-m') }}">UFV · mes</option>
                    <option value="/api/dolar?per_page=5">Dólar · últimos</option>
                    <option value="/api/dolar/{{ now()->toDateString() }}">Dólar · hoy</option>
                </select>
                <input id="path" value="/api/ufv?per_page=5" aria-label="Ruta a consultar">
                <button type="button" id="run">Consultar</button>
            </div>
            <pre id="out" style="margin-top:1rem;">// El resultado aparecerá aquí</pre>
        </section>

        <section class="card">
            <h3>Endpoints de lectura</h3>
            <table>
                <thead>
                    <tr>
                        <th>Método</th>
                        <th>Ruta</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['ufv' => 'UFV', 'dolar' => 'Dólar'] as $slug => $label)
                        <tr>
                            <td><code>GET</code></td>
                            <td><a href="{{ url('/api/'.$slug.'?per_page=30') }}">/api/{{ $slug }}?per_page=30</a></td>
                            <td>Listado paginado de {{ $label }} (máx. 100)</td>
                        </tr>
                        <tr>
                            <td><code>GET</code></td>
                            <td>/api/{{ $slug }}/{YYYY-MM-DD}</td>
                            <td>Valor de un día</td>
                        </tr>
                        <tr>
                            <td><code>GET</code></td>
                            <td>/api/{{ $slug }}/get-month/{YYYY-MM}</td>
                            <td>Valores de un mes</td>
                        </tr>
                        <tr>
                            <td><code>GET</code></td>
                            <td>/api/{{ $slug }}/get-year/{YYYY}</td>
                            <td>Valores de un año</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="card">
            <h3>Ejemplo de respuesta</h3>
            <pre>{
  "fecha": "2026-08-13",
  "valor": 3.29380
}</pre>
            <p class="hint">El dólar usa <code>precio_compra</code> y <code>precio_venta</code> en lugar de <code>valor</code>.</p>
        </section>

        <section class="card">
            <h3>Escritura (API Key)</h3>
            <p>POST, PUT/PATCH y DELETE requieren el encabezado:</p>
            <pre>X-API-KEY: ********</pre>
            <p class="hint">La lectura (GET) no requiere autenticación.</p>
        </section>

        <section class="card">
            <h3>Documentación</h3>
            <p>
                Detalle de validaciones, inserción masiva y convenciones en
                <a href="https://github.com/DRiberaC/cotizaciones" target="_blank" rel="noopener">GitHub</a>
                (<strong>README.md</strong>).
            </p>
        </section>
    </main>

    <footer>API de Cotizaciones</footer>

    <script>
        const preset = document.getElementById('preset');
        const path = document.getElementById('path');
        const out = document.getElementById('out');
        const run = document.getElementById('run');

        preset.addEventListener('change', () => { path.value = preset.value; });

        run.addEventListener('click', async () => {
            const url = path.value.startsWith('http') ? path.value : window.location.origin + path.value;
            out.textContent = 'Consultando…';
            try {
                const res = await fetch(url);
                const text = await res.text();
                try {
                    out.textContent = JSON.stringify(JSON.parse(text), null, 2);
                } catch {
                    out.textContent = text;
                }
            } catch (err) {
                out.textContent = String(err);
            }
        });
    </script>
</body>

</html>
