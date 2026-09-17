<?php

namespace App\Mail;

use App\Models\DocumentoTributario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DocumentoTributarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DocumentoTributario $documento) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->documento->numero().' — Aguas Santa Catalina',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.documento-tributario');
    }

    /**
     * El PDF va adjunto si está guardado. Si no, el correo igual sirve: avisa
     * el folio y el monto, que es lo que el cliente necesita para su propia
     * contabilidad mientras se le hace llegar el archivo.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $ruta = $this->documento->pdf_path;

        if (! $ruta || ! Storage::disk('public')->exists($ruta)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('public', $ruta)
                ->as(str_replace(' ', '-', strtolower($this->documento->numero())).'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
