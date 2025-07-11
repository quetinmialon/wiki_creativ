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
    public function createDocument(array $data)
    {
        $data['created_by'] = Auth::id();
        $data['content'] = $this->convertHtmlToMarkdown($data['content']);
        //clean content to take off potential script tags or other dangerous HTML to prevent XSS attacks
        $data['content'] = $this->sanitizeMarkdown($data['content']);
        $document = Document::create($data);
        if (!empty($data['categories_id'])) {
            $document->categories()->attach($data['categories_id']);
        }
        return $document;
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

    public function getAllCategoriesWithDocuments($limit = 3)
    {
        $user = Auth::user();
        if(!$user){
            return [];
        }
        $roleIds = $user->roles->pluck('id');
        return Category::whereIn('role_id', $roleIds)
                       ->with(['documents' => function ($query) use ($limit): void {
                            $query->where('formated_name','!=',null)->limit($limit)->orderBy('documents.created_at', 'desc');
                        }])
                       ->distinct()
                       ->get();
    }

    public function getEveryDocuments($limit = 3)
    {
        return Category::with(['documents' => function ($query) use ($limit): void {
            $query->where('formated_name', '!=', null)->limit($limit)->orderBy('formated_name', 'asc');
        }])->orderBy('name', 'asc')->get();
    }

    public function getEveryDocumentswithoutPagination()
    {
        return Category::with(['documents' => function ($query): void {
            $query->where('formated_name', '!=', null)->orderBy('formated_name', 'asc');
        }])->orderBy('name', 'asc')->get();
    }

    public function getDocumentsByCategory($categoryId, $perPage = 10)
    {
        return Document::whereHas('categories', function ($query) use ($categoryId): void {
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
                       ->orWhereHas('author', function ($q) use ($query): void {
                           $q->where('name', 'LIKE', "%{$query}%");
                       })
                       ->orWhereHas('categories', function ($q) use ($query): void {
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
    public function getAllDocumentThatAreNormed($perPage = 15)
    {
        return Document::whereNotNull('formated_name')
                      ->orderBy('formated_name', 'asc')
                      ->paginate($perPage);
    }

    public function addNormedNameToDocument(Document $document, ?string $formated_name)
    {
        $document->formated_name = $formated_name;
        $document->save();
    }
}
