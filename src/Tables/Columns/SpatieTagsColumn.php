<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

class SpatieTagsColumn extends TagsColumn
{
    protected ?string $type = null;

    public function getTags(): array
    {
        $state = $this->getState();

        if ($state && (! $state instanceof Collection)) {
            return $state;
        }

        $record = $this->getRecord();

        if ($this->queriesRelationships($record)) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        if (! method_exists($record, 'tagsWithType')) {
            return [];
        }

        $type = $this->getType();
        $tags = $record->tagsWithType($type);

        return $tags->pluck('name')->toArray();
    }

    public function type(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->queriesRelationships($query->getModel())) {
            return $query->with(["{$this->getRelationshipName()}.tags"]);
        }

        return $query->with(['tags']);
    }
}
