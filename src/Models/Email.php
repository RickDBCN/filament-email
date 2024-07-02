<?php

namespace RickDBCN\FilamentEmail\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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

    protected $casts = [
        'attachments' => 'json',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(config('filament-email.tenant_model'), 'team_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('teams', function (Builder $query) {
            if (auth()->check() && Filament::getTenant()) {
                $query->whereBelongsTo(auth()->user()?->teams);
            } else {
                $query->whereTeamId(null);
            }
        });
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($record) {
            $storageDisk = config('filament-email.attachments_disk', 'local');
            $folderPath = null;
            if (! empty($record->attachments)) {
                foreach ($record->attachments as $attachment) {
                    $filePath = Storage::disk($storageDisk)->path($attachment['path']);
                    if (empty($folderPath)) {
                        $parts = explode(DIRECTORY_SEPARATOR, $attachment['path']);
                        array_pop($parts);
                        $folderPath = implode(DIRECTORY_SEPARATOR, $parts);
                    }
                    if (! Storage::directoryExists($folderPath) && Storage::disk($storageDisk)->exists($attachment['path'])) {
                        Storage::disk($storageDisk)->delete($attachment['path']);
                    }
                }
            }

            if (! empty($record->raw_body)) {
                if (! Storage::disk($storageDisk)->directoryExists($record->raw_body) && Storage::disk($storageDisk)->exists($record->raw_body)) {
                    if (empty($folderPath)) {
                        $parts = explode(DIRECTORY_SEPARATOR, $record->raw_body);
                        array_pop($parts);
                        $folderPath = implode(DIRECTORY_SEPARATOR, $parts);
                    }
                    Storage::disk($storageDisk)->delete($record->raw_body);
                }
                if (Storage::disk($storageDisk)->directoryExists($folderPath)) {
                    Storage::disk($storageDisk)->deleteDirectory($folderPath);
                }
            }
        });
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDays(config('filament-email.keep_email_for_days', 60)));
    }

    private function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    private function getSearchableFields()
    {
        $columns = $this->getTableColumns();
        $fields = config('filament-email.resource.table_search_fields', $this->defaultSearchFields);

        return Arr::where($fields, function ($value) use ($columns) {
            return in_array($value, $columns);
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
