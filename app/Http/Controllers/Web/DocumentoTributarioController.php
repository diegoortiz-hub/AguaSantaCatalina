<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\DocumentoTributarioMail;
use App\Models\DocumentoTributario;
use App\Models\Order;
use App\Services\Tributario\EmisorDocumentos;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Boletas y facturas.
 *
 * El sistema no emite documentos tributarios: los registra. Quien los emite es
 * el contribuyente ante el SII, con certificado digital y folios CAF. Acá se
 * anota qué documento corresponde a cada pedido, con qué folio salió, y se
 * guarda el PDF para poder reenviarlo.
 */
class DocumentoTributarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = DocumentoTributario::with('order');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('mes')) {
            $query->delMes($request->mes);
        }
        if ($request->filled('q')) {
            $texto = $request->q;
            $query->where(function ($q) use ($texto) {
                $q->where('folio', $texto)
                    ->orWhereHas('order', fn ($o) => $o
                        ->where('nombre_cliente', 'like', "%{$texto}%")
                        ->orWhere('email_cliente', 'like', "%{$texto}%")
                        ->orWhere('razon_social', 'like', "%{$texto}%")
                        ->orWhere('rut_receptor', 'like', "%{$texto}%")
                        ->orWhere('id', $texto));
            });
        }

        $documentos = $query->latest('id')->paginate(25)->withQueryString();

        $conteos = [
            'todos'     => DocumentoTributario::count(),
            'pendiente' => DocumentoTributario::where('estado', 'pendiente')->count(),
            'emitido'   => DocumentoTributario::where('estado', 'emitido')->count(),
            'anulado'   => DocumentoTributario::where('estado', 'anulado')->count(),
        ];

        // Pedidos a los que nunca se les registró documento. Pasa si el emisor
        // falló al crear el pedido, y son los que no hay que perder de vista.
        $sinDocumento = Order::doesntHave('documentos')
            ->where('estado', '!=', 'cancelado')
            ->latest()
            ->limit(20)
            ->get();

        // Totales del mes para cuadrar con el libro de ventas.
        $mes = $request->input('mes', now()->format('Y-m'));
        $delMes = DocumentoTributario::emitidos()->delMes($mes)->get();

        $resumenMes = [
            'mes'      => $mes,
            'cantidad' => $delMes->count(),
            'neto'     => (float) $delMes->sum('neto'),
            'iva'      => (float) $delMes->sum('iva'),
            'total'    => (float) $delMes->sum('total'),
            'boletas'  => $delMes->where('tipo', 'boleta')->count(),
            'facturas' => $delMes->where('tipo', 'factura')->count(),
        ];

        return view('admin.documentos.index', compact('documentos', 'conteos', 'sinDocumento', 'resumenMes'));
    }

    public function show(DocumentoTributario $documento): View
    {
        $documento->load('order.items');

        return view('admin.documentos.show', compact('documento'));
    }

    /** Registra el documento que le falta a un pedido. */
    public function store(Order $order, EmisorDocumentos $emisor): RedirectResponse
    {
        $documento = $emisor->emitir($order);

        return redirect()
            ->route('admin.documentos.show', $documento)
            ->with('success', 'Documento registrado para el pedido #'.$order->id.'.');
    }

    /**
     * Registra folio, fecha y PDF, y lo marca como emitido.
     *
     * El folio es obligatorio para marcar emitido: un documento emitido sin
     * folio no existe para el SII, y dejarlo pasar haría que el libro de ventas
     * cuadre con algo que no se declaró.
     */
    public function update(Request $request, DocumentoTributario $documento): RedirectResponse
    {
        if ($documento->estado === 'anulado') {
            return back()->withErrors(['folio' => 'Este documento está anulado. Registra uno nuevo en su lugar.']);
        }

        $datos = $request->validate([
            'tipo'          => ['required', Rule::in(array_keys(DocumentoTributario::TIPOS))],
            'folio'         => [
                'required', 'integer', 'min:1',
                Rule::unique('documentos_tributarios', 'folio')
                    ->where('tipo', $request->input('tipo'))
                    ->ignore($documento->id),
            ],
            'fecha_emision' => ['required', 'date', 'before_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'pdf'           => ['nullable', 'file', 'mimetypes:application/pdf', 'max:4096'],
            'xml'           => ['nullable', 'file', 'max:2048'],
            'avisar'        => ['nullable', 'boolean'],
        ], [], [
            'folio'         => 'folio',
            'fecha_emision' => 'fecha de emisión',
        ]);

        $documento->fill([
            'tipo'          => $datos['tipo'],
            'folio'         => $datos['folio'],
            'fecha_emision' => $datos['fecha_emision'],
            'observaciones' => $datos['observaciones'] ?? null,
            'estado'        => 'emitido',
        ]);

        if ($request->hasFile('pdf')) {
            $documento->pdf_path = $request->file('pdf')->store('documentos', 'public');
        }
        if ($request->hasFile('xml')) {
            $documento->xml_path = $request->file('xml')->store('documentos', 'public');
        }

        $documento->save();

        $aviso = 'Documento registrado como emitido.';

        if ($request->boolean('avisar')) {
            try {
                Mail::to($documento->order->email_cliente)
                    ->send(new DocumentoTributarioMail($documento->load('order')));
                $aviso .= ' Se envió por correo a '.$documento->order->email_cliente.'.';
            } catch (\Throwable $e) {
                report($e);
                $aviso .= ' No se pudo enviar el correo, revisa la configuración de envío.';
            }
        }

        return redirect()->route('admin.documentos.show', $documento)->with('success', $aviso);
    }

    public function anular(Request $request, DocumentoTributario $documento): RedirectResponse
    {
        $datos = $request->validate([
            'motivo' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        // El registro no se borra: se marca. Un documento anulado sigue siendo
        // parte del histórico y el SII lo sabe.
        $documento->update([
            'estado'        => 'anulado',
            'anulado_at'    => now(),
            'observaciones' => trim(($documento->observaciones ? $documento->observaciones."\n" : '')
                .'Anulado: '.$datos['motivo']),
        ]);

        return back()->with('success', 'Documento anulado. Registra el reemplazo si corresponde.');
    }

    /** Reenvía el documento al correo del cliente. */
    public function reenviar(DocumentoTributario $documento): RedirectResponse
    {
        if (! $documento->estaEmitido()) {
            return back()->withErrors(['reenviar' => 'Sólo se puede enviar un documento ya emitido.']);
        }

        try {
            Mail::to($documento->order->email_cliente)
                ->send(new DocumentoTributarioMail($documento->load('order')));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['reenviar' => 'No se pudo enviar el correo. Revisa la configuración de envío.']);
        }

        return back()->with('success', 'Documento enviado a '.$documento->order->email_cliente.'.');
    }

    /**
     * Libro de ventas del mes en CSV.
     *
     * Es lo que el contador pide todos los meses. Sólo incluye documentos
     * emitidos: lo pendiente y lo anulado no se declara.
     */
    public function libroVentas(Request $request): StreamedResponse
    {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes    = $datos['mes'] ?? now()->format('Y-m');
        $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();

        return response()->streamDownload(function () use ($mes) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, [
                'Tipo', 'Folio', 'Fecha', 'RUT receptor', 'Razón social',
                'Neto', 'IVA', 'Exento', 'Total', 'Pedido',
            ], ';');

            DocumentoTributario::with('order')
                ->emitidos()
                ->delMes($mes)
                ->orderBy('tipo')
                ->orderBy('folio')
                ->chunk(500, function ($documentos) use ($salida) {
                    foreach ($documentos as $doc) {
                        fputcsv($salida, [
                            $doc->etiquetaTipo(),
                            $doc->folio,
                            $doc->fecha_emision?->format('Y-m-d'),
                            $doc->order->rut_receptor,
                            $doc->order->razon_social,
                            number_format((float) $doc->neto, 0, ',', ''),
                            number_format((float) $doc->iva, 0, ',', ''),
                            number_format((float) $doc->exento, 0, ',', ''),
                            number_format((float) $doc->total, 0, ',', ''),
                            $doc->order_id,
                        ], ';');
                    }
                });

            fclose($salida);
        }, "libro-ventas-{$inicio->format('Y-m')}.csv", [
            'Content-Type'  => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /** Descarga el PDF guardado, sin exponer la ruta del disco. */
    public function descargarPdf(DocumentoTributario $documento)
    {
        abort_unless($documento->pdf_path && Storage::disk('public')->exists($documento->pdf_path), 404);

        return Storage::disk('public')->download(
            $documento->pdf_path,
            str_replace(' ', '-', strtolower($documento->numero())).'.pdf'
        );
    }
}
