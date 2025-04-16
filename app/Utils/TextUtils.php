<?php

namespace App\Utils;

use App\Models\BlockedWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Polyfill\Intl\Normalizer\Normalizer;

class TextUtils
{
  private static function getBlockedWords(): array
  {
    return Cache::remember('blocked_words', 3600, function () {
      return BlockedWord::all()->mapWithKeys(function ($word) {
        return [$word->word => $word->weight];
      })->toArray();
    });
  }

  public static function calculateSpamProbability(string $text): float
  {
    $probability = 0.0;

    // Normalize text: remove diacritics and convert to lower-case.
    $normalizedText = Normalizer::normalize($text, Normalizer::NFKD);

    if ($text !== $normalizedText) {
      // Big chance of spam if the text has diacritics/special characters.
      $probability += 0.5;
    }

    $normalizedText = preg_replace('/\p{Mn}/u', '', $normalizedText);
    $normalizedText = strtolower($normalizedText);

    $blockedWords = self::getBlockedWords();
    foreach ($blockedWords as $word => $weight) {
      // First try exact match
      if (strpos($normalizedText, $word) !== false) {
        $probability += $weight;
        continue;
      }

      // Try fuzzy matching for words longer than 3 characters
      if (strlen($word) > 3) {
        $wordLen = strlen($word);
        // Check text in chunks similar to word length
        for ($i = 0; $i < strlen($normalizedText) - $wordLen + 1; $i++) {
          $chunk = substr($normalizedText, $i, $wordLen + 2);
          similar_text($word, $chunk, $percent);

          if ($percent > 80) {  // 80% similarity threshold
            $probability += $weight * ($percent / 100);
            break;
          }
        }
      }
    }

    // Repeating characters detection
    // Find sequences where any character repeats 4 or more times.
    if (preg_match_all('/(.)\1{3,}/u', $normalizedText, $matches)) {
      $repeatingCount = 0;
      foreach ($matches[0] as $match) {
        $repeatingCount += strlen($match);
      }
      $probability += min(0.3, $repeatingCount * 0.03);
    }

    // Suspicious links detection
    $suspiciousPatterns = [
      '/bit\.ly/i',
      '/tinyurl/i',
      '/goo\.gl/i',
      '/\b[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\b/',
      '/[^\w\-]\.ru\//i',
      '/[^\w\-]\.xyz\//i',
      '/[^\w\-]\.tk\//i',
      '/[^\w\-]\.info\//i'
    ];
    foreach ($suspiciousPatterns as $pattern) {
      if (preg_match($pattern, $normalizedText)) {
        $probability += 0.25;
      }
    }

    // Mixed character analysis: detect texts with a high mix of numbers/symbols.
    // For this, calculate the ratio of characters in an alphanumeric-only version vs.
    // a version with only letters and whitespace.
    $alnumText   = preg_replace('/[^a-z0-9]/', '', $normalizedText);
    $mixedCount  = strlen($alnumText);
    $alphaSpaceText = preg_replace('/[^a-z\s]/', '', $normalizedText);
    $normalCount = strlen($alphaSpaceText);
    if ($mixedCount > 0 && ($normalCount / $mixedCount) < 0.6) {
      $probability += 0.2;
    }

    return min(1.0, $probability);
  }
}
