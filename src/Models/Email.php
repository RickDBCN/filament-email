<?php

namespace RickDBCN\FilamentEmail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Email
 *
 * @property string $from
 * @property string $to
 * @property string $cc
 * @property string $bcc
 * @property string $subject
 * @property string $text_body
 * @property string $html_body
 * @property string $raw_body
 * @property string $sent_debug_info
 * @property Carbon|null $created_at
 */
class Email extends Model
{
    use HasFactory;
    use Prunable;

    protected $table = 'filament_email_log';

    protected $guarded = [];

    private $defaultSearchFields = [
        'subject',
        'from',
        'to',
    ];

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(Config::get('filament-email.keep_email_for_days')));
    }

    private function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    private function getSearchableFields()
    {
        $fields = Config::get('filament-email.resource.table_search_fields', $this->defaultSearchFields);

        return Arr::where($fields, function ($value, $key) {
            return in_array($value, $this->getTableColumns());
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            foreach ($this->getSearchableFields() as $key => $field) {
                $query->{$key > 0 ? 'orWhere' : 'where'}($field, 'LIKE', "%{$search}%");
            }
        });
    }
}
