<?php
use App\Services\DocumentService;
use App\Models\Category;
use App\Models\Document;

test('Convert html to markdown actualy convert html string into markdown string', function (): void {
    //arrange
    $htmlText = '<h1>test balise html</h1>';
    $expectedText = '# test balise html';

    //act
    $documentService = new DocumentService();
    $convertedText = $documentService->convertHtmlToMarkdown($htmlText);

    //assert
    expect($convertedText)->toContain($expectedText);
});

test('Convert mardown to html actualy convert mardown string into html string', function (): void{
    //arrange
    $mardownText = '# test balise html';
    $htmlExpectedText = '<h1>test balise html</h1>';

    //act
    $documentService = new DocumentService();
    $convertedText = $documentService->convertMarkdownToHtml($mardownText);

    //assert
    expect($convertedText)->toContain($htmlExpectedText);
});

test('converts markdown to HTML and strips dangerous HTML', function (): void {
    //arrange
    $service = new DocumentService();
    $markdownWithScript = "Paragraphe\n\n<script>alert('hack');</script>";
    $markdown = "# Titre\n\n**Texte en gras**";

    //act
    $htmlSanitized = $service->sanitizeMarkdown($markdownWithScript);
    $html = $service->sanitizeMarkdown($markdown);
    //expect
    expect($html)->toContain('<h1>Titre</h1>')
                 ->and($html)->toContain('<strong>Texte en gras</strong>');
    expect($htmlSanitized)->not()->toContain('<script>')
                 ->and($htmlSanitized)->toContain('<p>Paragraphe</p>');
});


