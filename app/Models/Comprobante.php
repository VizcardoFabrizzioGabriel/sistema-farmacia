<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';
    protected $primaryKey = 'id_comprobante';
    public $timestamps = true;

    protected $fillable = [
        'id_venta',
        'tipo',
        'numero_serie',
        'url_pdf',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    // Método: generar número de serie
    public static function generarNumeroSerie($tipo)
    {
        $prefijo = $tipo === 'Factura' ? 'F' : 'B';
        $numero = str_pad(static::count() + 1, 8, '0', STR_PAD_LEFT);
        return $prefijo . '-' . $numero;
    }

    // Método: generar XML (simulado)
    public function generarXML()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<comprobante>';
        $xml .= '<numero>' . $this->numero_serie . '</numero>';
        $xml .= '<tipo>' . $this->tipo . '</tipo>';
        $xml .= '<venta_id>' . $this->id_venta . '</venta_id>';
        $xml .= '</comprobante>';
        return $xml;
    }

    // Método: emitir PDF
    public function emitirPDF()
    {
        $this->url_pdf = 'comprobantes/' . $this->numero_serie . '.pdf';
        $this->save();
        return $this->url_pdf;
    }
}