<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'categoria_id', 'tipo', 'descricao', 'valor', 'data_lancamento', 'observacao'])]
class Lancamento extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_lancamento' => 'date',
        ];
    }

    protected function valorFormatado(): Attribute
    {
        return Attribute::make(
            get: fn () => 'R$ '.number_format((float) $this->valor, 2, ',', '.'),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
