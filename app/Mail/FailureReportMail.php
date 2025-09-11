<?php

namespace App\Mail;

use App\Filament\Resources\FailureReportResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\FailureReport;
use App\Models\User;
use Illuminate\Mail\Mailables\Address;



class FailureReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $evento,                // 'creado' | 'reportado' | 'aprobado' | 'estado_cambiado'
        public FailureReport $reporte,
        public ?User $actor = null,           // quién ejecutó la acción
        public array $extra = []              // datos adicionales si los necesitas
    ){}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = FailureReportResource::getUrl(
        'view',
        ['record' => $this->reporte],
        panel: 'gema', // <-- ajusta si tu panel tiene otro ID
        );

        return new Content(
            markdown: "emails.failure-reports.{$this->evento}",
            with: [
                'reporte' => $this->reporte,
                'actor'   => $this->actor,
                'estado'  => $this->extra['estado'] ?? $this->reporte->reportFollowup?->nombre,
                'url'     => $url,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

     private function asunto(): string
    {
        $num = mb_strtoupper($this->reporte->numero_reporte ?? 'Reporte de Falla');
        $location = $this->reporte->location->nombre;
        return match ($this->evento) {
            'creado'         => "GEMA | Nuevo {$num} creado | {$location}",
            'reportado'      => "GEMA | {$num} remitido para revisión | {$location}",
            'rechazado'      => "GEMA | {$num} rechazado | {$location}",
            'aprobado'       => "GEMA | {$num} notificado a JPCM | {$location}",
            'estado_cambiado'=> "GEMA | {$num} cambio de estado | {$location}",
            default          => "GEMA | {$num} | {$location}",
        };
    }
}
