<?php

namespace App\Utils;

class TextUtils
{
  public static function calculateSpamProbability(string $text): float
  {
    $probability = 0.0;
    $text = strtolower($text);

    // Repeating characters detection
    if (preg_match('/(.)\1{3,}/', $text)) {
      $repeatingCount = strlen(preg_replace('/[^(.)\1{3,}]/', '', $text));
      $probability += min(0.3, $repeatingCount * 0.03);
    }

    // Phishing related keywords
    $phishingTerms = [
      'password',
      'verify',
      'account',
      'login',
      'bank',
      'urgent',
      'security',
      'update required',
      'confirm your',
      'klik disini',
      'verifikasi',
      'dana',
      'rekening',
      'bca',
      'mandiri',
      'gopay',
      'ovo',
      'hadiah',
      'undian',
      'menang',
      'whatsapp',
      'wa',
      'selamat',
      'bonus',
      'gacor'
    ];
    foreach ($phishingTerms as $term) {
      if (str_contains($text, $term)) {
        $probability += 0.15;
      }
    }

    // Suspicious links detection
    $suspiciousPatterns = [
      '/bit\.ly/',
      '/tinyurl/',
      '/goo\.gl/',
      '/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/',
      '/[^\w\-]\.ru\//',
      '/[^\w\-]\.xyz\//',
      '/[^\w\-]\.tk\//',
      '/[^\w\-]\.info\//'
    ];
    foreach ($suspiciousPatterns as $pattern) {
      if (preg_match($pattern, $text)) {
        $probability += 0.25;
      }
    }

    // Mixed character analysis
    if (preg_match('/[A-Za-z][0-9]|[0-9][A-Za-z]/', $text)) {
      $mixedCount = strlen(preg_replace('/[^A-Za-z0-9]/', '', $text));
      $normalCount = strlen(preg_replace('/[^a-z\s]/', '', $text));
      if ($mixedCount > 0 && $normalCount / $mixedCount < 0.6) {
        $probability += 0.2;
      }
    }

    return min(1.0, $probability);
  }
}
