<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    /**
     * Check if model has attribute
     */
    public function hasAttribute($key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get table name for model
     */
    public static function getTableName(): string
    {
        return with(new static)->getTable();
    }

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered records
     */
    public function scopeOrdered($query, string $column = 'created_at', string $direction = 'desc')
    {
        return $query->orderBy($column, $direction);
    }

    /**
     * Check if model uses timestamp
     */
    public function usesTimestamps(): bool
    {
        return $this->timestamps;
    }

    /**
     * Get model's fillable attributes
     */
    public function getFillableAttributes(): array
    {
        return $this->fillable;
    }

    /**
     * Get model's guarded attributes
     */
    public function getGuardedAttributes(): array
    {
        return $this->guarded;
    }

    /**
     * Convert value to boolean
     */
    protected function asBoolean($value): bool
    {
        if (is_null($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $value, string $field = 'slug'): string
    {
        $slug = Str::slug($value);
        $count = static::where($field, 'LIKE', $slug.'%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Get cached attributes
     */
    public function getCachedAttributes(): array
    {
        return array_merge(
            $this->attributes,
            $this->relations->toArray()
        );
    }

    /**
     * Check if model has relation
     */
    public function hasRelation(string $relation): bool
    {
        return method_exists($this, $relation);
    }

    /**
     * Get model's table columns
     */
    public function getTableColumns(): array
    {
        return $this->getConnection()
            ->getSchemaBuilder()
            ->getColumnListing($this->getTable());
    }
}
