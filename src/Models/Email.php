<?php

namespace RickDBCN\FilamentEmail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($record) {
            $folderPath = null;
            if (! empty($record->attachments)) {
                foreach ($record->attachments as $attachment) {
                    $filePath = storage_path('app'.DIRECTORY_SEPARATOR.$attachment['path']);
                    if (empty($folderPath)) {
                        $parts = explode(DIRECTORY_SEPARATOR, $filePath);
                        array_pop($parts);
                        $folderPath = implode(DIRECTORY_SEPARATOR, $parts);
                    }
                    if (! is_dir($filePath) && file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            $savePathRaw = storage_path('app'.DIRECTORY_SEPARATOR.$record->raw_body);
            if (! is_dir($savePathRaw) && file_exists($savePathRaw)) {
                if (empty($folderPath)) {
                    $parts = explode(DIRECTORY_SEPARATOR, $savePathRaw);
                    array_pop($parts);
                    $folderPath = implode(DIRECTORY_SEPARATOR, $parts);
                }
                unlink($savePathRaw);
            }
            if (is_dir($folderPath) && file_exists($folderPath)) {
                rmdir($folderPath);
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
