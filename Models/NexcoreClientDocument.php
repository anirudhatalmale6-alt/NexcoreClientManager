<?php

namespace Modules\NexcoreClientManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NexcoreClientDocument extends Model
{
    use SoftDeletes;

    protected $table = 'nexcore_client_documents';

    protected $fillable = [
        'client_id', 'document_type_id', 'document_category', 'title', 'description',
        'file_path', 'file_name', 'file_size', 'file_type',
        'status_id', 'uploaded_by', 'expiry_date', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'file_size'   => 'integer',
        'is_active'   => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(NexcoreClient::class, 'client_id');
    }

    public function documentType()
    {
        return $this->belongsTo(NexcoreSystemDocumentType::class, 'document_type_id');
    }

    public function status()
    {
        return $this->belongsTo(NexcoreSystemReturnStatus::class, 'status_id');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
