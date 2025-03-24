<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\CommonMarkConverter;
use League\HTMLToMarkdown\HtmlConverter;
use App\Models\User;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;

class DocumentService
{
    protected $markdownConverter;
    protected $htmlConverter;

    public function __construct()
    {
        $this->markdownConverter = new CommonMarkConverter();
        $this->htmlConverter = new HtmlConverter();
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

    public function getAllCategoriesWithDocuments()
    {
        $user = Auth::user();

        // Récupérer tous les IDs des rôles de l'utilisateur
        $roleIds = $user->roles->pluck('id');

        // Chercher les catégories qui correspondent à ces rôles
        return Category::whereIn('role_id', $roleIds)
                       ->with('documents')
                       ->get();
    }

    public function getEveryDocuments()
    {
        return Category::with('documents')->get();
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

    public function getDocumentsByCategory($categoryId)
    {
        return Category::findOrFail($categoryId)->documents;
    }

    public function findDocument($id)
    {
        return Document::find($id);
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
}
