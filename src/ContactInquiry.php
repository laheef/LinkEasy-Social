<?php
namespace App;

/** Shared validation and plain-text serialization. No schema migration needed. */
final class ContactInquiry
{
    public static function settings(): array { return require LES_BASE_PATH . '/config/contact.php'; }
    public static function platforms(): array {
        return array_column(array_filter(config('platforms'), fn ($p) => !empty($p['available'])), 'name');
    }
    public static function validate(array $input): array {
        $settings = self::settings(); $data = []; $errors = [];
        foreach (['name','email','subject','message'] as $key) {
            $data[$key] = is_string($input[$key] ?? null) ? trim($input[$key]) : '';
        }
        if (str_starts_with($data['subject'], 'Managed subscription')) $data['subject'] = 'Managed subscription';
        if (mb_strlen($data['name']) < 2 || mb_strlen($data['name']) > 100 || preg_match('/[\r\n]/', $data['name'])) $errors['name'] = 'Enter your name (2–100 characters).';
        if (strlen($data['email']) > 190 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
        if (!in_array($data['subject'], $settings['subjects'], true)) $errors['subject'] = 'Choose a topic from the list.';
        if (mb_strlen($data['message']) < 10 || mb_strlen($data['message']) > 5000) $errors['message'] = 'Add 10–5,000 characters describing what you need.';
        $quote = in_array($data['subject'], $settings['quote_subjects'], true);
        if ($quote) {
            foreach ($settings['fields'] as $key => $field) {
                $value = is_string($input[$key] ?? null) ? trim($input[$key]) : '';
                $data[$key] = $value;
                if ($field['required'] && $value === '') { $errors[$key] = 'Complete: ' . $field['label'] . '.'; continue; }
                if ($value === '') continue;
                if ($field['type'] === 'select' && !in_array($value, $field['options'], true)) $errors[$key] = 'Choose one of the listed options.';
                elseif ($field['type'] === 'number' && (filter_var($value, FILTER_VALIDATE_INT) === false || (int)$value < 1 || (int)$value > $field['max'])) $errors[$key] = 'Enter a whole number from 1 to 9,999.';
                elseif (in_array($field['type'], ['text','url','tel'], true) && (mb_strlen($value) > $field['max'] || preg_match('/[\r\n]/', $value))) $errors[$key] = 'Use a single line, up to ' . $field['max'] . ' characters.';
                if ($field['type'] === 'url' && (!filter_var($value, FILTER_VALIDATE_URL) || !in_array(parse_url($value, PHP_URL_SCHEME), ['http','https'], true))) $errors[$key] = 'Use a full public URL starting with https:// or http://.';
            }
            foreach (['platforms' => self::platforms(), 'services' => $settings['services']] as $key => $allowed) {
                $selected = is_array($input[$key] ?? null) ? $input[$key] : [];
                $valid = array_values(array_unique(array_filter($selected, fn ($item) => is_string($item) && in_array($item, $allowed, true))));
                $data[$key] = $valid;
                if (!$valid || count($valid) !== count(array_unique(array_filter($selected, 'is_string'))) || count($selected) !== count(array_filter($selected, 'is_string'))) $errors[$key] = $key === 'platforms' ? 'Select at least one listed platform.' : 'Select at least one type of setup help.';
            }
            if ($data['platforms'] && ctype_digit($data['account_count'] ?? '') && (int)$data['account_count'] < count($data['platforms'])) $errors['account_count'] = 'The total must be at least the number of selected platforms.';
        }
        return [$data, $errors, $quote];
    }
    public static function message(array $data, bool $quote): string {
        if (!$quote) return $data['message'];
        $lines = ['SETUP / QUOTE REQUEST', 'Platforms: ' . implode(', ', $data['platforms']), 'Help requested: ' . implode(', ', $data['services'])];
        foreach (self::settings()['fields'] as $key => $field) $lines[] = $field['label'] . ': ' . ($data[$key] !== '' ? $data[$key] : 'Not provided');
        $lines[] = "\nGOALS & REQUIREMENTS\n" . $data['message'];
        return implode("\n", $lines);
    }
}
