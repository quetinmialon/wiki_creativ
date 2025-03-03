<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use League\CommonMark\CommonMarkConverter;
use Exception;

class ValidMarkdown implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $converter = new CommonMarkConverter();
            $html = $converter->convert($value);

            // Vérifie que le Markdown produit du HTML non vide
            if (strip_tags($html) === '') {
                $fail("Le contenu Markdown est invalide ou vide après conversion.");
            }
        } catch (Exception $e) {
            $fail("Erreur lors de la validation du Markdown.");
        }
    }
}
