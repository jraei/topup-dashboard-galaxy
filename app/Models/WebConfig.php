
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebConfig extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Define allowed color variables
    const ALLOWED_COLORS = [
        'primary_color',
        'primary_hover',
        'secondary_color',
        'secondary_hover',
        'content_bg',
        'footer_bg',
        'header_bg',
        'text_primary',
        'text_secondary',
    ];

    // Default values for settings
    const DEFAULT_VALUES = [
        'judul_web' => 'Game Top-Up Website',
        'meta_title' => 'Game Top-Up | Fast & Secure',
        'meta_description' => 'The best place to buy game credits and top-ups at affordable prices.',
        'meta_keywords' => 'game top-up, mobile legends, free fire, pubg mobile',
        'support_instagram' => 'https://instagram.com/yourgame',
        'support_whatsapp' => '6281234567890',
        'support_email' => 'support@yourgame.com',
        'support_youtube' => 'https://youtube.com/yourgame',
        'support_facebook' => 'https://facebook.com/yourgame',
        'primary_color' => '#6366F1',
        'primary_hover' => '#818CF8',
        'secondary_color' => '#8B5CF6',
        'secondary_hover' => '#A78BFA',
        'content_bg' => '#1F2937',
        'footer_bg' => '#111827',
        'header_bg' => '#111827',
        'text_primary' => '#F9FAFB',
        'text_secondary' => '#E5E7EB',
        'logo_header' => null,
        'logo_footer' => null,
        'logo_favicon' => null,
    ];

    public static function get($key, $default = null)
    {
        $config = self::where('key', $key)->first();

        if (!$config) {
            return $default ?? self::DEFAULT_VALUES[$key] ?? null;
        }

        return $config->value;
    }

    public static function set($key, $value, $description = null, $type = 'text')
    {
        $config = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'type' => $type
            ]
        );

        // Clear the cache when settings are updated
        Cache::forget('web_config');
        Cache::forget('color_scheme');

        return $config;
    }

    /**
     * Get the color scheme for the application
     * 
     * @return array
     */
    public static function getColorScheme()
    {
        return Cache::remember('color_scheme', 86400, function () {
            $colorScheme = [];
            
            foreach (self::ALLOWED_COLORS as $color) {
                $colorScheme[$color] = self::get($color, self::DEFAULT_VALUES[$color] ?? '#000000');
            }
            
            // Format the colors for Tailwind dynamic colors
            return [
                'primary' => [
                    'DEFAULT' => self::get('primary_color', self::DEFAULT_VALUES['primary_color']),
                    'hover' => self::get('primary_hover', self::DEFAULT_VALUES['primary_hover']),
                    'text' => self::get('text_primary', self::DEFAULT_VALUES['text_primary']),
                ],
                'secondary' => [
                    'DEFAULT' => self::get('secondary_color', self::DEFAULT_VALUES['secondary_color']),
                    'hover' => self::get('secondary_hover', self::DEFAULT_VALUES['secondary_hover']),
                    'text' => self::get('text_secondary', self::DEFAULT_VALUES['text_secondary']),
                ],
                'header_background' => self::get('header_bg', self::DEFAULT_VALUES['header_bg']),
                'footer_background' => self::get('footer_bg', self::DEFAULT_VALUES['footer_bg']),
                'content_background' => self::get('content_bg', self::DEFAULT_VALUES['content_bg']),
                'dark' => [
                    'DEFAULT' => '#070c16',
                    'lighter' => '#111927',
                    'card' => '#101826',
                    'sidebar' => '#111822'
                ]
            ];
        });
    }

    /**
     * Update a color value with validation
     * 
     * @param string $key
     * @param string $value
     * @return bool|array
     */
    public static function updateColor($key, $value)
    {
        // Validate the color format (hex, rgb, or rgba)
        if (!self::isValidColor($value)) {
            return ['success' => false, 'message' => 'Invalid color format'];
        }

        // Update the color
        self::set($key, $value, "Color setting: {$key}", 'color');
        
        // Clear color scheme cache
        Cache::forget('color_scheme');
        
        return ['success' => true, 'message' => 'Color updated successfully'];
    }

    // Method to validate colors (hex, rgb, rgba)
    public static function isValidColor($color)
    {
        // Validate Hex color
        $hexPattern = '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/';
        
        // Validate RGB color
        $rgbPattern = '/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/';
        
        // Validate RGBA color
        $rgbaPattern = '/^rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d*(?:\.\d+)?)\s*\)$/';
        
        return (preg_match($hexPattern, $color) || 
                preg_match($rgbPattern, $color) || 
                preg_match($rgbaPattern, $color));
    }

    public function getColorPaletteAttribute()
    {
        $colorPalette = [];

        foreach (self::ALLOWED_COLORS as $color) {
            $colorPalette[$color] = self::get($color, self::DEFAULT_VALUES[$color] ?? '#000000');
        }

        return $colorPalette;
    }

    public function getSocialLinksAttribute()
    {
        return [
            'instagram' => self::get('support_instagram'),
            'whatsapp' => self::get('support_whatsapp'),
            'email' => self::get('support_email'),
            'youtube' => self::get('support_youtube'),
            'facebook' => self::get('support_facebook'),
        ];
    }
}
