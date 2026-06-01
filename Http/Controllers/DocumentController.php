<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\NexcoreClientManager\Models\NexcoreClient;
use Modules\NexcoreClientManager\Models\NexcoreClientDocument;
use Modules\NexcoreClientManager\Models\NexcoreSystemDocumentType;
use Modules\NexcoreClientManager\Models\NexcoreSystemReturnStatus;

class DocumentController extends Controller
{
    public function index($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $documents = NexcoreClientDocument::where('client_id', $clientId)
            ->with(['documentType', 'status'])
            ->orderByDesc('created_at')
            ->get();

        return view('nexcore_client_manager::documents.index', compact('client', 'documents'));
    }

    public function create($clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);
        $documentTypes = NexcoreSystemDocumentType::where('is_active', true)->orderBy('name')->get();
        $statuses = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::documents.form', compact('client', 'documentTypes', 'statuses'));
    }

    public function store(Request $request, $clientId)
    {
        $client = NexcoreClient::findOrFail($clientId);

        $request->validate([
            'document_type_id' => 'required|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'document_file'    => 'required|file|max:20480',
            'expiry_date'      => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $filePath  = null;
        $fileName  = null;
        $fileSize  = null;
        $fileType  = null;

        if ($request->hasFile('document_file')) {
            $file      = $request->file('document_file');
            $fileName  = $file->getClientOriginalName();
            $fileSize  = $file->getSize();
            $fileType  = $file->getMimeType();
            $stored    = time() . '_' . $fileName;
            $uploadDir = base_path('../uploads/documents');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $file->move($uploadDir, $stored);
            $filePath = $stored;
        }

        $docCategory = 'Registrations';
        if ($request->filled('document_group')) {
            $cat = \Modules\CIMSDocManager\Models\DocumentCategory::find($request->input('document_group'));
            if ($cat) {
                $docCategory = $cat->name;
            }
        }

        NexcoreClientDocument::create(array_merge(
            $request->only(['document_type_id', 'title', 'description', 'expiry_date', 'notes']),
            [
                'client_id'          => $clientId,
                'document_category'  => $docCategory,
                'file_path'          => $filePath,
                'file_name'          => $fileName,
                'file_size'          => $fileSize,
                'file_type'          => $fileType,
                'uploaded_by'        => auth()->id(),
                'is_active'          => true,
                'created_by'         => auth()->id(),
                'updated_by'         => auth()->id(),
            ]
        ));

        return redirect()->to(route('nexcore.clients.show.dashboard', $clientId) . '?tab=documents')
            ->with('success', 'Document uploaded successfully.');
    }

    public function edit($clientId, $documentId)
    {
        $client        = NexcoreClient::findOrFail($clientId);
        $document      = NexcoreClientDocument::where('client_id', $clientId)->findOrFail($documentId);
        $documentTypes = NexcoreSystemDocumentType::where('is_active', true)->orderBy('name')->get();
        $statuses      = NexcoreSystemReturnStatus::where('is_active', true)->orderBy('sort_order')->get();

        return view('nexcore_client_manager::documents.form', compact('client', 'document', 'documentTypes', 'statuses'));
    }

    public function update(Request $request, $clientId, $documentId)
    {
        $client   = NexcoreClient::findOrFail($clientId);
        $document = NexcoreClientDocument::where('client_id', $clientId)->findOrFail($documentId);

        $request->validate([
            'document_type_id' => 'required|integer',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'document_file'    => 'nullable|file|max:20480',
            'status_id'        => 'required|integer',
            'expiry_date'      => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $data = array_merge(
            $request->only(['document_type_id', 'title', 'description', 'status_id', 'expiry_date', 'notes']),
            ['updated_by' => auth()->id()]
        );

        if ($request->hasFile('document_file')) {
            // Remove old file
            if ($document->file_path) {
                $oldPath = base_path('../uploads/documents/' . $document->file_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file      = $request->file('document_file');
            $fileName  = $file->getClientOriginalName();
            $stored    = time() . '_' . $fileName;
            $uploadDir = base_path('../uploads/documents');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $file->move($uploadDir, $stored);

            $data['file_path'] = $stored;
            $data['file_name'] = $fileName;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getMimeType();
        }

        $document->update($data);

        return redirect()->route('nexcore.clients.show.documents', $clientId)
            ->with('success', 'Document updated successfully.');
    }

    public function destroy($clientId, $documentId)
    {
        $document = NexcoreClientDocument::where('client_id', $clientId)->findOrFail($documentId);

        // Delete the physical file
        if ($document->file_path) {
            $filePath = base_path('../uploads/documents/' . $document->file_path);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $document->delete();

        return redirect()->route('nexcore.clients.show.documents', $clientId)
            ->with('success', 'Document deleted successfully.');
    }
}
