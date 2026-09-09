<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Trait Translatable
 *
 * Provides automatic translation support via *_translations tables.
 *
 * Usage in model:
 *   use Translatable;
 *
 *   protected $translatables = ['title', 'description'];
 *   protected $translationTable = 'course_translations';
 *   protected $translationForeignKey = 'course_id';
 */
trait Translatable
{
    public static function bootTranslatable(): void
    {
        static::saved(function (Model $model) {
            $model->syncTranslations();
        });
    }

    public function translations(): HasMany
    {
        $class = $this->getTranslationModelClass();
        return $this->hasMany($class, $this->getTranslationForeignKey());
    }

    public function translate(string $locale = null): ?Model
    {
        $locale = $locale ?: app()->getLocale();

        $translation = $this->translations()->where('locale', $locale)->first();

        if (!$translation && $locale !== 'ru') {
            $translation = $this->translations()->where('locale', 'ru')->first();
        }

        return $translation;
    }

    public function getTranslatedAttribute(string $locale = null): ?Model
    {
        return $this->translate($locale);
    }

    public function __get($key)
    {
        if (in_array($key, $this->getTranslatables())) {
            $translation = $this->translate();
            return $translation ? $translation->{$key} : parent::__get($key);
        }

        return parent::__get($key);
    }

    public function syncTranslations(): void
    {
        $translationClass = $this->getTranslationModelClass();
        $foreignKey = $this->getTranslationForeignKey();
        $translatables = $this->getTranslatables();

        $locale = app()->getLocale() ?: 'ru';

        $data = ['locale' => $locale];
        foreach ($translatables as $field) {
            $data[$field] = $this->getAttribute($field);
        }

        $translationClass::updateOrCreate(
            [$foreignKey => $this->getKey(), 'locale' => $locale],
            $data
        );
    }

    protected function getTranslatables(): array
    {
        return $this->translatables ?? [];
    }

    protected function getTranslationForeignKey(): string
    {
        return $this->translationForeignKey ?? (Str::snake(class_basename(static::class)) . '_id');
    }

    protected function getTranslationModelClass(): string
    {
        if (isset($this->translationModel)) {
            return $this->translationModel;
        }

        $modelClass = class_basename(static::class);
        return "App\\Models\\{$modelClass}Translation";
    }
}
