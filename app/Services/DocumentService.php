<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\CommonMarkConverter;
use League\HTMLToMarkdown\HtmlConverter;

class DocumentService
{
    protected $markdownConverter;
    protected $htmlConverter;

    public function __construct()
    {
        $this->markdownConverter = new CommonMarkConverter();
        $this->htmlConverter = new HtmlConverter(['header_style' => 'atx']);
    }

    /**
     * Convertit du HTML en Markdown avant stockage
     */
    public function convertHtmlToMarkdown(string $html): string
    {
        return $this->htmlConverter->convert($html);
    }

    /**
     * Convertit du Markdown en HTML pour l'affichage
     */
    public function convertMarkdownToHtml(string $markdown): string
    {
        return $this->markdownConverter->convert($markdown);
    }

    /**
     * Nettoie le Markdown avant stockage (évite HTML malveillant)
     */
    public function sanitizeMarkdown(string $markdown): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip', // Supprime le HTML potentiellement dangereux
        ]);
        return $converter->convert($markdown);
    }

    public function getAllCategoriesWithDocuments($limit = 6)
    {
        $user = Auth::user();
        if(!$user){
            return [];
        }

        $roleIds = $user->roles->pluck('id');

        return Category::whereIn('role_id', $roleIds)
                       ->with(['documents' => function ($query) use ($limit) {
                            $query->where('formated_name','!=',null)->limit($limit)->orderBy('documents.created_at', 'desc');
                        }])
                       ->distinct()
                       ->get();
    }

    public function getEveryDocuments($limit = 6)
    {
        return Category::with(['documents' => function ($query) use ($limit) {
            $query->where('formated_name', '!=', null)->limit($limit)->orderBy('formated_name', 'asc');
        }])->orderBy('name', 'asc')->get();
    }

    public function getEveryDocumentswithoutPagination()
    {
        return Category::with(['documents' => function ($query) {
            $query->where('formated_name', '!=', null)->orderBy('formated_name', 'asc');
        }])->orderBy('name', 'asc')->get();
    }

    public function createDocument(array $data)
    {
        $data['created_by'] = Auth::id();

        // Convertit le HTML en Markdown avant stockage
        $data['content'] = $this->convertHtmlToMarkdown($data['content']);

        // Nettoie le Markdown pour éviter les problèmes de sécurité
        $data['content'] = $this->sanitizeMarkdown($data['content']);

        $document = Document::create($data);

        if (!empty($data['categories_id'])) {
            $document->categories()->attach($data['categories_id']);
        }

        return $document;
    }

    public function getDocumentsByCategory($categoryId, $perPage = 6)
    {
        return Document::whereHas('categories', function ($query) use ($categoryId, $perPage) {
                $query->where('formated_name', '!=', null)->where('categories.id', $categoryId);
         })
         ->orderBy('formated_name', 'asc')->paginate($perPage);
    }

    public function findDocument($id)
    {
        return Document::where('id', $id)->first();
    }

    public function updateDocument(Document $document, array $data)
    {
        // Convertit et nettoie le Markdown lors de la mise à jour
        if (isset($data['content'])) {
            $data['content'] = $this->convertHtmlToMarkdown($data['content']);
            $data['content'] = $this->sanitizeMarkdown($data['content']);
        }
        $document->update($data);
        $document->categories()->sync($data['categories_id'] ?? []);

        return $document;
    }

    public function deleteDocument(Document $document)
    {
        return $document->delete();
    }

    public function searchDocuments($query)
    {
        return Document::where('name', 'LIKE', "%{$query}%")
                       ->orWhere('content', 'LIKE', "%{$query}%")
                       ->orWhere('excerpt', 'LIKE', "%{$query}%")
                       ->orWhereHas('author', function ($q) use ($query) {
                           $q->where('name', 'LIKE', "%{$query}%");
                       })
                       ->orWhereHas('categories', function ($q) use ($query) {
                           $q->where('name', 'LIKE', "%{$query}%");
                       })
                       ->orWhere('formated_name', 'LIKE', "%{$query}%")
                       ->groupBy(('documents.id'))
                       ->get();
    }
    public function getAllDocumentThatAreNotNormed()
    {
        return Document::where('formated_name', null)->get();
    }

    public function countDocumentsThatAreNotNormed()
    {
        return Document::where('formated_name', null)->count();
    }
    public function getAllDocumentThatAreNormed()
    {
        return Document::whereNotNull('formated_name')
                      ->orderBy('formated_name', 'asc')
                      ->get();
    }

    public function addNormedNameToDocument(Document $document, ?string $formated_name)
    {
        $document->formated_name = $formated_name;
        $document->save();
    }
}
